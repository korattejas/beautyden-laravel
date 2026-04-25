@extends('admin.layouts.app')
@section('content')


<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="catalogForm" enctype="multipart/form-data">
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
                                        <div class="mb-1">
                                            <img src="{{ asset('uploads/service/' . $service->icon) }}" class="img-fluid rounded shadow-sm" style="max-height: 80px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control filepond" name="icon">
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
                                    <div class="col-md-4 mb-1"><label class="form-label">Duration</label><div class="input-group"><span class="input-group-text"><i data-feather="clock"></i></span><input type="text" name="duration" class="form-control" value="{{ $service->duration }}"></div></div>
                                    <div class="col-md-4 mb-1"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1" {{ $service->status == 1 ? 'selected' : '' }}>Active</option><option value="0" {{ $service->status == 0 ? 'selected' : '' }}>Inactive</option></select></div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Popularity</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular" {{ $service->is_popular ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="is_popular">Is Popular?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Rating</label><input type="number" step="0.1" name="rating" class="form-control" value="{{ $service->rating }}"></div>
                                    <div class="col-6 mb-1"><label class="form-label">Reviews</label><input type="number" name="reviews" class="form-control" value="{{ $service->reviews }}"></div>
                                </div>

                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Banner Media</h4>
                            </div>
                            <div class="card-body pt-2">
                                <div class="d-flex flex-wrap gap-1 mb-1">
                                    @foreach($service->banner_media ?? [] as $key => $media)
                                        <div class="position-relative border rounded p-25 bg-light banner-media-row">
                                            <input type="hidden" name="banner[{{ $key }}][old_file]" value="{{ $media['url'] }}">
                                            <input type="hidden" name="banner[{{ $key }}][old_type]" value="{{ $media['type'] }}">
                                            @if($media['type'] == 'image')
                                                <img src="{{ asset('uploads/service-media/' . $media['url']) }}" class="rounded" style="height: 60px; width: 60px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-dark rounded" style="height: 60px; width: 60px;"><i data-feather="video" class="text-white"></i></div>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-icon btn-danger position-absolute top-0 end-0 m-25 remove-row" style="padding: 2px;"><i data-feather="x" style="width: 12px; height: 12px;"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="file" class="filepond" name="banner_files[]" multiple>
                                <small class="text-muted">Add more images or videos to the banner.</small>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Before & After Results</h4>
                            </div>
                            <div class="card-body pt-2">
                                <div class="d-flex flex-wrap gap-1 mb-1">
                                    @foreach($service->before_after ?? [] as $key => $ba)
                                        @php $img = is_array($ba) ? ($ba['before'] ?? ($ba['after'] ?? null)) : $ba; @endphp
                                        @if($img)
                                            <div class="position-relative border rounded p-25 bg-light ba-row">
                                                <input type="hidden" name="old_ba_images[]" value="{{ $img }}">
                                                <img src="{{ asset('uploads/service-media/' . $img) }}" class="rounded" style="height: 60px; width: 60px; object-fit: cover;">
                                                <button type="button" class="btn btn-sm btn-icon btn-danger position-absolute top-0 end-0 m-25 remove-row" style="padding: 2px;"><i data-feather="x" style="width: 12px; height: 12px;"></i></button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <input type="file" class="filepond" name="ba_images[]" multiple>
                                <small class="text-muted">Add more result images.</small>
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
                                                                    @if($step['image'])
                                                                        <div class="mb-50 text-center border rounded p-25 bg-light position-relative">
                                                                            <img src="{{ asset('uploads/service-content/' . $step['image']) }}" class="rounded" style="max-height: 50px; max-width: 100%;">
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="filepond" name="sections[{{ $idx }}][steps][{{ $sIdx }}][image]">
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
                                                        @if(!empty($section['image']))
                                                            <div class="mb-50 border rounded p-25 bg-light text-center">
                                                                <img src="{{ asset('uploads/service-content/' . $section['image']) }}" class="rounded" style="max-height: 80px; max-width: 100%;">
                                                            </div>
                                                        @endif
                                                        <input type="file" class="filepond" name="sections[{{ $idx }}][image]">
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
                                                                @if($item['image'])
                                                                    <div class="mb-50 text-center border rounded p-25 bg-light">
                                                                        <img src="{{ asset('uploads/service-content/' . $item['image']) }}" class="rounded" style="height: 40px; object-fit: contain;">
                                                                    </div>
                                                                @endif
                                                                <input type="file" class="filepond" name="sections[{{ $idx }}][items][{{ $iIdx }}][image]">
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
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="procedure">
        <input type="hidden" name="sections[INDEX][type]" value="procedure">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Procedure (Carousel)</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Title"><div class="steps-container"></div><button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="sections[INDEX][steps]">+ Add Item</button></div>
    </div>
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="expert">
        <input type="hidden" name="sections[INDEX][type]" value="expert">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo">Expert Profile</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white"><div class="row"><div class="col-md-4"><div class="premium-file-input p-1"><p class="mb-0 small fw-bold">Expert Image</p><input type="file" name="sections[INDEX][image]" onchange="updatePreview(this)"></div><div class="file-preview mt-1" style="display:none"></div></div><div class="col-md-8"><div class="points-container"><div class="input-group mb-1"><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div></div></div></div></div>
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

    $(function() {
        FilePond.registerPlugin(FilePondPluginImagePreview);

        const initPonds = () => {
            $('.filepond:not(.filepond--root)').each(function() {
                FilePond.create(this, {
                    allowMultiple: $(this).prop('multiple'),
                    instantUpload: false,
                    allowProcess: false,
                    storeAsFile: true,
                    labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>'
                });
            });
        };
        initPonds();

        $('.add-section, .add-step, .add-protocol-item').click(function() {
            setTimeout(initPonds, 100);
        });

        $('#catalogForm').on('submit', function(e) {
            e.preventDefault();
            loaderView();
            let formData = new FormData(this);
            axios.post(APP_URL + '/' + form_url, formData)
                .then(res => {
                    notificationToast(res.data.message, 'success');
                    setTimeout(() => window.location.href = APP_URL + '/' + redirect_url, 1000);
                })
                .catch(err => {
                    loaderHide();
                    notificationToast(err.response?.data?.message || 'Something went wrong', 'warning');
                });
        });

        var sectionIndex = {{ count($sections) }};

        $('.select2').select2({ width: '100%' });

        $('.add-section').click(function() {
            var html = $('#templates [data-type="'+$(this).data('type')+'"]').clone();
            $('#sections-container').append(html[0].outerHTML.replace(/INDEX/g, sectionIndex));
            $('#sections-container .select2-dynamic').last().select2({ width: '100%' });
            sectionIndex++; feather.replace();
        });

        $(document).on('click', '.add-step', function() {
            var con = $(this).siblings('.steps-container');
            con.append('<div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="row"><div class="col-4"><input type="file" class="filepond" name="'+$(this).data('prefix')+'['+con.children().length+'][image]"></div><div class="col-8"><input type="text" name="'+$(this).data('prefix')+'['+con.children().length+'][title]" class="form-control mb-1 form-control-sm" placeholder="Title"><textarea name="'+$(this).data('prefix')+'['+con.children().length+'][desc]" class="form-control form-control-sm" rows="2"></textarea></div></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-protocol-item', function() {
            var con = $(this).siblings('.protocol-items-container');
            con.append('<div class="col-6 mb-1"><div class="border rounded p-1 bg-white position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><input type="file" class="filepond" name="'+$(this).data('prefix')+'['+con.children().length+'][image]"><input type="text" name="'+$(this).data('prefix')+'['+con.children().length+'][title]" class="form-control form-control-sm mt-1" placeholder="Protocol Name"></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-point', function() {
            $(this).closest('.points-container').append('<div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="'+$(this).closest('.points-container').find('input').attr('name')+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button></div>');
            feather.replace();
        });

        $(document).on('click', '.remove-row', function() { $(this).closest('.step-card, .banner-media-row, .ba-row, .col-6, .input-group').remove(); });
        $(document).on('click', '.remove-section', function() { $(this).closest('.section-block').remove(); });

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
