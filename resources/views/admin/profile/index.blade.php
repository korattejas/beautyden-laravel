@extends('admin.layouts.app')

@section('page_title', 'Profile')
@section('page_heading', 'My Profile')

@section('content')
<div class="pa-profile-page">

    <div class="pa-page-intro">
        <p>Manage your account settings and security preferences</p>
    </div>

    <div class="row g-4">
        {{-- Left column --}}
        <div class="col-lg-4">

            {{-- Identity card --}}
            <div class="pa-profile-identity">
                <div class="pa-profile-cover"></div>
                <div class="pa-profile-identity-body">
                    <div class="pa-profile-avatar-wrap">
                        <img class="pa-profile-avatar-lg" src="{{ asset('panel-assets/images/portrait/small/avatar-s-11.jpg') }}" alt="{{ $admin->name }}">
                        <span class="pa-profile-avatar-status" title="Online"></span>
                    </div>
                    <h2 class="pa-profile-identity-name">{{ $admin->name }}</h2>
                    <p class="pa-profile-email">{{ $admin->email }}</p>
                    <span class="pa-profile-role-badge">
                        <i class="bi bi-shield-check"></i>
                        {{ $admin->role ? $admin->role->name : 'Super Admin' }}
                    </span>

                    <div class="pa-profile-stats">
                        <div class="pa-profile-stat">
                            <span class="pa-profile-stat-value">Active</span>
                            <span class="pa-profile-stat-label">Status</span>
                        </div>
                        <div class="pa-profile-stat">
                            <span class="pa-profile-stat-value">{{ $admin->created_at ? $admin->created_at->format('M Y') : '—' }}</span>
                            <span class="pa-profile-stat-label">Joined</span>
                        </div>
                        <div class="pa-profile-stat">
                            <span class="pa-profile-stat-value">Admin</span>
                            <span class="pa-profile-stat-label">Access</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security --}}
            <div class="pa-card-subtle mt-4">
                <div class="pa-card-header">
                    <h6><i class="bi bi-lock me-2 text-muted"></i>Account Security</h6>
                </div>
                <div class="pa-card-body">
                    <div class="pa-security-list">
                        <div class="pa-security-item">
                            <div class="pa-security-item-left">
                                <div class="pa-security-icon green"><i class="bi bi-shield-check"></i></div>
                                <div>
                                    <strong>Two-Factor Authentication</strong>
                                    <span>Add an extra layer of security</span>
                                </div>
                            </div>
                            <span class="pa-security-tag">Disabled</span>
                        </div>
                        <div class="pa-security-item">
                            <div class="pa-security-item-left">
                                <div class="pa-security-icon blue"><i class="bi bi-key"></i></div>
                                <div>
                                    <strong>Password</strong>
                                    <span>Last changed — not tracked</span>
                                </div>
                            </div>
                            <span class="pa-security-tag enabled">Secure</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column — Edit Profile & Recent Sessions full width of right side --}}
        <div class="col-lg-8">
            <div class="pa-card-subtle w-100">
                <div class="pa-card-header">
                    <h6><i class="bi bi-person-gear me-2 text-muted"></i>Edit Profile</h6>
                </div>
                <div class="pa-card-body">
                    <form id="profileForm">
                        @csrf

                        <div class="pa-form-section">
                            <p class="pa-form-section-title">Personal Information</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="pa-form-field">
                                        <label for="profile_name">Full Name</label>
                                        <div class="pa-input-wrap">
                                            <i class="bi bi-person"></i>
                                            <input type="text" id="profile_name" name="name" value="{{ $admin->name }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="pa-form-field">
                                        <label for="profile_email">Email Address</label>
                                        <div class="pa-input-wrap">
                                            <i class="bi bi-envelope"></i>
                                            <input type="email" id="profile_email" name="email" value="{{ $admin->email }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pa-form-section">
                            <p class="pa-form-section-title">Change Password</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="pa-form-field">
                                        <label for="profile_password">New Password</label>
                                        <div class="pa-input-wrap">
                                            <i class="bi bi-lock"></i>
                                            <input type="password" id="profile_password" name="password" placeholder="Leave blank to keep current">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="pa-form-field">
                                        <label for="profile_password_confirm">Confirm Password</label>
                                        <div class="pa-input-wrap">
                                            <i class="bi bi-lock-fill"></i>
                                            <input type="password" id="profile_password_confirm" name="password_confirmation" placeholder="Re-enter new password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pa-form-actions">
                            <button type="submit" class="pa-btn pa-btn-primary" id="saveProfileBtn">
                                <i class="bi bi-check2"></i> Save Changes
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="pa-btn pa-btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="pa-card-subtle w-100 mt-4">
                <div class="pa-card-header d-flex align-items-center justify-content-between">
                    <h6><i class="bi bi-display me-2 text-muted"></i>Recent Sessions</h6>
                    <span class="pa-badge pa-badge-neutral">1 active</span>
                </div>
                <div class="pa-card-body p-0">
                    <div class="table-responsive">
                        <table class="pa-sessions-table">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Location</th>
                                    <th>Last Active</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="pa-session-device">
                                            <div class="pa-session-device-icon"><i class="bi bi-laptop"></i></div>
                                            <div>
                                                <strong>Current Session</strong>
                                                <small>Chrome · Desktop</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Web Browser</td>
                                    <td>Now</td>
                                    <td><span class="pa-badge pa-badge-success">Active</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('saveProfileBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    axios.post("{{ route('admin.profile.update') }}", new FormData(this))
        .then(res => {
            if (res.data.status) toastr.success(res.data.message);
        })
        .catch(err => {
            toastr.error(err.response?.data?.message || 'Update failed.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2"></i> Save Changes';
        });
});
</script>
@endsection
