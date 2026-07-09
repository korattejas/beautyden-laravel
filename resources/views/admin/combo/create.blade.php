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
                        <h2 class="content-header-title float-start mb-0">Create Service Combo</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.combo.index') }}">Service Combos</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Add Combo</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="content-body">
                <section class="horizontal-wizard">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <div class="row row-sm">

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Combo Name</label>
                                                    <input type="text" name="name" class="form-control" placeholder="e.g. Bridal Glow Package" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Service Type</label>
                                                    <select name="service_type_id" class="form-control select2" required>
                                                        <option value="">Select Service Type</option>
                                                        @foreach($serviceTypes as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="3" placeholder="Combo description..."></textarea>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Combo Image</label>
                                                    <input type="file" name="icon" class="filepond" accept="image/*">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

@php
    $groupedServices = [];
    foreach($services as $service) {
        $catName = $service->category ? $service->category->name : 'Other Categories';
        $subCatName = $service->subcategory ? $service->subcategory->name : 'General Services';
        
        if (!isset($groupedServices[$catName])) {
            $groupedServices[$catName] = [];
        }
        if (!isset($groupedServices[$catName][$subCatName])) {
            $groupedServices[$catName][$subCatName] = [];
        }
        $groupedServices[$catName][$subCatName][] = $service;
    }
@endphp
<style>
    .service-selector-wrapper { border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; display: flex; flex-direction: column; }
    .service-search-box { padding: 15px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px 8px 0 0; }
    .service-list-wrapper { max-height: 400px; overflow-y: auto; padding: 0; }
    .service-category-header { background: #1a4a7a; color: #fff; padding: 10px 15px; font-weight: 700; position: sticky; top: 0; z-index: 10; font-size: 1rem; }
    .service-subcategory-header { background: #f1f5f9; color: #475569; padding: 8px 15px; font-weight: 600; font-size: 0.9rem; border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; }
    .service-item-row { padding: 10px 15px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; transition: background 0.2s; }
    .service-item-row:hover { background: #f8fafc; }
    .service-item-row:last-child { border-bottom: none; }
    .service-checkbox { width: 18px; height: 18px; margin-right: 12px; cursor: pointer; accent-color: #1a4a7a; }
    .service-label { margin: 0; cursor: pointer; font-weight: 500; color: #1e293b; flex-grow: 1; }
    .variant-badge { background: #e0e7ff; color: #4f46e5; font-size: 0.75rem; padding: 2px 8px; border-radius: 50px; margin-left: 8px; font-weight: 600; }
</style>
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Select Services (Multiple) <span class="text-danger">*</span></label>
                                                    
                                                    <div class="service-selector-wrapper">
                                                        <div class="service-search-box">
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                                                <input type="text" id="serviceSearchInput" class="form-control border-start-0 ps-0 shadow-none" placeholder="Search by service name, category or variant...">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="service-list-wrapper" id="serviceListWrapper">
                                                            @foreach($groupedServices as $catName => $subcategories)
                                                                <div class="service-category-group" data-category="{{ strtolower($catName) }}">
                                                                    <div class="service-category-header">
                                                                        {{ $catName }}
                                                                    </div>
                                                                    
                                                                    @foreach($subcategories as $subCatName => $svcs)
                                                                        <div class="service-subcategory-group" data-subcategory="{{ strtolower($subCatName) }}">
                                                                            @if($subCatName != 'General Services')
                                                                                <div class="service-subcategory-header">
                                                                                    {{ $subCatName }}
                                                                                </div>
                                                                            @endif
                                                                            
                                                                            @foreach($svcs as $service)
                                                                                @if($service->variants->count() > 0)
                                                                                    @foreach($service->variants as $variant)
                                                                                        <div class="service-item-row" data-search="{{ strtolower($service->name . ' ' . $variant->name . ' ' . $catName . ' ' . $subCatName) }}">
                                                                                            <input type="checkbox" name="services[]" value="S_{{ $service->id }}_V_{{ $variant->id }}" class="service-checkbox custom-service-checkbox" id="svc_{{ $service->id }}_var_{{ $variant->id }}">
                                                                                            <label class="service-label" for="svc_{{ $service->id }}_var_{{ $variant->id }}">
                                                                                                {{ $service->name }} <span class="variant-badge">{{ $variant->name }}</span>
                                                                                            </label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    <div class="service-item-row" data-search="{{ strtolower($service->name . ' ' . $catName . ' ' . $subCatName) }}">
                                                                                        <input type="checkbox" name="services[]" value="S_{{ $service->id }}" class="service-checkbox custom-service-checkbox" id="svc_{{ $service->id }}">
                                                                                        <label class="service-label" for="svc_{{ $service->id }}">
                                                                                            {{ $service->name }}
                                                                                        </label>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                            <div id="noServiceFoundMsg" style="display: none; padding: 20px; text-align: center; color: #64748b;">
                                                                <i class="bi bi-search" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                                                No services found matching your search.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2" id="default-services-section" style="display:none;">
                                                <div class="form-group">
                                                    <label>Mark Default (Pre-selected) Services</label>
                                                    <div id="default-services-list" class="mt-2"></div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                    <div>
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                        <a href="{{ route('admin.combo.index') }}" class="btn btn-secondary">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    var form_url = 'combo/store';
    var redirect_url = 'combo';
    var is_one_image_and_multiple_image_status = 'is_one_image';

    $(document).ready(function() {
        // Select2
        $('.select2').select2({ placeholder: "Choose services...", width: '100%' });

        // Search functionality
        $('#serviceSearchInput').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            let foundAny = false;
            
            $('.service-category-group').each(function() {
                let hasVisibleServiceInCategory = false;
                
                $(this).find('.service-subcategory-group').each(function() {
                    let hasVisibleServiceInSubcat = false;
                    
                    $(this).find('.service-item-row').each(function() {
                        let searchStr = $(this).data('search');
                        if (searchStr.indexOf(value) > -1) {
                            $(this).show();
                            hasVisibleServiceInSubcat = true;
                            hasVisibleServiceInCategory = true;
                            foundAny = true;
                        } else {
                            $(this).hide();
                        }
                    });
                    
                    if (hasVisibleServiceInSubcat) {
                        $(this).children('.service-subcategory-header').show();
                        $(this).show();
                    } else {
                        $(this).children('.service-subcategory-header').hide();
                        $(this).hide();
                    }
                });
                
                if (hasVisibleServiceInCategory) {
                    $(this).children('.service-category-header').show();
                    $(this).show();
                } else {
                    $(this).children('.service-category-header').hide();
                    $(this).hide();
                }
            });
            
            if (!foundAny && value !== '') {
                $('#noServiceFoundMsg').show();
            } else {
                $('#noServiceFoundMsg').hide();
            }
        });

        // Update default services list
        $(document).on('change', '.custom-service-checkbox', function() {
            updateDefaultServicesList();
        });
        
        function updateDefaultServicesList() {
            let listHtml = '';
            let hasChecked = false;
            
            $('.custom-service-checkbox:checked').each(function() {
                hasChecked = true;
                let val = $(this).val();
                let text = $(this).siblings('.service-label').html(); // this includes the variant span if any
                
                listHtml += `
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="default_services[]" value="${val}" id="def_${val}" checked>
                        <label class="form-check-label" for="def_${val}">${text}</label>
                    </div>
                `;
            });
            
            if (hasChecked) {
                $('#default-services-section').show();
            } else {
                $('#default-services-section').hide();
            }
            $('#default-services-list').html(listHtml);
        }
    });
</script>
@endsection
