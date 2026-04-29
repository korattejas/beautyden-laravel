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
                        <h2 class="content-header-title float-start mb-0">Create Notification</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.notifications.index') }}">Notifications</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">New Notification</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section class="horizontal-wizard">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                    @csrf
                                    <div class="row row-sm">

                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Notification Title</label>
                                                <input type="text" name="title" class="form-control" placeholder="e.g. 50% Off Today!" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Message Content</label>
                                                <textarea name="message" class="form-control" rows="4" placeholder="Write your notification message here..." required></textarea>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Banner Image (Optional)</label>
                                                <input type="file" name="image" class="form-control">
                                                <small class="text-muted">Recommended size: 1000x500px</small>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Scheduled Time (Optional)</label>
                                                <input type="datetime-local" name="scheduled_at" class="form-control">
                                                <small class="text-muted">Leave blank to send immediately.</small>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Target Audience</label>
                                                <select name="target_type" class="form-control">
                                                    <option value="all">All Registered Users</option>
                                                    <option value="beauticians">Beauticians Only</option>
                                                    <option value="customers">Customers Only</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Action Data (JSON - Optional)</label>
                                                <textarea name="custom_data" class="form-control" rows="3" placeholder='{"type": "offer", "id": 5}'></textarea>
                                                <small class="text-muted">Pass custom data for app redirection.</small>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Blast Notification</button>
                                                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Cancel</a>
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
@endsection

@section('footer_script_content')
<script>
    var form_url = 'notifications/store';
    var redirect_url = 'notifications';
</script>
@endsection
