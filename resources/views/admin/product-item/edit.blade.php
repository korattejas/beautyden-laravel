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
        min-height: 100px;
    }
    .section-block {
        padding: 12px;
    }
    .premium-file-input:hover { border-color: #6366f1; background: #f5f3ff; }
    .premium-file-input input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 5; }
    .premium-file-input.has-preview { border-style: solid; border-color: #e2e8f0; background: #f8fafc; }
    .preview-container { position: relative; margin-top: 10px; }
    .preview-media { max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #ddd; }
    .preview-actions { position: absolute; top: 5px; right: 5px; display: flex; gap: 5px; z-index: 10; }
    .action-btn { background: rgba(255,255,255,0.9); border: none; border-radius: 4px; padding: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; }
    .action-btn:hover { background: #fff; transform: scale(1.1); }
</style>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="productForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="{{ $product->id }}">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-0">Edit Premium Product</h2>
                        <p class="text-muted mb-0">Update product details, variants and rich content.</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-item.index') }}" class="btn btn-outline-secondary">Discard</a>
                        <button type="submit" class="btn btn-primary px-3 shadow">Update Product</button>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Side: Basic Info & Media -->
                    <div class="col-xl-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom"><h4 class="card-title"><i data-feather="info" class="me-1"></i>Basic Product Info</h4></div>
                            <div class="card-body pt-2">
                                <div class="mb-1">
                                    <label class="form-label">Product Title</label>
                                    <input type="text" name="name" class="form-control form-control-lg border-primary border-opacity-25" value="{{ $product->name }}" placeholder="e.g. Matte Lipstick" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Brand</label>
                                        <select name="brand_id" class="form-select select2">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand) 
                                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-select select2" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat) 
                                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Sub Category</label>
                                        <select name="sub_category_id" id="sub_category_id" class="form-select select2">
                                            <option value="">Select Sub Category</option>
                                            @foreach($subcategories as $sub)
                                                <option value="{{ $sub->id }}" {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">SKU</label>
                                        <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" placeholder="e.g. PROD123">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-1"><label class="form-label">Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="price" class="form-control" value="{{ $product->price }}" required></div></div>
                                    <div class="col-6 mb-1"><label class="form-label">Discount %</label><div class="input-group"><input type="number" name="discount_percentage" class="form-control" value="{{ $product->discount_percentage }}" placeholder="0"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-1"><label class="form-label">Stock Qty</label><input type="number" name="stock_quantity" class="form-control" value="{{ $product->stock_quantity }}" placeholder="0"></div>
                                    <div class="col-md-4 mb-1"><label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Popularity</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ $product->is_featured ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="is_featured">Featured?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">New</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_new" value="1" id="is_new" {{ $product->is_new ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="is_new">Is New?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8 mb-1">
                                        <label class="form-label">Visibility</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="show_in_client_app" value="1" id="show_in_client_app" {{ $product->show_in_client_app ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="show_in_client_app">Show in Client App?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Short Description</label>
                                    <textarea name="short_description" class="form-control" rows="2" placeholder="Brief info...">{{ $product->short_description }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Full Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Detailed info...">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Product Gallery</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-media">+ Add Media</button>
                            </div>
                            <div class="card-body pt-2" id="media-container">
                                @foreach($product->media as $media)
                                    <div class="media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative">
                                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>
                                        <div class="preview-container" style="display:block">
                                            <div class="preview-actions">
                                                <button type="button" class="action-btn text-primary view-full" data-url="{{ asset('uploads/product-media/' . $media->file_path) }}"><i data-feather="maximize" style="width:14px"></i></button>
                                            </div>
                                            @if($media->type == 'video')
                                                <video src="{{ asset('uploads/product-media/' . $media->file_path) }}" class="preview-media" controls></video>
                                            @else
                                                <img src="{{ asset('uploads/product-media/' . $media->file_path) }}" class="preview-media">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                <div class="media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative">
                                    <div class="premium-file-input">
                                        <div class="placeholder-content">
                                            <i data-feather="upload-cloud" class="text-primary mb-1"></i>
                                            <p class="mb-0 fw-bold small">Click to upload Image or Video</p>
                                        </div>
                                        <input type="file" name="media[]" onchange="handlePreview(this)">
                                    </div>
                                    <div class="preview-container"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Product Variants</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-variant">+ Add Variant</button>
                            </div>
                            <div class="card-body pt-2" id="variant-container">
                                @foreach($product->variants as $variant)
                                    <div class="variant-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative">
                                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>
                                        <div class="row">
                                            <div class="col-4">
                                                <label class="form-label">Variant Name</label>
                                                <input type="text" name="variants[][name]" class="form-control form-control-sm" value="{{ $variant->variant_name }}" placeholder="e.g. Ruby Red">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">Price</label>
                                                <input type="number" name="variants[][price]" class="form-control form-control-sm" value="{{ $variant->price }}" placeholder="Optional">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">Stock</label>
                                                <input type="number" name="variants[][stock_quantity]" class="form-control form-control-sm" value="{{ $variant->stock_quantity }}" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Content Sections -->
                    <div class="col-xl-7">
                        <div class="card shadow-sm border-0 bg-light bg-opacity-25" style="min-height: 800px;">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                                <h4 class="card-title text-primary"><i data-feather="layout" class="me-1"></i>Dynamic Page Components <small class="text-muted" style="font-size: 10px;">(Drag to Reorder)</small></h4>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">+ Add Section</button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg">
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="list"><i data-feather="list" class="me-50"></i> Bullet Points / Features</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="how_to_use"><i data-feather="help-circle" class="me-50"></i> How to Use</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="note"><i data-feather="alert-circle" class="me-50"></i> Important Note</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2" id="sections-container">
                                @php $sections = $product->content_json ?? []; @endphp
                                @if(count($sections) == 0)
                                    <div class="text-center py-5 empty-msg">
                                        <div class="bg-white rounded-circle shadow-sm d-inline-flex p-3 mb-2">
                                            <i data-feather="box" style="width: 60px; height: 60px; color: #cbd5e1;"></i>
                                        </div>
                                        <h4 class="text-secondary fw-bold">No Custom Sections Added</h4>
                                        <p class="text-muted mx-auto" style="max-width: 300px;">Select from "+ Add Section" to start building rich content.</p>
                                    </div>
                                @endif

                                @foreach($sections as $idx => $section)
                                    @php $type = $section['type']; @endphp
                                    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="{{ $type }}">
                                        <input type="hidden" name="content_json[{{ $idx }}][type]" value="{{ $type }}">
                                        <div class="section-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 fw-bold text-indigo">
                                                <i data-feather="{{ $type == 'list' ? 'list' : ($type == 'how_to_use' ? 'help-circle' : 'alert-circle') }}" class="me-50"></i> 
                                                {{ $type == 'list' ? 'Features / Points' : ($type == 'how_to_use' ? 'How to Use' : 'Important Note') }}
                                            </h5>
                                            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
                                        </div>
                                        <div class="card-body py-1 bg-white">
                                            <input type="text" name="content_json[{{ $idx }}][title]" class="form-control mb-1 fw-bold" value="{{ $section['title'] ?? '' }}" placeholder="Section Title">
                                            
                                            @if($type == 'list' || $type == 'note')
                                                <div class="points-container">
                                                    @foreach($section['points'] ?? [] as $point)
                                                        <div class="input-group mb-1">
                                                            <span class="input-group-text"><i data-feather="check"></i></span>
                                                            <input type="text" name="content_json[{{ $idx }}][points][]" class="form-control" value="{{ $point }}">
                                                            <button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button>
                                                        </div>
                                                    @endforeach
                                                    <div class="input-group mb-1">
                                                        <span class="input-group-text"><i data-feather="check"></i></span>
                                                        <input type="text" name="content_json[{{ $idx }}][points][]" class="form-control">
                                                        <button type="button" class="btn btn-outline-indigo add-point">+</button>
                                                    </div>
                                                </div>
                                            @elseif($type == 'how_to_use')
                                                <div class="steps-container">
                                                    @foreach($section['steps'] ?? [] as $sIdx => $step)
                                                        <div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative">
                                                            <button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button>
                                                            <input type="text" name="content_json[{{ $idx }}][steps][{{ $sIdx }}][title]" class="form-control mb-1 form-control-sm" value="{{ $step['title'] ?? '' }}" placeholder="Step Title">
                                                            <textarea name="content_json[{{ $idx }}][steps][{{ $sIdx }}][desc]" class="form-control form-control-sm" rows="2" placeholder="Description">{{ $step['desc'] ?? '' }}</textarea>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="content_json[{{ $idx }}][steps]">+ Add Step Item</button>
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

<!-- Templates for JS (Same as Create) -->
<div id="templates" style="display: none;">
    <!-- List / Features -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="list">
        <input type="hidden" name="content_json[INDEX][type]" value="list">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-indigo"><i data-feather="list" class="me-50"></i> Features / Points</h5>
            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
        </div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="content_json[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Section Title (e.g. Why You'll Love It)">
            <div class="points-container">
                <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="content_json[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
            </div>
        </div>
    </div>

    <!-- How to Use -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="how_to_use">
        <input type="hidden" name="content_json[INDEX][type]" value="how_to_use">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-indigo"><i data-feather="help-circle" class="me-50"></i> How to Use</h5>
            <button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button>
        </div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="content_json[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Section Title (e.g. Application Steps)">
            <div class="steps-container"></div>
            <button type="button" class="btn btn-sm btn-outline-indigo add-step w-100" data-prefix="content_json[INDEX][steps]">+ Add Step Item</button>
        </div>
    </div>

    <!-- Note -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="note">
        <input type="hidden" name="content_json[INDEX][type]" value="note">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo"><i data-feather="alert-circle" class="me-50"></i> Important Note</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="content_json[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="Heading (e.g. Storage Instructions)">
            <div class="points-container">
                <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="content_json[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    var form_url = 'product-item/store';
    var redirect_url = 'product-item';

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

    $(function() {
        $('.select2').select2({ width: '100%' });

        $(document).on('click', '.clear-input', function() {
            const row = $(this).closest('.preview-container').siblings('.premium-file-input');
            row.find('input[type="file"]').val('');
            row.removeClass('has-preview');
            $(this).closest('.preview-container').fadeOut().empty();
            row.find('.placeholder-content').show();
        });

        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            loaderView();
            let formData = new FormData(this);
            axios.post(APP_URL + '/admin/' + form_url, formData)
                .then(res => {
                    notificationToast(res.data.message, 'success');
                    setTimeout(() => window.location.href = APP_URL + '/admin/' + redirect_url, 1000);
                })
                .catch(err => {
                    loaderHide();
                    notificationToast(err.response?.data?.message || 'Something went wrong', 'warning');
                });
        });

        var sectionIndex = {{ count($sections) }};

        // Initialize Sortable
        var el = document.getElementById('sections-container');
        var sortable = Sortable.create(el, {
            animation: 150,
            handle: '.section-header',
            ghostClass: 'bg-light',
            onEnd: function() {
                reindexSections();
            }
        });

        function reindexSections() {
            $('#sections-container .section-block').each(function(index) {
                $(this).find('input, select, textarea').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        let newName = name.replace(/content_json\[\d+\]/, 'content_json[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
            sectionIndex = $('#sections-container .section-block').length;
        }

        $('.add-media').click(function() {
            $('#media-container').append('<div class="media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="premium-file-input"><div class="placeholder-content"><i data-feather="upload-cloud" class="text-primary mb-1"></i><p class="mb-0 fw-bold small">Upload File</p></div><input type="file" name="media[]" onchange="handlePreview(this)"></div><div class="preview-container"></div></div>');
            feather.replace();
        });

        $('.add-variant').click(function() {
            $('#variant-container').append('<div class="variant-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="row"><div class="col-4"><label class="form-label">Variant Name</label><input type="text" name="variants[][name]" class="form-control form-control-sm" placeholder="e.g. Ruby Red"></div><div class="col-4"><label class="form-label">Price</label><input type="number" name="variants[][price]" class="form-control form-control-sm" placeholder="Optional"></div><div class="col-4"><label class="form-label">Stock</label><input type="number" name="variants[][stock_quantity]" class="form-control form-control-sm" placeholder="0"></div></div></div>');
            feather.replace();
        });

        $('.add-section').click(function() {
            $('.empty-msg').hide();
            var type = $(this).data('type');
            var html = $('#templates [data-type="'+type+'"]').clone();
            $('#sections-container').append(html[0].outerHTML.replace(/INDEX/g, sectionIndex));
            sectionIndex++; 
            feather.replace();
            reindexSections();
        });

        $(document).on('click', '.add-point', function() {
            var con = $(this).closest('.points-container');
            con.append('<div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="'+con.find('input').attr('name')+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button></div>');
            feather.replace();
        });

        $(document).on('click', '.add-step', function() {
            var con = $(this).siblings('.steps-container');
            var idx = con.children().length;
            con.append('<div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><input type="text" name="'+$(this).data('prefix')+'['+idx+'][title]" class="form-control mb-1 form-control-sm" placeholder="Step Title"><textarea name="'+$(this).data('prefix')+'['+idx+'][desc]" class="form-control form-control-sm" rows="2" placeholder="Description"></textarea></div>');
            feather.replace();
        });

        $(document).on('click', '.remove-row', function() { $(this).closest('.media-row, .variant-row, .step-card, .input-group').remove(); });
        $(document).on('click', '.remove-section', function() { 
            $(this).closest('.section-block').remove(); 
            if(!$('.section-block').length) $('.empty-msg').show(); 
            reindexSections();
        });

        $('#category_id').on('change', function() {
            var id = $(this).val();
            $('#sub_category_id').html('<option value="">Loading...</option>');
            if(id) $.get(APP_URL + '/admin/product-item/get-subcategories/' + id, function(data) {
                var html = '<option value="">Select</option>';
                data.forEach(function(i) { html += '<option value="'+i.id+'">'+i.name+'</option>'; });
                $('#sub_category_id').html(html);
            });
        });
    });
</script>
@endsection
