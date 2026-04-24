@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Add Service Catalog</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.service-master.index') }}">Services</a></li>
                                <li class="breadcrumb-item active">Add New</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <form method="POST" id="addEditForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="0">
                
                <div class="row">
                    <!-- Sidebar: Basic Details -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header border-bottom"><h4 class="card-title">Basic Details</h4></div>
                            <div class="card-body pt-2">
                                <div class="mb-1">
                                    <label class="form-label">Service Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Service Name" required>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-select select2" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-select select2">
                                        <option value="">Select Sub Category</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Price</label><input type="number" name="price" class="form-control"></div>
                                    <div class="col-6 mb-1"><label class="form-label">Disc. Price</label><input type="number" name="discount_price" class="form-control"></div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Duration</label>
                                    <input type="text" name="duration" class="form-control" placeholder="e.g. 30 Min">
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Main Icon</label>
                                    <input type="file" name="icon" class="form-control">
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Builder: Dynamic Sections -->
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Content Sections Builder</h4>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        + Add Section
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="overview">Overview Essentials</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="ritual">Ritual / Steps Section</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="procedure">Procedure (Carousel)</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="expert">Expert Profile</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="protocol">Hygiene Protocols</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="list">Bullet Points List</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" id="sections-container">
                                <!-- Sections will be appended here -->
                                <div class="text-center py-5 empty-msg">
                                    <i data-feather="plus-circle" style="width: 50px; height: 50px; opacity: 0.1;"></i>
                                    <p class="text-muted mt-1">No sections added yet. Click "Add Section" to begin.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center my-3">
                    <button type="submit" class="btn btn-primary btn-lg px-5">SAVE CATALOG</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Templates for JS -->
