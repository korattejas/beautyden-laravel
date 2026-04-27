@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--mst-bg); }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 20px; }
    .form-label { font-weight: 700; color: #1e293b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .btn-save { background: var(--mst-primary); color: #fff; font-weight: 700; border-radius: 8px; padding: 12px 40px; border: none; }
    .select2-container--default .select2-selection--multiple { border-color: #e2e8f0; border-radius: 8px; padding: 5px; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Create Service Combo</h2>
            </div>
        </div>

        <div class="content-body">
            <form id="combo-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">Basic Information</h5>
                                <div class="mb-3">
                                    <label class="form-label">Combo Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Bridal Glow Package" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Min. Order Price (₹)</label>
                                        <input type="number" name="min_price" class="form-control" placeholder="e.g. 599" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Combo Image</label>
                                    <input type="file" name="image" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">Included Services</h5>
                                <div class="mb-3">
                                    <label class="form-label">Select Services (Multiple)</label>
                                    <select name="services[]" id="services-select" class="form-select select2" multiple required>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="default-services-section" style="display:none;">
                                    <label class="form-label">Mark Default (Pre-selected) Services</label>
                                    <div id="default-services-list" class="mt-2">
                                        <!-- Dynamically populated via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-save shadow">Create Combo</button>
                            <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 12px 30px;">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ placeholder: "Choose services..." });

        $('#services-select').on('change', function() {
            let selectedData = $(this).select2('data');
            let listHtml = '';
            
            if(selectedData.length > 0) {
                $('#default-services-section').show();
                selectedData.forEach(item => {
                    listHtml += `
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="default_services[]" value="${item.id}" id="def_${item.id}" checked>
                            <label class="form-check-label" for="def_${item.id}">${item.text}</label>
                        </div>
                    `;
                });
            } else {
                $('#default-services-section').hide();
            }
            $('#default-services-list').html(listHtml);
        });

        $('#combo-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.combo.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        setTimeout(() => window.location.href = "{{ route('admin.combo.index') }}", 1000);
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        });
    });
</script>
@endsection
