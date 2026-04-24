@extends('admin.layouts.app')
@section('content')
<style>
    .builder-card { border: 2px dashed #e2e8f0; background: #f8fafc; transition: all 0.3s ease; }
    .builder-card:hover { border-color: #1a237e; }
    .premium-file-input { position: relative; border: 2px dashed #d1d5db; border-radius: 12px; padding: 20px; text-align: center; background: #fff; cursor: pointer; transition: all 0.3s; }
    .premium-file-input:hover { border-color: #6366f1; background: #f5f3ff; }
    .premium-file-input input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .section-header { background: #f1f5f9; border-bottom: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px 8px 0 0; }
    .form-label { font-weight: 600; color: #334155; margin-bottom: 5px; }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="addEditForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="{{ $service->id }}">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-0">Edit Premium Catalog</h2>
                        <p class="text-muted mb-0">Updating service details for Luxury Service: #{{ $service->id }}</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.service-master.show', encryptId($service->id)) }}" class="btn btn-outline-primary">View Current</a>
                        <button type="submit" class="btn btn-primary px-3 shadow">Save Changes</button>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Side -->
                    <div class="col-xl-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom"><h4 class="card-title">Basic Service Info</h4></div>
                            <div class="card-body pt-2">
                                <div class="mb-2">
                                    <label class="form-label">Service Master Icon (Thumbnail)</label>
                                    @if($service->icon)
                                        <img src="{{ asset('uploads/service/' . $service->icon) }}" class="img-fluid rounded mb-1 shadow-sm" style="max-height: 80px;">
                                    @endif
                                    <div class="premium-file-input">
                                        <i data-feather="image" class="text-primary mb-1"></i>
                                        <p class="mb-0 fw-bold">Change Icon</p>
                                        <input type="file" name="icon" onchange="updatePreview(this)">
                                    </div>
                                    <div class="file-preview mt-1" style="display:none"></div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Service Title</label>
                                    <input type="text" name="name" class="form-control form-control-lg" value="{{ $service->name }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-select select2" required>
                                            @foreach($categories as $cat) <option value="{{ $cat->id }}" {{ $service->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Sub Category</label>
                                        <select name="sub_category_id" id="sub_category_id" class="form-select select2">
                                            @foreach($subcategories as $sub) <option value="{{ $sub->id }}" {{ $service->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option> @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Base Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="price" class="form-control" value="{{ $service->price }}"></div></div>
                                    <div class="col-6 mb-1"><label class="form-label">Discounted</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="discount_price" class="form-control" value="{{ $service->discount_price }}"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Duration</label><div class="input-group"><span class="input-group-text"><i data-feather="clock"></i></span><input type="text" name="duration" class="form-control" value="{{ $service->duration }}"></div></div>
                                    <div class="col-6 mb-1"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1" {{ $service->status == 1 ? 'selected' : '' }}>Active</option><option value="0" {{ $service->status == 0 ? 'selected' : '' }}>Inactive</option></select></div>
                                </div>
                                <div class="mb-1 text-primary">Rating: {{ $service->rating }} ⭐ ({{ $service->reviews }} Reviews)</div>
                            </div>
                        </div>

                        <!-- Banner Media Section -->
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Banner Media</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-banner">+ Add Media</button>
                            </div>
                            <div class="card-body pt-2" id="banner-media-container">
                                @foreach($service->banner_media ?? [] as $key => $media)
                                    <div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative">
                                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>
                                        <input type="hidden" name="banner[{{ $key }}][old_file]" value="{{ $media['url'] }}">
                                        <input type="hidden" name="banner[{{ $key }}][old_type]" value="{{ $media['type'] }}">
                                        <div class="premium-file-input">
                                            @if($media['type'] == 'image')
                                                <img src="{{ asset('uploads/service-media/' . $media['url']) }}" class="img-fluid rounded mb-1" style="max-height: 100px;">
                                            @else
                                                <div class="mb-1">🎥 Video: {{ $media['url'] }}</div>
                                            @endif
                                            <p class="mb-0 fw-bold small">Change Media File</p>
                                            <input type="file" name="banner[{{ $key }}][file]" onchange="updatePreview(this)">
                                        </div>
                                        <div class="file-preview mt-1" style="display:none"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Before / After -->
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Before & After</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-ba">Add Comparison</button>
                            </div>
                            <div class="card-body pt-2" id="ba-container">
                                @foreach($service->before_after ?? [] as $key => $ba)
                                    <div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative">
                                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="hidden" name="ba_pair[{{ $key }}][old_before]" value="{{ $ba['before'] }}">
                                                @if($ba['before']) <img src="{{ asset('uploads/service-media/' . $ba['before']) }}" class="img-fluid rounded mb-1 shadow-sm" style="height: 60px; width: 100%; object-fit: cover;"> @endif
                                                <div class="premium-file-input p-1" style="border-style: solid; border-width: 1px;"><small class="fw-bold d-block">Before</small><input type="file" name="ba_pair[{{ $key }}][before]"></div>
                                            </div>
                                            <div class="col-6">
                                                <input type="hidden" name="ba_pair[{{ $key }}][old_after]" value="{{ $ba['after'] }}">
                                                @if($ba['after']) <img src="{{ asset('uploads/service-media/' . $ba['after']) }}" class="img-fluid rounded mb-1 shadow-sm" style="height: 60px; width: 100%; object-fit: cover;"> @endif
                                                <div class="premium-file-input p-1" style="border-style: solid; border-width: 1px;"><small class="fw-bold d-block">After</small><input type="file" name="ba_pair[{{ $key }}][after]"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="col-xl-7">
                        <div class="card shadow-sm border-0 bg-light bg-opacity-25" style="min-height: 800px;">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                                <h4 class="card-title text-primary"><i data-feather="layout" class="me-1"></i>Dynamic Page Components</h4>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">+ Add Section</button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg">
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="overview">Overview Essentials</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="ritual">Ritual / Steps Section</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="procedure">Procedure (Carousel)</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="expert">Expert Profile</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="protocol">Hygiene Protocols</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="list">Information List</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2" id="sections-container">
                                @php $sections = $service->content_json ?? []; @endphp
                                @foreach($sections as $idx => $section)
                                    @php $type = $section['type']; @endphp
                                    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="{{ $type }}">
                                        <input type="hidden" name="sections[{{ $idx }}][type]" value="{{ $type }}">
                                        <div class="section-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 fw-bold text-indigo"><i data-feather="box" class="me-50"></i> {{ ucfirst($type) }} Section</h5>
                                            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
                                        </div>
                                        <div class="card-body py-1 bg-white">
                                            @if($type == 'overview')
                                                <select name="sections[{{ $idx }}][essential_ids][]" class="form-select select2" multiple>
                                                    @foreach($essentials as $es) <option value="{{ $es->id }}" {{ in_array($es->id, $section['essential_ids'] ?? []) ? 'selected' : '' }}>{{ $es->title }}</option> @endforeach
                                                </select>
                                            @elseif($type == 'ritual' || $type == 'procedure')
                                                <input type="text" name="sections[{{ $idx }}][title]" class="form-control mb-1 fw-bold" value="{{ $section['title'] ?? '' }}">
                                                <div class="steps-container">
                                                    @foreach($section['steps'] ?? [] as $sIdx => $step)
                                                        <div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative">
                                                            <button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button>
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <input type="hidden" name="sections[{{ $idx }}][steps][{{ $sIdx }}][old_image]" value="{{ $step['image'] }}">
                                                                    @if($step['image']) <img src="{{ asset('uploads/service-content/' . $step['image']) }}" class="img-fluid rounded mb-1 shadow-sm" style="max-height: 80px; width:100%; object-fit: cover;"> @endif
                                                                    <div class="premium-file-input p-1 shadow-none"><input type="file" name="sections[{{ $idx }}][steps][{{ $sIdx }}][image]"></div>
                                                                </div>
                                                                <div class="col-8">
                                                                    <input type="text" name="sections[{{ $idx }}][steps][{{ $sIdx }}][title]" class="form-control mb-1 form-control-sm" value="{{ $step['title'] }}">
                                                                    <textarea name="sections[{{ $idx }}][steps][{{ $sIdx }}][desc]" class="form-control form-control-sm" rows="2">{{ $step['desc'] }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="sections[{{ $idx }}][steps]">+ Add New Step Item</button>
                                            @elseif($type == 'expert')
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <input type="hidden" name="sections[{{ $idx }}][old_image]" value="{{ $section['image'] ?? '' }}">
                                                        @if(isset($section['image']) && $section['image']) <img src="{{ asset('uploads/service-content/' . $section['image']) }}" class="img-fluid rounded mb-1 shadow-sm" style="max-height: 100px; width:100%; object-fit: cover;"> @endif
                                                        <div class="premium-file-input p-1"><input type="file" name="sections[{{ $idx }}][image]"></div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="points-container">
                                                            @foreach($section['points'] ?? [] as $p)
                                                                <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="sections[{{ $idx }}][points][]" class="form-control" value="{{ $p }}"><button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button></div>
                                                            @endforeach
                                                            <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="sections[{{ $idx }}][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($type == 'list')
                                                <input type="text" name="sections[{{ $idx }}][title]" class="form-control mb-1 fw-bold" value="{{ $section['title'] ?? '' }}">
                                                <div class="points-container">
                                                    @foreach($section['points'] ?? [] as $p) <div class="input-group mb-1"><input type="text" name="sections[{{ $idx }}][points][]" class="form-control" value="{{ $p }}"><button type="button" class="btn btn-outline-danger remove-row">-</button></div> @endforeach
                                                    <div class="input-group mb-1"><input type="text" name="sections[{{ $idx }}][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
                                                </div>
                                            @elseif($type == 'protocol')
                                                <input type="text" name="sections[{{ $idx }}][title]" class="form-control mb-1 fw-bold" value="{{ $section['title'] ?? '' }}">
                                                <div class="protocol-items-container row">
                                                    @foreach($section['items'] ?? [] as $iIdx => $item)
                                                        <div class="col-6 mb-1">
                                                            <div class="border rounded p-1 bg-white position-relative">
                                                                <button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button>
                                                                <input type="hidden" name="sections[{{ $idx }}][items][{{ $iIdx }}][old_image]" value="{{ $item['image'] }}">
                                                                @if($item['image']) <img src="{{ asset('uploads/service-content/' . $item['image']) }}" class="img-fluid rounded mb-1" style="height: 50px; width:100%; object-fit: contain;"> @endif
                                                                <div class="premium-file-input p-1 shadow-none"><input type="file" name="sections[{{ $idx }}][items][{{ $iIdx }}][image]"></div>
                                                                <input type="text" name="sections[{{ $idx }}][items][{{ $iIdx }}][title]" class="form-control form-control-sm mt-1" value="{{ $item['title'] }}">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-indigo add-protocol-item w-100" data-prefix="sections[{{ $idx }}][items]">+ Add Standard / Icon</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Templates (Same as Create) -->
<div id="templates" style="display: none;">
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="overview">
        <input type="hidden" name="sections[INDEX][type]" value="overview">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Overview Essentials</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><select name="sections[INDEX][essential_ids][]" class="form-select select2-dynamic" multiple>@foreach($essentials as $es)<option value="{{ $es->id }}">{{ $es->title }}</option>@endforeach</select></div>
    </div>
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="ritual">
        <input type="hidden" name="sections[INDEX][type]" value="ritual">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Ritual / Steps</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Title"><div class="steps-container"></div><button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="sections[INDEX][steps]">+ Add Step</button></div>
    </div>
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="expert">
        <input type="hidden" name="sections[INDEX][type]" value="expert">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Expert Profile</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><div class="row"><div class="col-md-4"><div class="premium-file-input p-1"><input type="file" name="sections[INDEX][image]"></div></div><div class="col-md-8"><div class="points-container"><div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div></div></div></div></div>
    </div>
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="list">
        <input type="hidden" name="sections[INDEX][type]" value="list">
        <div class="card-header border-bottom py-1 d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">List Section</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold"><div class="points-container"><div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div></div></div>
    </div>
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="protocol">
        <input type="hidden" name="sections[INDEX][type]" value="protocol">
        <div class="section-header border-bottom py-1 d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Hygiene</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold"><div class="protocol-items-container row"></div><button type="button" class="btn btn-sm btn-outline-indigo add-protocol-item w-100" data-prefix="sections[INDEX][items]">+ Add Item</button></div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    var form_url = 'service-master/store';
    var redirect_url = 'service-master';

    function updatePreview(input) {
        var preview = $(input).parent().siblings('.file-preview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var content = input.files[0].type.includes('video') ? '<video controls class="img-fluid rounded mt-1 shadow-sm" style="max-height:150px;"><source src="'+e.target.result+'"></video>' : '<img src="'+e.target.result+'" class="img-fluid rounded mt-1 shadow-sm" style="max-height:150px;">';
                preview.html(content).fadeIn();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function() {
        var sectionIndex = {{ count($sections) }};
        var bannerIndex = {{ count($service->banner_media ?? []) }};
        var baIndex = {{ count($service->before_after ?? []) }};

        $('.select2').select2({ width: '100%' });

        $('.add-banner').click(function() {
            $('#banner-media-container').append('<div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="premium-file-input"><i data-feather="upload-cloud" class="text-primary mb-1"></i><p class="mb-0 fw-bold">Click to upload Media</p><input type="file" name="banner['+bannerIndex+'][file]" onchange="updatePreview(this)"></div><div class="file-preview mt-1" style="display:none"></div></div>');
            bannerIndex++; feather.replace();
        });

        $('.add-ba').click(function() {
            $('#ba-container').append('<div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="row g-1"><div class="col-6"><div class="premium-file-input p-1" style="border-style: solid; border-width: 1px;"><small class="fw-bold d-block">Before</small><input type="file" name="ba_pair['+baIndex+'][before]"></div></div><div class="col-6"><div class="premium-file-input p-1" style="border-style: solid; border-width: 1px;"><small class="fw-bold d-block">After</small><input type="file" name="ba_pair['+baIndex+'][after]"></div></div></div></div>');
            baIndex++; feather.replace();
        });

        $('.add-section').click(function() {
            var html = $('#templates [data-type="'+$(this).data('type')+'"]').clone();
            $('#sections-container').append(html[0].outerHTML.replace(/INDEX/g, sectionIndex));
            $('#sections-container .select2-dynamic').last().select2({ width: '100%' });
            sectionIndex++; feather.replace();
        });

        $(document).on('click', '.add-step', function() {
            var con = $(this).siblings('.steps-container');
            con.append('<div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="row"><div class="col-4"><div class="premium-file-input p-1 shadow-none" style="height:100%"><i data-feather="image"></i><input type="file" name="'+$(this).data('prefix')+'['+con.children().length+'][image]"></div></div><div class="col-8"><input type="text" name="'+$(this).data('prefix')+'['+con.children().length+'][title]" class="form-control mb-1 form-control-sm" placeholder="Title"><textarea name="'+$(this).data('prefix')+'['+con.children().length+'][desc]" class="form-control form-control-sm" rows="2"></textarea></div></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-protocol-item', function() {
            var con = $(this).siblings('.protocol-items-container');
            con.append('<div class="col-6 mb-1"><div class="border rounded p-1 bg-white position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="premium-file-input p-1 shadow-none"><input type="file" name="'+$(this).data('prefix')+'['+con.children().length+'][image]"></div><input type="text" name="'+$(this).data('prefix')+'['+con.children().length+'][title]" class="form-control form-control-sm mt-1" placeholder="Protocol Name"></div></div>');
        });

        $(document).on('click', '.add-point', function() {
            $(this).closest('.points-container').append('<div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="'+$(this).closest('.points-container').find('input').attr('name')+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button></div>');
            feather.replace();
        });

        $(document).on('click', '.remove-row', function() { $(this).closest('div').parent().closest('div').remove(); });
        $(document).on('click', '.remove-section', function() { $(this).closest('.section-block').remove(); });
    });
</script>
@endsection
