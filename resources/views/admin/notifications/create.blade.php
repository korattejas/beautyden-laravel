@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--mst-bg); }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    .form-label { font-weight: 700; color: #1e293b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .btn-save { background: var(--mst-primary); color: #fff; font-weight: 700; border-radius: 8px; padding: 12px 40px; border: none; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Create Notification</h2>
            </div>
        </div>

        <div class="content-body">
            <form id="notif-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Notification Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. 50% Off Today!" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message Content</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Write your notification message here..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Banner Image (Optional)</label>
                                    <input type="file" name="image" class="form-control">
                                    <small class="text-muted">Recommended size: 1000x500px</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5 class="mb-3">Settings & Payload</h5>
                                <div class="mb-3">
                                    <label class="form-label">Scheduled Time (Optional)</label>
                                    <input type="datetime-local" name="scheduled_at" class="form-control">
                                    <small class="text-muted">Leave blank to send immediately.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Action Data (JSON - Optional)</label>
                                    <textarea name="custom_data" class="form-control" rows="3" placeholder='{"type": "offer", "id": 5}'></textarea>
                                    <small class="text-muted">Pass custom data for app redirection.</small>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Target Audience</label>
                                    <select name="target_type" class="form-select">
                                        <option value="all">All Registered Users</option>
                                        <option value="beauticians">Beauticians Only</option>
                                        <option value="customers">Customers Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-save shadow"><i class="bi bi-send-check"></i> Blast Notification</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    $('#notif-form').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.notifications.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if(res.success) {
                    toastr.success(res.message);
                    setTimeout(() => window.location.href = "{{ route('admin.notifications.index') }}", 1000);
                } else {
                    toastr.error(res.message);
                }
            }
        });
    });
</script>
@endsection
