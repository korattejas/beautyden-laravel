@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <h2 class="content-header-title float-start mb-0">Edit Team Member</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.team.index') }}">Team Members</a></li>
                            <li class="breadcrumb-item active">Edit Team Member</li>
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
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="{{ $team->id }}">
                                        <input type="hidden" id="form-method" value="edit">

                                        <div class="row">

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ old('name', $team->name) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Role</label>
                                                    <input type="text" class="form-control" name="role"
                                                        value="{{ old('role', $team->role) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Experience (Years)</label>
                                                    <input type="number" class="form-control" name="experience_years"
                                                        value="{{ old('experience_years', $team->experience_years) }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Specialties (comma separated)</label>
                                                    <textarea class="form-control" name="specialties" rows="2"> 
                                                        {{
                                                        is_array($team->specialties)
                                                            ? implode(',', $team->specialties)
                                                            : ($team->specialties
                                                                ? implode(',', json_decode($team->specialties, true))  {{-- string hoy to decode --}}
                                                                : '')
                                                    }}
                                                    </textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Bio</label>
                                                    <textarea class="form-control" name="bio" rows="3">{{ old('bio', $team->bio) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Certifications (comma separated)</label>
                                                    <textarea class="form-control" name="certifications" rows="2">
                                                        {{
                                                        is_array($team->certifications)
                                                            ? implode(',', $team->certifications)
                                                            : ($team->certifications
                                                                ? implode(',', json_decode($team->certifications, true))  {{-- string hoy to decode --}}
                                                                : '')
                                                        }}
                                                    </textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Profile Photo</label>
                                                    @if(isset($team->icon) && !empty($team->icon))
                                                    <div class="mb-3">
                                                        <img src="{{ asset('uploads/team-member/' . $team->icon) }}"
                                                            alt="Service Icon" style="width: 120px; height: auto;" />
                                                    </div>
                                                @endif
                                                    <input type="file" class="form-control filepond" name="icon">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Popular</label>
                                                    <select name="is_popular" class="form-control">
                                                        <option value="0" {{ $team->is_popular == 0 ? 'selected' : '' }}>No</option>
                                                        <option value="1" {{ $team->is_popular == 1 ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" {{ $team->status == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ $team->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <a href="{{ route('admin.team.index') }}"
                                                    class="btn btn-secondary">Cancel</a>
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
        var form_url = 'team/store';
        var redirect_url = 'team';
        var is_one_image_and_multiple_image_status = 'is_one_image';
    </script>
@endsection
