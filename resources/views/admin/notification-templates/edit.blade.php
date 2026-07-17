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
                        <h2 class="content-header-title float-start mb-0">Edit Notification Template</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.notification-templates.index') }}">Templates</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="main_form" class="form" method="POST" action="javascript:void(0);">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $template->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="form-group">
                                                <label for="event_name" class="form-label">System Event (Read Only)</label>
                                                <input type="text" class="form-control" id="event_name_display" value="{{ strtoupper(str_replace('_', ' ', $template->event_name)) }}" readonly>
                                                <input type="hidden" name="event_name" value="{{ $template->event_name }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <div class="form-group">
                                                <label for="type" class="form-label">Redirect Screen Type <span class="text-danger">*</span></label>
                                                <select name="type" id="type" class="form-select select2" required>
                                                    @foreach($screenTypes as $key => $label)
                                                        <option value="{{ $key }}" {{ $template->type == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-2">
                                            <div class="form-group">
                                                <label for="title" class="form-label">Notification Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title" value="{{ $template->title }}" placeholder="e.g. Booking Confirmed! ✅" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-2">
                                            <div class="form-group">
                                                <label for="message" class="form-label">Message Body <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter message text here" required>{{ $template->message }}</textarea>
                                                <div class="form-text text-muted mt-2" style="font-size: 14px;">
                                                    <strong>Available Variables:</strong> <br>
                                                    <code>{user_name}</code> - Customer Name, 
                                                    <code>{order_id}</code> - Order Number, 
                                                    <code>{amount}</code> - Transaction/Wallet Amount, 
                                                    <code>{coupon_code}</code> - Coupon/Offer Code.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 mt-3 text-end">
                                            <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-outline-secondary me-1">Cancel</a>
                                            <button type="submit" class="btn btn-primary" id="submit_btn">Update Template</button>
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
    var form_url = '/notification-templates/store';
    var redirect_url = '/notification-templates';
</script>
<script src="{{ URL::asset('panel-assets/js/core/submit-ajax.js') }}?v={{ time() }}"></script>
@endsection
