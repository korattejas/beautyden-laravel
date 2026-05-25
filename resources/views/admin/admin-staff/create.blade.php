@extends('admin.layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Create Admin Staff</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.admin-staff.index') }}">Admin Staff</a></li>
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
                                                <label>Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" placeholder="admin@beautyden.com" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="role_id" class="form-control">
                                                    <option value="">Super Admin</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 mt-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.admin-staff.index') }}" class="btn btn-secondary">Cancel</a>
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
    var form_url = 'admin-staff/store';
    var redirect_url = 'admin-staff';
    var is_one_image_and_multiple_image_status = '';
</script>
@endsection
