@extends('admin.layouts.app')

@section('content')
@php
    $modules = [
        'dashboard' => 'Dashboard',
        'appointments' => 'Appointments',
        'team_members' => 'Team Members',
        'service_catalog' => 'Service Catalog (Services, Categories)',
        'products' => 'Products Management',
        'offers' => 'Offers / Promos',
        'reviews' => 'Customer Reviews',
        'blogs' => 'Blogs',
        'contact_submissions' => 'Contact Submissions',
        'settings' => 'Settings (App, Masters)'
    ];
@endphp
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Create Role</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section class="horizontal-wizard">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" data-parsley-validate="" id="addEditForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="0">
                                    <input type="hidden" id="form-method" value="add">

                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Role Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" placeholder="e.g. Sales, Catalog Manager" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h5 class="mb-2 border-bottom pb-1">Assign Menu Permissions</h5>
                                        </div>
                                        @foreach($modules as $key => $label)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check form-check-primary">
                                                <input type="checkbox" class="form-check-input" id="perm_{{ $key }}" name="permissions[]" value="{{ $key }}">
                                                <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mt-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
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
@endsection

@section('footer_script_content')
<script>
    var form_url = 'roles/store';
    var redirect_url = 'roles';
    var is_one_image_and_multiple_image_status = '';
</script>
@endsection
