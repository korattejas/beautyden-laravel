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
                            <h2 class="content-header-title float-start mb-0">Edit Portfolio</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.portfolio.index') }}">Portfolio</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Edit Portfolio</a>
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
                                            <input type="hidden" name="edit_value" value="{{ $portfolio->id }}">
                                            <input type="hidden" id="form-method" value="edit">
                                            <div class="row row-sm">

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ trans('admin_string.name') }}</label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ $portfolio->name }}"
                                                            placeholder="{{ trans('admin_string.name') }}" required>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                              <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                       @if (isset($portfolio) && !empty($portfolio->photos))
                                                            <div class="mb-3 d-flex flex-wrap gap-2">
                                                                @foreach ($portfolio->photos as $img)
                                                                    <div style="position: relative;">
                                                                        <img 
                                                                            src="{{ asset('uploads/portfolio/' . $img) }}"
                                                                            alt="Portfolio Image"
                                                                            style="width:120px;height:120px;object-fit:cover;border:1px solid #ddd;border-radius:4px;"
                                                                        >
                                                                        <span 
                                                                            class="remove-image" 
                                                                            data-image="{{ $img }}"
                                                                            style="position:absolute;top:2px;right:6px;cursor:pointer;color:red;font-weight:bold;"
                                                                        >×</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        <input 
                                                            type="file"
                                                            class="form-control filepond"
                                                            name="photos[]"
                                                            multiple
                                                            accept="image/*">

                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select id="status" name="status" class="form-control" required>
                                                            <option value="">
                                                                {{ trans('admin_string.select_status') }}
                                                            </option>
                                                            <option value="1"
                                                                @if ($portfolio->status == '1') selected @endif>
                                                                Active</option>
                                                            <option value="0"
                                                                @if ($portfolio->status == '0') selected @endif>
                                                                Inactive</option>
                                                        </select>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                        <div>
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                            <a href="{{ route('admin.portfolio.index') }}"
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
        var form_url = 'portfolio/store';
        var redirect_url = 'portfolio';
        var is_one_image_and_multiple_image_status = 'is_multiple_image';
    </script>
@endsection
