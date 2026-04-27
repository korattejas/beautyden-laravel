@extends('admin.layouts.app')
@section('content')
<style>
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
        min-height: 80px;
    }
    .section-block {
        padding: 12px;
    }
    .premium-file-input:hover { border-color: #6366f1; background: #f5f3ff; }
    .premium-file-input input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 5; }
    .premium-file-input.has-preview { border-style: solid; border-color: #e2e8f0; background: #f8fafc; }
    .preview-container { position: relative; margin-top: 10px; }
    .preview-media { max-width: 100%; max-height: 120px; border-radius: 8px; border: 1px solid #ddd; }
    .preview-actions { position: absolute; top: 5px; right: 5px; display: flex; gap: 5px; z-index: 10; }
    .action-btn { background: rgba(255,255,255,0.9); border: none; border-radius: 4px; padding: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; }
    .action-btn:hover { background: #fff; transform: scale(1.1); }
</style>


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
                                <div class="mb-1">
                                    <label class="form-label">Recommended Skin Type</label>
                                    <select name="skin_type" class="form-select select2">
                                        <option value="All Skin Types" {{ $service->skin_type == 'All Skin Types' ? 'selected' : '' }}>All Skin Types</option>
                                        <option value="Normal Skin" {{ $service->skin_type == 'Normal Skin' ? 'selected' : '' }}>Normal Skin</option>
                                        <option value="Dry Skin" {{ $service->skin_type == 'Dry Skin' ? 'selected' : '' }}>Dry Skin</option>
                                        <option value="Normal To Dry Skin" {{ $service->skin_type == 'Normal To Dry Skin' ? 'selected' : '' }}>Normal To Dry Skin</option>
                                        <option value="Oily Skin" {{ $service->skin_type == 'Oily Skin' ? 'selected' : '' }}>Oily Skin</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Global Default Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="price" class="form-control" value="{{ $service->price }}"></div></div>
                                    <div class="col-6 mb-1"><label class="form-label">Global Discounted Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="discount_price" class="form-control" value="{{ $service->discount_price }}"></div></div>
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
                                <button type="button" class="btn btn-sm btn-outline-primary add-banner">+ Add Media</button>
                            </div>
                            <div class="card-body pt-2" id="banner-media-container">
                                @foreach($service->banner_media ?? [] as $key => $media)
                                    <div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative">
                                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>
                                        <input type="hidden" name="banner[{{ $key }}][old_file]" value="{{ $media['url'] }}">
                                        <input type="hidden" name="banner[{{ $key }}][old_type]" value="{{ $media['type'] }}">
                                        <div class="premium-file-input has-preview">
                                            <div class="placeholder-content" style="display:none">
                                                <i data-feather="upload-cloud" class="text-primary mb-1"></i>
                                                <p class="mb-0 fw-bold small">Change Media</p>
                                            </div>
                                            <input type="file" name="banner[{{ $key }}][file]" onchange="handlePreview(this)">
                                        </div>
                                        <div class="preview-container">
                                            <div class="preview-actions">
                                                <button type="button" class="action-btn text-primary view-full" data-url="{{ asset('uploads/service-media/' . $media['url']) }}"><i data-feather="maximize" style="width:14px"></i></button>
                                            </div>
                                            @if($media['type'] == 'image')
                                                <img src="{{ asset('uploads/service-media/' . $media['url']) }}" class="preview-media">
                                            @else
                                                <div class="mb-1">🎥 Video: {{ $media['url'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Before & After Results</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-ba">+ Add Result</button>
                            </div>
                            <div class="card-body pt-2" id="ba-container">
                                @foreach($service->before_after ?? [] as $key => $ba)
                                    @php $img = is_array($ba) ? ($ba['before'] ?? ($ba['after'] ?? null)) : $ba; @endphp
                                    <div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative">
                                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>
                                        <input type="hidden" name="old_ba_images[]" value="{{ $img }}">
                                        <div class="premium-file-input has-preview">
                                            <div class="placeholder-content" style="display:none"><i data-feather="image"></i></div>
                                            <input type="file" name="ba_images[]" onchange="handlePreview(this)">
                                        </div>
                                        <div class="preview-container">
                                            <div class="preview-actions">
                                                <button type="button" class="action-btn text-primary view-full" data-url="{{ asset('uploads/service-media/' . $img) }}"><i data-feather="maximize" style="width:14px"></i></button>
                                            </div>
                                            <img src="{{ asset('uploads/service-media/' . $img) }}" class="preview-media">
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
                                                                    <div class="premium-file-input {{ $step['image'] ? 'has-preview' : '' }}" style="min-height:60px">
                                                                        <div class="placeholder-content" style="{{ $step['image'] ? 'display:none' : '' }}"><p class="mb-0 x-small fw-bold">Img</p></div>
                                                                        <input type="file" name="sections[{{ $idx }}][steps][{{ $sIdx }}][image]" onchange="handlePreview(this)">
                                                                    </div>
                                                                    <div class="preview-container" style="{{ $step['image'] ? 'display:block' : '' }}">
                                                                        @if($step['image'])
                                                                            <div class="preview-actions">
                                                                                <button type="button" class="action-btn text-primary view-full" data-url="{{ asset('uploads/service-content/' . $step['image']) }}"><i data-feather="maximize" style="width:14px"></i></button>
                                                                            </div>
                                                                            <img src="{{ asset('uploads/service-content/' . $step['image']) }}" class="preview-media">
                                                                        @endif
                                                                    </div>
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
                                                        <div class="premium-file-input {{ !empty($section['image']) ? 'has-preview' : '' }}">
                                                            <div class="placeholder-content" style="{{ !empty($section['image']) ? 'display:none' : '' }}"><p class="mb-0 small fw-bold">Expert</p></div>
                                                            <input type="file" name="sections[{{ $idx }}][image]" onchange="handlePreview(this)">
                                                        </div>
                                                        <div class="preview-container" style="{{ !empty($section['image']) ? 'display:block' : '' }}">
                                                            @if(!empty($section['image']))
                                                                <div class="preview-actions">
                                                                    <button type="button" class="action-btn text-primary view-full" data-url="{{ asset('uploads/service-content/' . $section['image']) }}"><i data-feather="maximize" style="width:14px"></i></button>
                                                                </div>
                                                                <img src="{{ asset('uploads/service-content/' . $section['image']) }}" class="preview-media">
                                                            @endif
                                                        </div>
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
                                                                <div class="premium-file-input {{ $item['image'] ? 'has-preview' : '' }}" style="min-height:50px">
                                                                    <div class="placeholder-content" style="{{ $item['image'] ? 'display:none' : '' }}"><i data-feather="image" style="width:16px"></i></div>
                                                                    <input type="file" name="sections[{{ $idx }}][items][{{ $iIdx }}][image]" onchange="handlePreview(this)">
                                                                </div>
                                                                <div class="preview-container" style="{{ $item['image'] ? 'display:block' : '' }}">
                                                                    @if($item['image'])
                                                                        <div class="preview-actions">
                                                                            <button type="button" class="action-btn text-primary view-full" data-url="{{ asset('uploads/service-content/' . $item['image']) }}"><i data-feather="maximize" style="width:14px"></i></button>
                                                                        </div>
                                                                        <img src="{{ asset('uploads/service-content/' . $item['image']) }}" class="preview-media" style="height:40px; object-fit:contain">
                                                                    @endif
                                                                </div>
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
        <div class="card-body py-1 bg-white">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="premium-file-input">
                        <div class="placeholder-content">
                            <p class="mb-0 small fw-bold">Expert Image</p>
                        </div>
                        <input type="file" name="sections[INDEX][image]" onchange="handlePreview(this)">
                    </div>
                    <div class="preview-container"></div>
                </div>
                <div class="col-md-8">
                    <div class="points-container">
                        <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    function handlePreview(input) {
        const file = input.files[0];
        const container = $(input).closest('.premium-file-input').parent().find('.preview-container');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let html = '<div class="preview-actions">';
                html += '<button type="button" class="action-btn text-primary view-full" data-url="'+e.target.result+'"><i data-feather="maximize" style="width:14px"></i></button>';
                html += '<button type="button" class="action-btn text-danger clear-input"><i data-feather="trash-2" style="width:14px"></i></button>';
                html += '</div>';
                
                if (file.type.startsWith('video/')) {
                    html += '<video src="'+e.target.result+'" class="preview-media" controls></video>';
                } else {
                    html += '<img src="'+e.target.result+'" class="preview-media">';
                }
                
                container.html(html).fadeIn();
                $(input).closest('.premium-file-input').addClass('has-preview').find('.placeholder-content').hide();
                feather.replace();
            }
            reader.readAsDataURL(file);
        }
    }

    window.updatePreview = function(input) { handlePreview(input); };

    $(function() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.create($('.filepond')[0], {
            allowMultiple: false,
            instantUpload: false,
            allowProcess: false,
            storeAsFile: true,
            labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>'
        });

        $(document).on('click', '.clear-input', function() {
            const row = $(this).closest('.preview-container').siblings('.premium-file-input');
            row.find('input[type="file"]').val('');
            row.removeClass('has-preview').find('.placeholder-content').show();
            $(this).closest('.preview-container').fadeOut().empty();
        });

        $(document).on('click', '.view-full', function() {
            window.open($(this).data('url'), '_blank');
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
        var bannerIndex = {{ count($service->banner_media ?? []) }};

        $('.select2').select2({ width: '100%' });

        $('.add-banner').click(function() {
            $('#banner-media-container').append('<div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="premium-file-input"><div class="placeholder-content"><i data-feather="upload-cloud" class="text-primary mb-1"></i><p class="mb-0 fw-bold small">Upload File</p></div><input type="file" name="banner['+bannerIndex+'][file]" onchange="handlePreview(this)"></div><div class="preview-container"></div></div>');
            bannerIndex++; feather.replace();
        });

        $('.add-ba').click(function() {
            $('#ba-container').append('<div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="premium-file-input"><div class="placeholder-content"><i data-feather="image" class="text-primary mb-1"></i><p class="mb-0 fw-bold small">Upload Image</p></div><input type="file" name="ba_images[]" onchange="handlePreview(this)"></div><div class="preview-container"></div></div>');
            feather.replace();
        });

        $('.add-section').click(function() {
            var html = $('#templates [data-type="'+$(this).data('type')+'"]').clone();
            $('#sections-container').append(html[0].outerHTML.replace(/INDEX/g, sectionIndex));
            $('#sections-container .select2-dynamic').last().select2({ width: '100%' });
            sectionIndex++; feather.replace();
        });

        $(document).on('click', '.add-step', function() {
            var con = $(this).siblings('.steps-container');
            var idx = con.children().length;
            con.append('<div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="row"><div class="col-4"><div class="premium-file-input p-1" style="min-height:60px"><div class="placeholder-content"><p class="mb-0 x-small fw-bold">Img</p></div><input type="file" name="'+$(this).data('prefix')+'['+idx+'][image]" onchange="handlePreview(this)"></div><div class="preview-container"></div></div><div class="col-8"><input type="text" name="'+$(this).data('prefix')+'['+idx+'][title]" class="form-control mb-1 form-control-sm" placeholder="Title"><textarea name="'+$(this).data('prefix')+'['+idx+'][desc]" class="form-control form-control-sm" rows="2"></textarea></div></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-protocol-item', function() {
            var con = $(this).siblings('.protocol-items-container');
            var idx = con.children().length;
            con.append('<div class="col-6 mb-1"><div class="border rounded p-1 bg-white position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="premium-file-input p-1" style="min-height:50px"><div class="placeholder-content"><i data-feather="image" style="width:16px"></i></div><input type="file" name="'+$(this).data('prefix')+'['+idx+'][image]" onchange="handlePreview(this)"></div><div class="preview-container"></div><input type="text" name="'+$(this).data('prefix')+'['+idx+'][title]" class="form-control form-control-sm mt-1" placeholder="Protocol Name"></div></div>');
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