<div id="templates" style="display: none;">
    <!-- Overview Template -->
    <div class="section-block card mb-2" data-type="overview">
        <input type="hidden" name="sections[INDEX][type]" value="overview">
        <div class="card-header border-bottom py-1 d-flex justify-content-between">
            <h5 class="mb-0">Overview Essentials</h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
        </div>
        <div class="card-body pt-2">
            <label class="form-label">Pick Essentials</label>
            <select name="sections[INDEX][essential_ids][]" class="form-select select2-dynamic" multiple>
                @foreach($essentials as $es)
                    <option value="{{ $es->id }}">{{ $es->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Ritual/Steps Template -->
    <div class="section-block card mb-2" data-type="ritual">
        <input type="hidden" name="sections[INDEX][type]" value="ritual">
        <div class="card-header border-bottom py-1 d-flex justify-content-between">
            <h5 class="mb-0">Ritual / Steps Section</h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
        </div>
        <div class="card-body pt-2">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1" placeholder="Section Title (e.g. 3-Step Ritual)">
            <div class="steps-container"></div>
            <button type="button" class="btn btn-sm btn-outline-primary add-step" data-prefix="sections[INDEX][steps]">Add Step</button>
        </div>
    </div>

    <!-- Expert Template -->
    <div class="section-block card mb-2" data-type="expert">
        <input type="hidden" name="sections[INDEX][type]" value="expert">
        <div class="card-header border-bottom py-1 d-flex justify-content-between">
            <h5 class="mb-0">Expert Profile</h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
        </div>
        <div class="card-body pt-2">
            <div class="row">
                <div class="col-md-4"><label class="form-label">Expert Photo</label><input type="file" name="sections[INDEX][image]" class="form-control"></div>
                <div class="col-md-8">
                    <label class="form-label">Qualifications / Points</label>
                    <div class="points-container">
                        <div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-primary add-point">+</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- List Template -->
    <div class="section-block card mb-2" data-type="list">
        <input type="hidden" name="sections[INDEX][type]" value="list">
        <div class="card-header border-bottom py-1 d-flex justify-content-between">
            <h5 class="mb-0">Bullet Points List (Precautions, etc.)</h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
        </div>
        <div class="card-body pt-2">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1" placeholder="List Title (e.g. Things To Know)">
            <div class="points-container">
                <div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-primary add-point">+</button></div>
            </div>
        </div>
    </div>

    <!-- Protocol Template -->
    <div class="section-block card mb-2" data-type="protocol">
        <input type="hidden" name="sections[INDEX][type]" value="protocol">
        <div class="card-header border-bottom py-1 d-flex justify-content-between">
            <h5 class="mb-0">Hygiene Protocols</h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
        </div>
        <div class="card-body pt-2">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1" placeholder="Section Title">
            <div class="protocol-items-container"></div>
            <button type="button" class="btn btn-sm btn-outline-primary add-protocol-item" data-prefix="sections[INDEX][items]">Add Protocol Item</button>
        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    var form_url = 'service-master/store';
    var redirect_url = 'service-master';

    $(function() {
        var sectionIndex = 0;

        $('.select2').select2();

        // Add Section
        $('.add-section').click(function() {
            $('.empty-msg').hide();
            var type = $(this).data('type');
            var html = $('#templates [data-type="'+type+'"]').clone();
            
            // Replace INDEX with current sectionIndex
            var htmlStr = html[0].outerHTML.replace(/INDEX/g, sectionIndex);
            $('#sections-container').append(htmlStr);
            
            // Re-init select2 for dynamic blocks
            $('#sections-container .select2-dynamic').select2({ width: '100%' });
            
            sectionIndex++;
            feather.replace();
        });

        // Add Step (within ritual/procedure)
        $(document).on('click', '.add-step', function() {
            var container = $(this).siblings('.steps-container');
            var prefix = $(this).data('prefix');
            var stepIndex = container.children().length;
            var html = '<div class="step-row border p-1 mb-1 position-relative">' +
                       '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-row">×</button>' +
                       '<div class="row">' +
                       '<div class="col-md-4"><input type="file" name="'+prefix+'['+stepIndex+'][image]" class="form-control mb-1"></div>' +
                       '<div class="col-md-8"><input type="text" name="'+prefix+'['+stepIndex+'][title]" class="form-control mb-1" placeholder="Step Title">' +
                       '<textarea name="'+prefix+'['+stepIndex+'][desc]" class="form-control" rows="2" placeholder="Step Description"></textarea></div>' +
                       '</div></div>';
            container.append(html);
        });

        // Add Protocol Item
        $(document).on('click', '.add-protocol-item', function() {
            var container = $(this).siblings('.protocol-items-container');
            var prefix = $(this).data('prefix');
            var itemIndex = container.children().length;
            var html = '<div class="protocol-item-row border p-1 mb-1 position-relative">' +
                       '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-row">×</button>' +
                       '<div class="row align-items-center">' +
                       '<div class="col-md-4"><input type="file" name="'+prefix+'['+itemIndex+'][image]" class="form-control"></div>' +
                       '<div class="col-md-8"><input type="text" name="'+prefix+'['+itemIndex+'][title]" class="form-control" placeholder="Item Name"></div>' +
                       '</div></div>';
            container.append(html);
        });

        // Add Point
        $(document).on('click', '.add-point', function() {
            var container = $(this).closest('.points-container');
            var name = container.find('input').attr('name');
            var html = '<div class="input-group mb-1"><input type="text" name="'+name+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row">-</button></div>';
            container.append(html);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('div').remove();
        });

        $(document).on('click', '.remove-section', function() {
            $(this).closest('.section-block').remove();
            if($('#sections-container .section-block').length == 0) $('.empty-msg').show();
        });

        // Subcategories
        $('#category_id').on('change', function() {
            var id = $(this).val();
            $('#sub_category_id').html('<option value="">Loading...</option>');
            if(id) {
                $.get('/service-master/get-subcategories/' + id, function(data) {
                    var html = '<option value="">Select Sub Category</option>';
                    data.forEach(function(item) {
                        html += '<option value="'+item.id+'">'+item.name+'</option>';
                    });
                    $('#sub_category_id').html(html);
                });
            }
        });
    });
</script>
@endsection
