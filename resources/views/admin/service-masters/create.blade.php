@extends('admin.layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<div class="app-content content pa-catalog-page">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="catalogForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="0">
                
                <div class="pa-catalog-toolbar">
                    <div>
                        <h2>Create Premium Catalog</h2>
                        <p>Build high-quality service pages with dynamic sections.</p>
                    </div>
                    <div class="pa-catalog-toolbar-actions">
                        <a href="{{ route('admin.service-master.index') }}" class="btn btn-outline-secondary">Discard</a>
                        <button type="submit" class="btn btn-primary">Publish Service</button>
                    </div>
                </div>

                <div class="row pa-catalog-layout">
                    <div class="col-xl-5">
                        <div class="pa-catalog-sidebar">
                        <div class="card shadow-sm border-0 pa-catalog-card">
                            <div class="card-header border-bottom"><h4 class="card-title"><i data-feather="info" class="me-1"></i>Basic Service Info</h4></div>
                            <div class="card-body pt-2">
                                <div class="mb-2">
                                    <label class="form-label">Service Master Icon (Thumbnail)</label>
                                    <input type="file" class="form-control filepond" name="icon">
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
                                <div class="mb-1">
                                    <label class="form-label">Recommended Skin Type</label>
                                    <select name="skin_type" class="form-select select2">
                                        <option value="All Skin Types" selected>All Skin Types</option>
                                        <option value="Normal Skin">Normal Skin</option>
                                        <option value="Dry Skin">Dry Skin</option>
                                        <option value="Normal To Dry Skin">Normal To Dry Skin</option>
                                        <option value="Oily Skin">Oily Skin</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-1">
                                        <div class="form-check form-switch pa-variant-toggle">
                                            <input class="form-check-input ms-0 me-1" type="checkbox" name="has_variants" value="1" id="has_variants">
                                            <label class="form-check-label" for="has_variants">Does this service have variants? (e.g., O3, Raaga)</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="global-price-section">
                                    <div class="row">
                                        <div class="col-6 mb-1"><label class="form-label">Global Default Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="price" class="form-control" id="global_price"></div></div>
                                        <div class="col-6 mb-1"><label class="form-label">Global Discounted Price</label><div class="input-group"><span class="input-group-text">₹</span><input type="number" name="discount_price" class="form-control"></div></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-1"><label class="form-label">Duration</label><div class="input-group"><span class="input-group-text"><i data-feather="clock"></i></span><input type="text" name="duration" class="form-control" placeholder="30 Min"></div></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-1"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
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

                        <div class="card shadow-sm border-0 pa-catalog-card" id="variants-section" style="display: none;">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title text-primary">Service Variants</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-variant">+ Add Variant</button>
                            </div>
                            <div class="card-body pt-2" id="variants-container">
                                <!-- Variant Rows will go here -->
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 pa-catalog-card">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Banner Media (Carousel)</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-banner">+ Add Media</button>
                            </div>
                            <div class="card-body pt-2" id="banner-media-container">
                                <div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative">
                                    <div class="d-flex justify-content-end mb-1 pe-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input banner-type-toggle" type="checkbox" name="banner[0][is_scroll_banner_image]" value="1" checked>
                                            <label class="form-check-label small fw-bold">Is Scroll Banner Image?</label>
                                        </div>
                                    </div>
                                    <div class="premium-file-input">
                                        <div class="placeholder-content">
                                            <i data-feather="upload-cloud" class="text-primary mb-1"></i>
                                            <p class="mb-0 fw-bold small">Click to upload Image or Video</p>
                                        </div>
                                        <input type="file" name="banner[0][file]" onchange="handlePreview(this)">
                                    </div>
                                    <div class="preview-container"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 pa-catalog-card">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Before & After Results</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary add-ba">+ Add Result</button>
                            </div>
                            <div class="card-body pt-2" id="ba-container">
                                <div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative">
                                    <div class="premium-file-input">
                                        <div class="placeholder-content">
                                            <i data-feather="image" class="text-primary mb-1"></i>
                                            <p class="mb-0 fw-bold small">Upload Result Image</p>
                                        </div>
                                        <input type="file" name="ba_images[]" onchange="handlePreview(this)">
                                    </div>
                                    <div class="preview-container"></div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Right Side: Content Sections -->
                    <div class="col-xl-7">
                        <div class="card shadow-sm border-0 pa-catalog-builder">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                                <h4 class="card-title text-primary"><i data-feather="layout" class="me-1"></i>Dynamic Page Components <small class="text-muted ms-1">(Drag to Reorder)</small></h4>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">+ Add Section</button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg">
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="overview"><i data-feather="grid" class="me-50"></i> Overview Essentials</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="ritual"><i data-feather="layers" class="me-50"></i> Ritual / Steps Section</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="procedure"><i data-feather="refresh-cw" class="me-50"></i> Procedure (Carousel)</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="expert"><i data-feather="user-check" class="me-50"></i> Expert Profile</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="protocol"><i data-feather="shield" class="me-50"></i> Hygiene Protocols</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="list"><i data-feather="list" class="me-50"></i> Information List</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="aftercare"><i data-feather="heart" class="me-50"></i> Aftercare Tips</a>
                                        <a class="dropdown-item add-section" href="javascript:void(0)" data-type="note"><i data-feather="alert-circle" class="me-50"></i> Please Note</a>
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

    <!-- Aftercare Tips -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="aftercare">
        <input type="hidden" name="sections[INDEX][type]" value="aftercare">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo"><i data-feather="heart" class="me-50"></i> Aftercare Tips</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="List Heading (e.g. Post-Facial Care)">
            <div class="points-container">
                <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
            </div>
        </div>
    </div>

    <!-- Please Note -->
    <div class="section-block card mb-2 border shadow-none overflow-hidden" data-type="note">
        <input type="hidden" name="sections[INDEX][type]" value="note">
        <div class="section-header d-flex justify-content-between align-items-center"><h5 class="mb-0 fw-bold text-indigo"><i data-feather="alert-circle" class="me-50"></i> Please Note</h5><button type="button" class="btn btn-icon btn-flat-danger btn-sm remove-section"><i data-feather="trash-2"></i></button></div>
        <div class="card-body py-1 bg-white">
            <input type="text" name="sections[INDEX][title]" class="form-control mb-1 fw-bold" placeholder="List Heading (e.g. Important Instructions)">
            <div class="points-container">
                <div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="sections[INDEX][points][]" class="form-control"><button type="button" class="btn btn-outline-indigo add-point">+</button></div>
            </div>
        </div>
    </div>

    <!-- List (Keep for compatibility) -->
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

    window.handleVariantThumbPreview = function(input) {
        const file = input.files[0];
        if (!file) return;
        const row = $(input).closest('.variant-row');
        const previewDiv = row.find('.variant-thumb-preview');
        const reader = new FileReader();
        reader.onload = function(e) {
            previewDiv.find('img').attr('src', e.target.result);
            previewDiv.show();
        };
        reader.readAsDataURL(file);
    };

    $(document).on('click', '.clear-variant-thumb', function() {
        const row = $(this).closest('.variant-row');
        row.find('.variant-thumb-preview').hide().find('img').attr('src', '');
        row.find('input[type="file"][name*="thumbnail_image"]').val('');
    });


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
            row.removeClass('has-preview');
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

        var sectionIndex = 0;
        var bannerIndex = 1;
        $('.select2').select2({ width: '100%' });

        // Initialize Sortable
        var el = document.getElementById('sections-container');
        var sortable = Sortable.create(el, {
            animation: 150,
            handle: '.section-header', // Drag by header
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
                        let newName = name.replace(/sections\[\d+\]/, 'sections[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
                $(this).find('.add-step, .add-protocol-item').each(function() {
                    let prefix = $(this).attr('data-prefix');
                    if (prefix) {
                        let newPrefix = prefix.replace(/sections\[\d+\]/, 'sections[' + index + ']');
                        $(this).attr('data-prefix', newPrefix);
                    }
                });
            });
            // Update global sectionIndex to avoid collisions if needed
            sectionIndex = $('#sections-container .section-block').length;
        }

        $('.add-banner').click(function() {
            var html = '<div class="banner-media-row mb-1 p-2 border rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button>';
            html += '<div class="d-flex justify-content-end mb-1 pe-4"><div class="form-check form-switch"><input class="form-check-input banner-type-toggle" type="checkbox" name="banner['+bannerIndex+'][is_scroll_banner_image]" value="1" checked><label class="form-check-label small fw-bold">Is Scroll Banner Image?</label></div></div>';
            html += '<div class="premium-file-input"><div class="placeholder-content"><i data-feather="upload-cloud" class="text-primary mb-1"></i><p class="mb-0 fw-bold small">Upload File</p></div><input type="file" name="banner['+bannerIndex+'][file]" onchange="handlePreview(this)"></div><div class="preview-container"></div>';
            html += '</div>';
            $('#banner-media-container').append(html);
            bannerIndex++; feather.replace();
        });

        $('.add-ba').click(function() {
            $('#ba-container').append('<div class="ba-row border p-1 mb-1 rounded bg-light bg-opacity-50 position-relative animate__animated animate__fadeIn"><button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-1 remove-row" style="z-index:10"><i data-feather="x"></i></button><div class="premium-file-input"><div class="placeholder-content"><i data-feather="image" class="text-primary mb-1"></i><p class="mb-0 fw-bold small">Upload Image</p></div><input type="file" name="ba_images[]" onchange="handlePreview(this)"></div><div class="preview-container"></div></div>');
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
            reindexSections();
        });

        $(document).on('click', '.add-step', function() {
            var con = $(this).siblings('.steps-container');
            var idx = new Date().getTime();
            con.append('<div class="step-card border rounded p-1 mb-1 bg-white shadow-sm position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="row"><div class="col-4"><div class="premium-file-input p-1" style="min-height:60px"><div class="placeholder-content"><p class="mb-0 x-small fw-bold">Img</p></div><input type="file" name="'+$(this).data('prefix')+'['+idx+'][image]" onchange="handlePreview(this)"></div><div class="preview-container"></div></div><div class="col-8"><input type="text" name="'+$(this).data('prefix')+'['+idx+'][title]" class="form-control mb-1 form-control-sm" placeholder="Title"><textarea name="'+$(this).data('prefix')+'['+idx+'][desc]" class="form-control form-control-sm" rows="2"></textarea></div></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-protocol-item', function() {
            var con = $(this).siblings('.protocol-items-container');
            var idx = new Date().getTime();
            con.append('<div class="col-6 mb-1"><div class="border rounded p-1 bg-white position-relative"><button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button><div class="premium-file-input p-1" style="min-height:50px"><div class="placeholder-content"><i data-feather="image" style="width:16px"></i></div><input type="file" name="'+$(this).data('prefix')+'['+idx+'][image]" onchange="handlePreview(this)"></div><div class="preview-container"></div><input type="text" name="'+$(this).data('prefix')+'['+idx+'][title]" class="form-control form-control-sm mt-1" placeholder="Protocol Name"></div></div>');
            feather.replace();
        });

        $(document).on('click', '.add-point', function() {
            var con = $(this).closest('.points-container');
            con.append('<div class="input-group mb-1"><span class="input-group-text"><i data-feather="check"></i></span><input type="text" name="'+con.find('input').attr('name')+'" class="form-control"><button type="button" class="btn btn-outline-danger remove-row"><i data-feather="minus"></i></button></div>');
            feather.replace();
        });

        $(document).on('click', '.remove-row', function() { $(this).closest('.step-card, .banner-media-row, .ba-row, .col-6, .input-group, .variant-row').remove(); });
        $(document).on('click', '.remove-section', function() { 
            $(this).closest('.section-block').remove(); 
            if(!$('.section-block').length) $('.empty-msg').show(); 
            reindexSections();
        });

        $('#category_id').on('change', function() {
            var id = $(this).val();
            $('#sub_category_id').html('<option value="">Loading...</option>');
            if(id) $.get(APP_URL + '/service-master/get-subcategories/' + id, function(data) {
                var html = '<option value="">Select</option>';
                data.forEach(function(i) { html += '<option value="'+i.id+'">'+i.name+'</option>'; });
                $('#sub_category_id').html(html);
            });
        });

        // Variants Logic
        $('#has_variants').change(function() {
            if($(this).is(':checked')) {
                $('#global-price-section').slideUp();
                $('#global_price').val('');
                $('#variants-section').slideDown();
                if($('#variants-container').children().length === 0) {
                    $('.add-variant').trigger('click');
                }
            } else {
                $('#global-price-section').slideDown();
                $('#variants-section').slideUp();
            }
        });

        var variantIndex = 0;
        $('.add-variant').click(function() {
            var html = '<div class="variant-row border p-2 mb-2 rounded bg-white shadow-sm position-relative">';
            html += '<button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 remove-row">×</button>';
            html += '<div class="row g-1">';
            html += '<div class="col-md-12 mb-1"><label class="form-label small fw-bold">Variant Name (e.g. O3, Raaga)</label><input type="text" name="variants['+variantIndex+'][name]" class="form-control form-control-sm" required></div>';
            html += '<div class="col-md-12 mb-1"><label class="form-label small">Description</label><textarea name="variants['+variantIndex+'][description]" class="form-control form-control-sm" rows="2" placeholder="Short description of this variant..."></textarea></div>';
            html += '<div class="col-md-6 mb-1"><label class="form-label small">Price (₹)</label><div class="input-group input-group-sm"><span class="input-group-text">₹</span><input type="number" name="variants['+variantIndex+'][price]" class="form-control" required></div></div>';
            html += '<div class="col-md-6 mb-1"><label class="form-label small">Discount %</label><div class="input-group input-group-sm"><input type="number" step="0.01" name="variants['+variantIndex+'][discount_percentage]" class="form-control" placeholder="e.g. 10"><span class="input-group-text">%</span></div></div>';
            html += '<div class="col-md-6 mb-1"><label class="form-label small">Duration</label><input type="text" name="variants['+variantIndex+'][duration]" class="form-control form-control-sm" placeholder="e.g. 30 Min"></div>';
            html += '<div class="col-md-3 mb-1"><label class="form-label small">Rating</label><input type="number" step="0.1" min="0" max="5" name="variants['+variantIndex+'][rating]" class="form-control form-control-sm" placeholder="4.5"></div>';
            html += '<div class="col-md-3 mb-1"><label class="form-label small">Reviews #</label><input type="number" name="variants['+variantIndex+'][reviews]" class="form-control form-control-sm" placeholder="150"></div>';
            html += '<div class="col-md-12 mb-1"><label class="form-label small">Thumbnail Image</label><div class="variant-thumb-upload" style="border:2px dashed #d1d5db;border-radius:8px;padding:8px;text-align:center;cursor:pointer;position:relative;"><p class="mb-0 small text-muted"><i data-feather="image" style="width:14px"></i> Upload Thumbnail</p><input type="file" name="variants['+variantIndex+'][thumbnail_image]" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;" onchange="handleVariantThumbPreview(this)"></div><div class="variant-thumb-preview mt-1" style="display:none;"><img style="max-height:60px;border-radius:6px;border:1px solid #ddd;" src=""><button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 clear-variant-thumb">Remove</button></div></div>';
            html += '</div></div>';
            $('#variants-container').append(html);
            feather.replace();
            variantIndex++;
        });


    });
</script>
@endsection
