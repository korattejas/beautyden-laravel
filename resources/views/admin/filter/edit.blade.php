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
                        <h2 class="content-header-title float-start mb-0">{{trans('admin_string.edit_filter')}}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">{{trans('admin_string.edit_filter')}}</a>
                                </li>
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
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="{{$filter->id}}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{trans('admin_string.name')}}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{$filter->name}}"
                                                        placeholder="{{trans('admin_string.name')}}" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <select name="values[]" class="form-control select2-dropdown"
                                                        id="values" multiple>
                                                        @php
                                                            $selectedValues = explode(',', $filter->values);
                                                        @endphp

                                                        @foreach ($filterShortValues as $value)
                                                            <option value="{{ $value }}" {{ in_array($value, $selectedValues) ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>


                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>{{trans('admin_string.status')}}</label>
                                                    <select id="status" name="status" class="form-control" required>
                                                        <option value="">{{trans('admin_string.select_status')}}
                                                        </option>
                                                        <option value="1" @if($filter->status == '1') selected @endif>
                                                            Active</option>
                                                        <option value="0" @if($filter->status == '0') selected @endif>
                                                            Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>{{trans('admin_string.priority_status')}}</label>
                                                    <select id="is_main" name="is_main" class="form-control" required>
                                                        <option value="">
                                                            {{trans('admin_string.select_priority_status')}}</option>
                                                        <option value="1" @if($filter->is_main == '1') selected @endif>
                                                            High Priority</option>
                                                        <option value="0" @if($filter->is_main == '0') selected @endif>Low
                                                            Priority</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                    <div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                        <a href="{{ route('admin.filter.index') }}"
                                                            class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
    var form_url = 'filter/store';
    var redirect_url = 'filter';
    $(document).ready(function () {
        $('.select2-dropdown').select2({
            allowClear: true
        });
    });
</script>
@endsection