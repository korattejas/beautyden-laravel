@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Add Hiring</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hirings.index') }}">Hirings</a></li>
                        <li class="breadcrumb-item active">Add Hiring</li>
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
                                <form method="POST" data-parsley-validate id="addEditForm" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="0">
                                    <input type="hidden" id="form-method" value="add">

                                    <div class="row">

                                        <!-- Title -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Job Title</label>
                                                <input type="text" class="form-control" name="title" placeholder="e.g. Software Engineer" required>
                                            </div>
                                        </div>

                                        <!-- City -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" class="form-control" name="city" placeholder="e.g. Surat" required>
                                            </div>
                                        </div>

                                        <!-- Min Experience -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Min Experience (years)</label>
                                                <input type="number" class="form-control" name="min_experience" min="0">
                                            </div>
                                        </div>

                                        <!-- Max Experience -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Max Experience (years)</label>
                                                <input type="number" class="form-control" name="max_experience" min="0">
                                            </div>
                                        </div>

                                        <!-- Salary Range -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Salary Range</label>
                                                <input type="text" class="form-control" name="salary_range" placeholder="e.g. ₹20,000 - ₹40,000">
                                            </div>
                                        </div>

                                        <!-- Experience Level -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Experience Level</label>
                                                <select name="experience_level" class="form-control">
                                                    <option value="1">Fresher</option>
                                                    <option value="2" selected>Experienced</option>
                                                    <option value="3">Expert</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Hiring Type -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Hiring Type</label>
                                                <select name="hiring_type" class="form-control">
                                                    <option value="1">Full-time</option>
                                                    <option value="2">Part-time</option>
                                                    <option value="3">Internship</option>
                                                    <option value="4" selected>Work from home</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Gender Preference -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Gender Preference</label>
                                                <select name="gender_preference" class="form-control">
                                                    <option value="1">Female</option>
                                                    <option value="2">Male</option>
                                                    <option value="3" selected>Any</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Required Skills -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Required Skills (comma separated)</label>
                                                <input type="text" class="form-control" name="required_skills" placeholder="e.g. PHP, Laravel, React">
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Job Description</label>
                                                <textarea class="form-control" name="description" rows="4" placeholder="Write job description"></textarea>
                                            </div>
                                        </div>

                                        <!-- Popular -->
                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Popular</label>
                                                <select name="is_popular" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1" selected>Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="col-12 mt-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <a href="{{ route('admin.hirings.index') }}" class="btn btn-secondary">Cancel</a>
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
    var form_url = 'hirings/store';
    var redirect_url = 'hirings';
</script>
@endsection
