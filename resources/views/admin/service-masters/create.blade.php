@extends('admin.layouts.app')
@section('content')
<style>
    .builder-card { border: 2px dashed #e2e8f0; background: #f8fafc; transition: all 0.3s ease; }
    .builder-card:hover { border-color: #1a237e; }
    .premium-file-input { 
        position: relative; 
        border: 2px dashed #d1d5db; 
        border-radius: 12px; 
        padding: 15px; 
        text-align: center; 
        background: #fff; 
        cursor: pointer; 
        transition: all 0.3s; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center;
        min-height: 100px;
    }
    .premium-file-input:hover { border-color: #6366f1; background: #f5f3ff; }
    .premium-file-input input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10; }
    .premium-file-input .placeholder-content { transition: all 0.3s; }
    .premium-file-input.has-preview .placeholder-content { display: none; }
    .preview-media { max-width: 100%; max-height: 150px; border-radius: 8px; object-fit: contain; }
    .section-header { background: #f1f5f9; border-bottom: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px 8px 0 0; }
    .form-label { font-weight: 600; color: #334155; margin-bottom: 5px; }
    .step-card { border-left: 4px solid #6366f1 !important; transition: transform 0.2s; }
    .step-card:hover { transform: translateX(5px); }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="addEditForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="0">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-0">Create Premium Catalog</h2>
                        <p class="text-muted mb-0">Build high-quality service pages with dynamic sections.</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.service-master.index') }}" class="btn btn-outline-secondary">Discard</a>
                        <button type="submit" class="btn btn-primary px-3 shadow">Publish Service</button>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Side: Essentials & Media -->
                    <div class="col-xl-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom"><h4 class="card-title"><i data-feather="info" class="me-1"></i>Basic Service Info</h4></div>
                            <div class="card-body pt-2">
                                <div class="mb-2">
                                    <label class="form-label">Service Master Icon (Thumbnail)</label>
                                    <div class="premium-file-input">
                                        <div class="placeholder-content">
                                            <i data-feather="image" class="text-primary mb-1"></i>
                                            <p class="mb-0 fw-bold">Select Icon Image</p>
                                        </div>
                                        <input type="file" name="icon" onchange="updatePreview(this)" required>
                                    </div>
                                    <div class="file-preview mt-1" style="display:none"></div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Service Title</label>
                                    <input type="text" name="name" class="form-control form-control-lg border-primary border-opacity-25" placeholder="e.g. Luxury Fruit Facial" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-select select2" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Sub Category</label>
                                        <select name="sub_category_id" id="sub_category_id" class="form-select select2">
                                            <option value="">Select Sub Category</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Base Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="price" class="form-control"></div></div>
                                    <div class="col-6 mb-1"><label class="form-label">Discounted</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="discount_price" class="form-control"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-1"><label class="form-label">Duration</label><div class="input-group"><span class="input-group-text"><i data-feather="clock"></i></span><input type="text" name="duration" class="form-control" placeholder="30 Min"></div></div>
                                    <div class="col-md-4 mb-1"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Popularity</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular">
                                            <label class="form-check-label fw-bold" for="is_popular">Is Popular?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Rating</label><input type="number" step="0.1" name="rating" class="form-control" placeholder="e.g. 4.5"></div>
                                    <div class="col-6 mb-1"><label class="form-label">Reviews</label><input type="number" name="reviews" class="form-control" placeholder="e.g. 150"></div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Search Keywords / Short Description</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Brief info for meta data..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Banner Media Section -->
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Banner Media (Carousel)</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-banner">+ Add File</button>
                            </div>
                            <div class="card-body pt-2" id="banner-media-container">
                                <div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50">
                                    <div class="premium-file-input">
                                        <div class="placeholder-content">
                                            <i data-feather="upload-cloud" class="text-primary mb-1"></i>
                                            <p class="mb-0 fw-bold">Click to upload Image or Video</p>
                                            <small class="text-muted">Supports MP4, JPG, PNG, WEBP</small>
                                        </div>
                                        <input type="file" name="banner[0][file]" onchange="updatePreview(this)">
                                    </div>
                                    <div class="file-preview mt-1" style="display:none"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Before / After -->
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Before & After Results</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-ba">+ Add Result</button>
                            </div>
                            <div class="card-body pt-2" id="ba-container">
                                <div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative">
                                    <div class="premium-file-input">
                                        <div class="placeholder-content">
                                            <i data-feather="image" class="text-primary mb-1"></i>
                                            <p class="mb-0 fw-bold small">Upload Before & After Image</p>
                                        </div>
                                        <input type="file" name="ba_images[]" onchange="updatePreview(this)">
                                    </div>
                                    <div class="file-preview mt-1" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Content Sections -->
                    <div class="col-xl-7">
                        <div class="card shadow-sm border-0 bg-light bg-opacity-25" style="min-height: 800px;">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                                <h4 class="card-title text-primary"><i data-feather="layout" class="me-1"></i>Dynamic Page Components</h4>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">+ Add Section</button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg">
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="overview"><i data-feather="grid" class="me-50"></i> Overview Essentials</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="ritual"><i data-feather="layers" class="me-50"></i> Ritual / Steps Section</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="procedure"><i data-feather="refresh-cw" class="me-50"></i> Procedure (Carousel)</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="expert"><i data-feather="user-check" class="me-50"></i> Expert Profile</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="protocol"><i data-feather="shield" class="me-50"></i> Hygiene Protocols</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="list"><i data-feather="list" class="me-50"></i> Information List</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2" id="sections-container">
                                <div class="text-center py-5 empty-msg">
                                    <div class="bg-white rounded-circle shadow-sm d-inline-flex p-3 mb-2">
                                        <i data-feather="box" style="width: 60px; height: 60px; color: #cbd5e1;"></i>
                                    </div>
                                    <h4 class="text-secondary fw-bold">Your Catalog is Empty</h4>
                                    <p class="text-muted mx-auto" style="max-width: 300px;">Select from "+ Add Section" to start building your luxury service detail page.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Templates for JS -->
<div id="templates" style="display: none;">
    <!-- Overview -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="overview">
        <input type="hidden" name="sections[INDEX][type]" value="overview">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold underline text-indigo"><i data-feather="grid" class="me-50"></i> Overview Essentials</h5>
            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
        </div>
        <div class="card-body py-1 bg-white">
            <select name="sections[INDEX][essential_ids][]" class="form-select select2-dynamic" multiple>@foreach($essentials as $es)<option value="{{ $es->id }}">{{ $es->title }}</option>@endforeach</select>
        </div>
    </div>

    <!-- Ritual -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="ritual">
        <input type="hidden" name="sections[INDEX][type]" value="ritual">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-indigo"><i data-feather="layers" class="me-50"></i> Ritual / Steps</h5>
            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
        </div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Process Name (e.g. 5-Step Process)">
            <div class="steps-container"></div>
            <button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="sections[INDEX][steps]">+ Add New Step Item</button>
        </div>
    </div>
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="procedure">
        <input type="hidden" name="sections[INDEX][type]" value="procedure">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-indigo"><i data-feather="repeat" class="me-50"></i> Procedure (Carousel)</h5>
            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
        </div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Carousel Title">
            <div class="steps-container"></div>
            <button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="sections[INDEX][steps]">+ Add Carousel Item</button>
        </div>
    </div>

    <!-- Expert -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="expert">
        <input type="hidden" name="sections[INDEX][type]" value="expert">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Expert Profile</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="premium-file-input p-1">
                        <div class="placeholder-content">
                            <p class="mb-0 small fw-bold">Expert Image</p>
                        </div>
                        <input type="file" name="sections[INDEX][image]" onchange="updatePreview(this)">
                    </div>
                    <div class="file-preview mt-1" style="display:none"></div>
                </div>
                <div class="col-md-8">
                    <div class="points-container">
                        <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- List -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="list">
        <input type="hidden" name="sections[INDEX][type]" value="list">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Information List</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="List Heading">
            <div class="points-container">
                <div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
            </div>
        </div>
    </div>

    <!-- Protocol -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="protocol">
        <input type="hidden" name="sections[INDEX][type]" value="protocol">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Hygiene Standards</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Safety Header">
            <div class="protocol-items-container row"></div>
            <button type="button" class="btn btn-sm btn-outline-indigo add-protocol-item w-100" data-prefix="sections[INDEX][items]">+ Add Standard / Icon</button>
        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    var form_url = 'service-master/store';
    var redirect_url = 'service-master';

    function updatePreview(input) {
        var wrapper = $(input).parent();
        var preview = wrapper.siblings('.file-preview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var content = input.files[0].type.includes('video') 
                    ? '<video controls class="preview-media shadow-sm"><source src="'+e.target.result+'"></video>' 
                    : '<img src="'+e.target.result+'" class="preview-media shadow-sm">';
                preview.html(content).fadeIn();
                wrapper.addClass('has-preview');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function() {
        var sectionIndex = 0;
        $('.select2').select2({ width: '100%' });

        var bannerIndex = 1;
        $('.add-banner').click(function() {
            var html = '<div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn">' +
                       '<button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>' +
                       '<div class="premium-file-input"><div class="placeholder-content"><i data-feather="upload-cloud" class="text-primary mb-1"></i><p class="mb-0 fw-bold">Click to upload Media</p></div>' +
                       '<input type="file" name="banner['+bannerIndex+'][file]" onchange="updatePreview(this)"></div>' +
                       '<div class="file-preview mt-1" style="display:none"></div></div>';
            $('#banner-media-container').append(html);
            bannerIndex++;
            feather.replace();
        });

        $('.add-ba').click(function() {
            var html = '<div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn">' +
                       '<button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>' +
                       '<div class="premium-file-input"><div class="placeholder-content"><i data-feather="image" class="text-primary mb-1"></i><p class="mb-0 fw-bold small">Upload B&A Image</p></div>' +
                       '<input type="file" name="ba_images[]" onchange="updatePreview(this)"></div>' +
                       '<div class="file-preview mt-1" style="display:none"></div></div>';
            $('#ba-container').append(html);
            feather.replace();
        });

        $('.add-section').click(function() {
            $('.empty-msg').hide();
            var type = $(this).data('type');
            var html = $('#templates [data-type="'+type+'"]').clone();
            $('#sections-container').append(html[0].outerHTML.replace(/INDEX/g, sectionIndex));
            $('#sections-container .select2-dynamic').last().select2({ width: '100%' });
            sectionIndex++;
            feather.replace();
        });

        $(document).on('click', '.add-step', function() {
            var con = $(this).siblings('.steps-container');
            con.append('<div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="row"><div class="col-4"><div class="premium-file-input p-1 shadow-none" style="height:100%"><div class="placeholder-content"><i data-feather="image"></i></div><input type="file" name="'+$(this).data('prefix')+'['+con.children().length+'][image]" onchange="updatePreview(this)"></div><div class="file-preview mt-1" style="display:none"></div></div><div class="col-8"><input type="text" name="'+$(this).data('prefix')+'['+con.children().length+'][title]" class="form-control mb-1 form-control-sm" placeholder="Title"><textarea name="'+$(this).data('prefix')+'['+con.children().length+'][desc]" class="form-control form-control-sm" rows="2"></textarea></div></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-protocol-item', function() {
            var con = $(this).siblings('.protocol-items-container');
            con.append('<div class="col-6 mb-1"><div class="border rounded p-1 bg-white position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="premium-file-input p-1 shadow-none"><div class="placeholder-content"><i data-feather="image"></i></div><input type="file" name="'+$(this).data('prefix')+'['+con.children().length+'][image]" onchange="updatePreview(this)"></div><div class="file-preview mt-1" style="display:none"></div><input type="text" name="'+$(this).data('prefix')+'['+con.children().length+'][title]" class="form-control form-control-sm mt-1" placeholder="Protocol Name"></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-point', function() {
            var con = $(this).closest('.points-container');
            con.append('<div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="'+con.find('input').attr('name')+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button></div>');
            feather.replace();
        });

        $(document).on('click', '.remove-row', function() { $(this).closest('.step-card, .banner-media-row, .ba-row, .col-6, .input-group').remove(); });
        $(document).on('click', '.remove-section', function() { $(this).closest('.section-block').remove(); if(!$('.section-block').length) $('.empty-msg').show(); });

        $('#category_id').on('change', function() {
            var id = $(this).val();
            $('#sub_category_id').html('<option value="">Loading...</option>');
            if(id) $.get(APP_URL + '/service-master/get-subcategories/' + id, function(data) {
                var html = '<option value="">Select</option>';
                data.forEach(function(i) { html += '<option value="'+i.id+'">'+i.name+'</option>'; });
                $('#sub_category_id').html(html);
            });
        });
    });
</script>
@endsection
