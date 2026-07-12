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

    .premium-create-container {
        padding: 2rem;
        background: var(--mst-bg-body);
        min-height: calc(100vh - 60px);
    }

    .create-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .create-title h2 {
        font-weight: 800;
        color: var(--mst-indigo);
        font-size: 1.75rem;
        margin: 0;
    }

    .create-form-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--mst-border);
        padding: 2.5rem;
        /* max-width: 1000px; */
        margin: 0 auto;
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
            <div class="premium-create-container">
                
                <div class="create-card-header">
                    <div class="create-title">
                        <h2>Create Category Lookbook ✨</h2>
                    </div>
                    <a href="{{ route('admin.category_lookbook.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left"></i> Back to Gallery
                    </a>
                </div>

                <div class="create-form-card">
                    <form method="POST" enctype="multipart/form-data" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" name="edit_value" value="0">
                        
                        <div class="row">
                            <!-- Category Selection -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Select Category</label>
                                <select name="category_id" id="category_id" class="form-control luxury-input select2" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Select Sub Category (Optional)</label>
                                <select name="sub_category_id" id="sub_category_id" class="form-control luxury-input select2">
                                    <option value="">Select a sub category</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Status</label>
                                <select id="status" name="status" class="form-control luxury-input" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <!-- Image Management -->
                            <div class="col-12 mb-4">
                                <label class="form-label-luxury">Gallery Collection</label>
                                
                                <div class="upload-zone p-5 text-center" style="background: #f1f5f9; border-radius: 16px; border: 2px dashed #cbd5e1;">
                                    <i class="bi bi-cloud-arrow-up-fill mb-3" style="font-size: 3rem; color: var(--mst-indigo);"></i>
                                    <h5 class="mb-3">Drag & Drop your photos here</h5>
                                    <p class="text-muted mb-4 small">Or click to browse from your device</p>
                                    
                                    <input type="file" class="form-control" name="photos[]" multiple accept="image/*" required>
                                    
                                    <div class="mt-4">
                                        <span class="badge bg-light text-dark border p-2">
                                            <i class="bi bi-info-circle me-1"></i> You can select multiple images at once
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-submit-luxury">
                                    Create Lookbook
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
        var form_url = 'category-lookbook/store';
        var redirect_url = 'category-lookbook';
        var is_one_image_and_multiple_image_status = 'is_multiple_image';
        
        $(document).ready(function() {
            if ($('.select2').length) {
                $('.select2').select2({
                    placeholder: "Select an option"
                });
            }

            $('#category_id').on('change', function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: '/admin/service-master/get-subcategories/' + categoryId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#sub_category_id').empty();
                            $('#sub_category_id').append('<option value="">Select a sub category</option>');
                            $.each(data, function(key, value) {
                                $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#sub_category_id').empty();
                    $('#sub_category_id').append('<option value="">Select a sub category</option>');
                }
            });
        });
    </script>
@endsection
