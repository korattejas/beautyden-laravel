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
                        <h2 class="content-header-title float-start mb-0">Edit Service Combo</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.combo.index') }}">Service Combos</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Edit Combo</a></li>
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
                                        <input type="hidden" name="edit_value" value="{{ $combo->id }}">
                                        <div class="row row-sm">

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Combo Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $combo->name }}" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Min. Order Price (₹)</label>
                                                    <input type="number" name="min_price" class="form-control" value="{{ $combo->min_price }}" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="3">{{ $combo->description }}</textarea>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Combo Image (Leave blank to keep current)</label>
                                                    <input type="file" name="image" class="form-control">
                                                    @if($combo->image)
                                                        <img src="{{ asset('uploads/combos/' . $combo->image) }}" class="mt-2 rounded" style="max-width:120px;">
                                                    @endif
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" {{ $combo->status == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ $combo->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Select Services (Multiple)</label>
                                                    <select name="services[]" id="services-select" class="form-control select2" multiple required>
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}" {{ in_array($service->id, $selectedServiceIds) ? 'selected' : '' }}>
                                                                {{ $service->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2" id="default-services-section">
                                                <div class="form-group">
                                                    <label>Mark Default (Pre-selected) Services</label>
                                                    <div id="default-services-list" class="mt-2">
                                                        @foreach($services as $service)
                                                            @if(in_array($service->id, $selectedServiceIds))
                                                                <div class="form-check mb-1">
                                                                    <input class="form-check-input" type="checkbox" name="default_services[]" value="{{ $service->id }}" id="def_{{ $service->id }}" {{ in_array($service->id, $defaultServiceIds) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="def_{{ $service->id }}">{{ $service->name }}</label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
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

    $(document).ready(function() {
        $('.select2').select2({ placeholder: "Choose services...", width: '100%' });

        $('#services-select').on('change', function() {
            let selectedData = $(this).select2('data');
            let listHtml = '';
            if (selectedData.length > 0) {
                $('#default-services-section').show();
                selectedData.forEach(item => {
                    let isChecked = $(`#def_${item.id}`).prop('checked') !== false ? 'checked' : '';
                    listHtml += `
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="default_services[]" value="${item.id}" id="def_${item.id}" ${isChecked}>
                            <label class="form-check-label" for="def_${item.id}">${item.text}</label>
                        </div>
                    `;
                });
            } else {
                $('#default-services-section').hide();
            }
            $('#default-services-list').html(listHtml);
        });
    });
</script>
@endsection
