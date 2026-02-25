@extends('admin.layouts.app')

@section('header_style_content')
<style>
    :root {
        --mst-indigo: #102365;
        --mst-indigo-light: #f5f7ff;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-bg-body: #f8fafc;
        --mst-border: #e2e8f0;
    }

    .premium-edit-container {
        padding: 2rem;
        background: var(--mst-bg-body);
        min-height: calc(100vh - 60px);
    }

    .edit-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .edit-title h2 {
        font-weight: 800;
        color: var(--mst-indigo);
        font-size: 1.75rem;
        margin: 0;
    }

    .edit-form-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--mst-border);
        padding: 2.5rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Photos Grid Redesign */
    .portfolio-photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #fcfdfe;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
    }

    .photo-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: 0.3s;
    }

    .photo-preview-item:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 15px rgba(0,0,0,0.12);
    }

    .photo-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-photo-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 26px;
        height: 26px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.1rem;
        transition: 0.2s;
        border: 2px solid #fff;
        backdrop-filter: blur(4px);
    }

    .remove-photo-btn:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }

    .form-label-luxury {
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: block;
    }

    .luxury-input {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 1rem;
        transition: 0.3s;
        background: #fbfcfe;
    }

    .luxury-input:focus {
        border-color: var(--mst-indigo);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(16, 35, 101, 0.08);
    }

    .btn-submit-luxury {
        background: var(--mst-indigo);
        color: #fff;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(16, 35, 101, 0.2);
    }

    .btn-submit-luxury:hover {
        background: #0a1740;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 35, 101, 0.3);
        color: #fff;
    }

</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="premium-edit-container">
                
                <div class="edit-card-header">
                    <div class="edit-title">
                        <h2>Edit Master Portfolio ✨</h2>
                    </div>
                    <a href="{{ route('admin.portfolio.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left"></i> Back to Gallery
                    </a>
                </div>

                <div class="edit-form-card">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="edit_value" value="{{ $portfolio->id }}">
                        
                        <div class="row">
                            <!-- Portfolio Name -->
                            <div class="col-md-8 mb-4">
                                <label class="form-label-luxury">Portfolio Name</label>
                                <input type="text" class="form-control luxury-input" name="name"
                                    value="{{ $portfolio->name }}"
                                    placeholder="Enter portfolio name (e.g. Bridal Look 2024)" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Status</label>
                                <select id="status" name="status" class="form-control luxury-input" required>
                                    <option value="1" @if ($portfolio->status == '1') selected @endif>Active</option>
                                    <option value="0" @if ($portfolio->status == '0') selected @endif>Inactive</option>
                                </select>
                            </div>

                            <!-- Image Management -->
                            <div class="col-12 mb-4">
                                <label class="form-label-luxury">Gallery Collection</label>
                                
                                @if (isset($portfolio) && !empty($portfolio->photos))
                                    <div class="portfolio-photos-grid" id="existing-photos">
                                        @foreach ($portfolio->photos as $img)
                                            <div class="photo-preview-item" id="photo-{{ md5($img) }}">
                                                <img src="{{ asset('uploads/portfolio/' . $img) }}" alt="Portfolio Image">
                                                <span class="remove-photo-btn remove-image-ajax" 
                                                      data-id="{{ $portfolio->id }}" 
                                                      data-image="{{ $img }}"
                                                      title="Delete this image">
                                                    <i class="bi bi-x"></i>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="upload-zone p-4 text-center" style="background: #f1f5f9; border-radius: 16px; border: 2px dashed #cbd5e1;">
                                    <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 2.5rem; color: var(--mst-indigo);"></i>
                                    <h5 class="mb-3">Add More Photos</h5>
                                    <input type="file" class="form-control" name="photos[]" multiple accept="image/*">
                                    <p class="text-muted mt-2 small">Upload high-quality JPG, PNG or WebP images.</p>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-submit-luxury">
                                    Save Portfolio Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        var form_url = 'portfolio/store';
        var redirect_url = 'portfolio';

        $(document).on('click', '.remove-image-ajax', function() {
            var icon = $(this);
            var id = icon.data('id');
            var imageName = icon.data('image');
            var parent = icon.closest('.photo-preview-item');

            Swal.fire({
                title: 'Delete Image?',
                text: "This image will be permanently removed from this portfolio.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#102365',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.portfolio.removeImage') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            image: imageName
                        },
                        success: function(response) {
                            if (response.success) {
                                parent.fadeOut(300, function() {
                                    $(this).remove();
                                });
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    });
                }
            });
        });

        // Initialize form submission via existing common logic if any, 
        // else we can add a local one. Assuming there's a global addEditForm handler.
    </script>
@endsection
