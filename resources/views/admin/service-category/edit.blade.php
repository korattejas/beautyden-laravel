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
                        <h2 class="content-header-title float-start mb-0">Edit Service Category</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                 <li class="breadcrumb-item">
                                    <a href="{{ route('admin.service-category.index') }}">Service Category</a>
                                </li>
                                <li class="breadcrumb-item active"><a
                                        href="#">Edit Service Category</a>
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
                                        <input type="hidden" name="edit_value" value="{{$category->id}}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{trans('admin_string.name')}}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{$category->name}}"
                                                        placeholder="{{trans('admin_string.name')}}" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Image</label>
                                                    @if(isset($category->icon) && !empty($category->icon))
                                                        <div class="mb-3">
                                                            <img src="{{ asset('uploads/service-category/' . $category->icon) }}"
                                                                alt="Category Image" style="width: 250px; height: auto;" />
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control filepond" name="icon">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="4"
                                                        placeholder="{{trans('admin_string.description')}}">{{($category->description ?? '')}}</textarea>

                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select id="status" name="status" class="form-control" required>
                                                        <option value="">{{trans('admin_string.select_status')}}
                                                        </option>
                                                        <option value="1" @if($category->status == '1') selected @endif>
                                                            Active</option>
                                                        <option value="0" @if($category->status == '0') selected @endif>
                                                            Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Priority Status</label>
                                                    <select id="is_popular" name="is_popular" class="form-control" required>
                                                        <option value="">
                                                            {{trans('admin_string.select_priority_status')}}
                                                        </option>
                                                        <option value="1" @if($category->is_popular == '1') selected @endif>
                                                            High Priority</option>
                                                        <option value="0" @if($category->is_popular == '0') selected @endif>
                                                            Low Priority</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                    <div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                        <a href="{{ route('admin.service-category.index') }}"
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
    var form_url = 'service-category/store';
    var redirect_url = 'service-category';
    var is_one_image_and_multiple_image_status = 'is_one_image';
</script>
@endsection