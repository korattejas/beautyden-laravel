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
                        <h2 class="content-header-title float-start mb-0">Edit Service Catalog</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.service-master.index') }}">Services</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <form method="POST" id="addEditForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="{{ $service->id }}">
                
                <div class="row">
                    <!-- Sidebar: Basic Details -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header border-bottom"><h4 class="card-title">Basic Details</h4></div>
                            <div class="card-body pt-2">
                                <div class="mb-1">
                                    <label class="form-label">Service Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-select select2" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $service->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-select select2">
                                        @foreach($subcategories as $sub)
                                            <option value="{{ $sub->id }}" {{ $service->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Price</label><input type="number" name="price" class="form-control" value="{{ $service->price }}"></div>
                                    <div class="col-6 mb-1"><label class="form-label">Disc. Price</label><input type="number" name="discount_price" class="form-control" value="{{ $service->discount_price }}"></div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Duration</label>
                                    <input type="text" name="duration" class="form-control" value="{{ $service->duration }}">
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Main Icon</label>
                                    <input type="file" name="icon" class="form-control">
                                    @if($service->icon)
                                        <img src="{{ asset('uploads/service/' . $service->icon) }}" class="mt-1" style="max-width: 80px;">
                                    @endif
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>Inactive</option>
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
                                @php $sections = $service->content_json ?? []; @endphp
                                @foreach($sections as $idx => $section)
                                    @php $type = $section['type']; @endphp
                                    
                                    @if($type == 'overview')
                                        <div class="section-block card mb-2" data-type="overview">
                                            <input type="hidden" name="sections[{{ $idx }}][type]" value="overview">
                                            <div class="card-header border-bottom py-1 d-flex justify-content-between">
                                                <h5 class="mb-0">Overview Essentials</h5>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
                                            </div>
                                            <div class="card-body pt-2">
                                                <select name="sections[{{ $idx }}][essential_ids][]" class="form-select select2" multiple>
                                                    @foreach($essentials as $es)
                                                        <option value="{{ $es->id }}" {{ in_array($es->id, $section['essential_ids'] ?? []) ? 'selected' : '' }}>{{ $es->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @elseif($type == 'ritual' || $type == 'procedure')
                                        <div class="section-block card mb-2" data-type="{{ $type }}">
                                            <input type="hidden" name="sections[{{ $idx }}][type]" value="{{ $type }}">
                                            <div class="card-header border-bottom py-1 d-flex justify-content-between">
                                                <h5 class="mb-0">{{ ucfirst($type) }} Section</h5>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
                                            </div>
                                            <div class="card-body pt-2">
                                                <input type="text" name="sections[{{ $idx }}][title]" class="form-control mb-1" value="{{ $section['title'] ?? '' }}" placeholder="Section Title">
                                                <div class="steps-container">
                                                    @foreach($section['steps'] ?? [] as $sIdx => $step)
                                                        <div class="step-row border p-1 mb-1 position-relative">
                                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-row">×</button>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input type="hidden" name="sections[{{ $idx }}][steps][{{ $sIdx }}][old_image]" value="{{ $step['image'] }}">
                                                                    @if($step['image'])
                                                                        <img src="{{ asset('uploads/service-content/' . $step['image']) }}" class="mb-1 d-block" style="max-width: 100px;">
                                                                    @endif
                                                                    <input type="file" name="sections[{{ $idx }}][steps][{{ $sIdx }}][image]" class="form-control mb-1">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <input type="text" name="sections[{{ $idx }}][steps][{{ $sIdx }}][title]" class="form-control mb-1" value="{{ $step['title'] }}">
                                                                    <textarea name="sections[{{ $idx }}][steps][{{ $sIdx }}][desc]" class="form-control" rows="2">{{ $step['desc'] }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary add-step" data-prefix="sections[{{ $idx }}][steps]">Add Step</button>
                                            </div>
                                        </div>
                                    @elseif($type == 'expert')
                                        <div class="section-block card mb-2" data-type="expert">
                                            <input type="hidden" name="sections[{{ $idx }}][type]" value="expert">
                                            <div class="card-header border-bottom py-1 d-flex justify-content-between">
                                                <h5 class="mb-0">Expert Profile</h5>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
                                            </div>
                                            <div class="card-body pt-2">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="hidden" name="sections[{{ $idx }}][old_image]" value="{{ $section['image'] ?? '' }}">
                                                        @if(isset($section['image']) && $section['image'])
                                                            <img src="{{ asset('uploads/service-content/' . $section['image']) }}" class="mb-1 d-block" style="max-width: 80px;">
                                                        @endif
                                                        <input type="file" name="sections[{{ $idx }}][image]" class="form-control">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="points-container">
                                                            @foreach($section['points'] ?? [] as $p)
                                                                <div class="input-group mb-1"><input type="text" name="sections[{{ $idx }}][points][]" class="form-control" value="{{ $p }}"><button type="button" class="btn btn-outline-danger remove-row">-</button></div>
                                                            @endforeach
                                                            <div class="input-group mb-1"><input type="text" name="sections[{{ $idx }}][points][]" class="form-control"><button type="button" class="btn btn-outline-primary add-point">+</button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($type == 'list')
                                        <div class="section-block card mb-2" data-type="list">
                                            <input type="hidden" name="sections[{{ $idx }}][type]" value="list">
                                            <div class="card-header border-bottom py-1 d-flex justify-content-between">
                                                <h5 class="mb-0">Bullet Points List</h5>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
                                            </div>
                                            <div class="card-body pt-2">
                                                <input type="text" name="sections[{{ $idx }}][title]" class="form-control mb-1" value="{{ $section['title'] ?? '' }}">
                                                <div class="points-container">
                                                    @foreach($section['points'] ?? [] as $p)
                                                        <div class="input-group mb-1"><input type="text" name="sections[{{ $idx }}][points][]" class="form-control" value="{{ $p }}"><button type="button" class="btn btn-outline-danger remove-row">-</button></div>
                                                    @endforeach
                                                    <div class="input-group mb-1"><input type="text" name="sections[{{ $idx }}][points][]" class="form-control"><button type="button" class="btn btn-outline-primary add-point">+</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($type == 'protocol')
                                        <div class="section-block card mb-2" data-type="protocol">
                                            <input type="hidden" name="sections[{{ $idx }}][type]" value="protocol">
                                            <div class="card-header border-bottom py-1 d-flex justify-content-between">
                                                <h5 class="mb-0">Hygiene Protocols</h5>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button>
                                            </div>
                                            <div class="card-body pt-2">
                                                <input type="text" name="sections[{{ $idx }}][title]" class="form-control mb-1" value="{{ $section['title'] ?? '' }}">
                                                <div class="protocol-items-container">
                                                    @foreach($section['items'] ?? [] as $iIdx => $item)
                                                        <div class="protocol-item-row border p-1 mb-1 position-relative">
                                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-row">×</button>
                                                            <div class="row align-items-center">
                                                                <div class="col-md-4">
                                                                    <input type="hidden" name="sections[{{ $idx }}][items][{{ $iIdx }}][old_image]" value="{{ $item['image'] }}">
                                                                    @if($item['image'])
                                                                        <img src="{{ asset('uploads/service-content/' . $item['image']) }}" class="mb-1 d-block" style="max-width: 60px;">
                                                                    @endif
                                                                    <input type="file" name="sections[{{ $idx }}][items][{{ $iIdx }}][image]" class="form-control">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <input type="text" name="sections[{{ $idx }}][items][{{ $iIdx }}][title]" class="form-control" value="{{ $item['title'] }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary add-protocol-item" data-prefix="sections[{{ $idx }}][items]">Add Protocol Item</button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center my-3">
                    <button type="submit" class="btn btn-primary btn-lg px-5">UPDATE CATALOG</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Re-use templates from create or just use JS -->
<div id="templates" style="display: none;">
    <!-- (Same templates as create view) -->
    <div class="section-block card mb-2" data-type="overview">
        <input type="hidden" name="sections[INDEX][type]" value="overview">
        <div class="card-header border-bottom py-1 d-flex justify-content-between"><h5 class="mb-0">Overview Essentials</h5><button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button></div>
        <div class="card-body pt-2"><select name="sections[INDEX][essential_ids][]" class="form-select select2-dynamic" multiple>@foreach($essentials as $es)<option value="{{ $es->id }}">{{ $es->title }}</option>@endforeach</select></div>
    </div>
    <div class="section-block card mb-2" data-type="ritual">
        <input type="hidden" name="sections[INDEX][type]" value="ritual">
        <div class="card-header border-bottom py-1 d-flex justify-content-between"><h5 class="mb-0">Steps Section</h5><button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button></div>
        <div class="card-body pt-2"><input type="text" name="sections[INDEX][title]" class="form-control mb-1" placeholder="Title"><div class="steps-container"></div><button type="button" class="btn btn-sm btn-outline-primary add-step" data-prefix="sections[INDEX][steps]">Add Step</button></div>
    </div>
    <div class="section-block card mb-2" data-type="expert">
        <input type="hidden" name="sections[INDEX][type]" value="expert">
        <div class="card-header border-bottom py-1 d-flex justify-content-between"><h5 class="mb-0">Expert Profile</h5><button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button></div>
        <div class="card-body pt-2"><div class="row"><div class="col-md-4"><input type="file" name="sections[INDEX][image]" class="form-control"></div><div class="col-md-8"><div class="points-container"><div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-primary add-point">+</button></div></div></div></div></div>
    </div>
    <div class="section-block card mb-2" data-type="list">
        <input type="hidden" name="sections[INDEX][type]" value="list">
        <div class="card-header border-bottom py-1 d-flex justify-content-between"><h5 class="mb-0">List Section</h5><button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button></div>
        <div class="card-body pt-2"><input type="text" name="sections[INDEX][title]" class="form-control mb-1" placeholder="Title"><div class="points-container"><div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-primary add-point">+</button></div></div></div>
    </div>
    <div class="section-block card mb-2" data-type="protocol">
        <input type="hidden" name="sections[INDEX][type]" value="protocol">
        <div class="card-header border-bottom py-1 d-flex justify-content-between"><h5 class="mb-0">Hygiene Protocols</h5><button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove</button></div>
        <div class="card-body pt-2"><input type="text" name="sections[INDEX][title]" class="form-control mb-1" placeholder="Title"><div class="protocol-items-container"></div><button type="button" class="btn btn-sm btn-outline-primary add-protocol-item" data-prefix="sections[INDEX][items]">Add Protocol Item</button></div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    var form_url = 'service-master/store';
    var redirect_url = 'service-master';

    $(function() {
        var sectionIndex = {{ count($sections) }};

        $('.select2').select2({ width: '100%' });

        $('.add-section').click(function() {
            var type = $(this).data('type');
            var html = $('#templates [data-type="'+type+'"]').clone();
            var htmlStr = html[0].outerHTML.replace(/INDEX/g, sectionIndex);
            $('#sections-container').append(htmlStr);
            $('#sections-container .select2-dynamic').select2({ width: '100%' });
            sectionIndex++;
        });

        // (Rest of JS same as create.blade.php)
        $(document).on('click', '.add-step', function() {
            var container = $(this).siblings('.steps-container');
            var prefix = $(this).data('prefix');
            var stepIndex = container.children().length;
            var html = '<div class="step-row border p-1 mb-1 position-relative"><button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-row">×</button><div class="row"><div class="col-md-4"><input type="file" name="'+prefix+'['+stepIndex+'][image]" class="form-control mb-1"></div><div class="col-md-8"><input type="text" name="'+prefix+'['+stepIndex+'][title]" class="form-control mb-1" placeholder="Step Title"><textarea name="'+prefix+'['+stepIndex+'][desc]" class="form-control" rows="2" placeholder="Description"></textarea></div></div></div>';
            container.append(html);
        });

        $(document).on('click', '.add-protocol-item', function() {
            var container = $(this).siblings('.protocol-items-container');
            var prefix = $(this).data('prefix');
            var itemIndex = container.children().length;
            var html = '<div class="protocol-item-row border p-1 mb-1 position-relative"><button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-row">×</button><div class="row align-items-center"><div class="col-md-4"><input type="file" name="'+prefix+'['+itemIndex+'][image]" class="form-control"></div><div class="col-md-8"><input type="text" name="'+prefix+'['+itemIndex+'][title]" class="form-control" placeholder="Item Name"></div></div></div>';
            container.append(html);
        });

        $(document).on('click', '.add-point', function() {
            var container = $(this).closest('.points-container');
            var name = container.find('input').attr('name');
            var html = '<div class="input-group mb-1"><input type="text" name="'+name+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row">-</button></div>';
            container.append(html);
        });

        $(document).on('click', '.remove-row', function() { $(this).closest('div').parent().closest('div.position-relative').length ? $(this).closest('div.position-relative').remove() : $(this).closest('div').remove(); });
        $(document).on('click', '.remove-section', function() { $(this).closest('.section-block').remove(); });
        
        $('#category_id').on('change', function() {
            var id = $(this).val();
            $('#sub_category_id').html('<option value="">Loading...</option>');
            if(id) {
                $.get('/service-master/get-subcategories/' + id, function(data) {
                    var html = '<option value="">Select Sub Category</option>';
                    data.forEach(function(item) { html += '<option value="'+item.id+'">'+item.name+'</option>'; });
                    $('#sub_category_id').html(html);
                });
            }
        });
    });
</script>
@endsection
