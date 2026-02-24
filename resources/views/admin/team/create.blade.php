@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <h2 class="content-header-title float-start mb-0">Add Team Member</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.team.index') }}">Team Members</a></li>
                            <li class="breadcrumb-item active">Add Team Member</li>
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
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        <div class="row">

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        placeholder="Full Name" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Role</label>
                                                    <input type="text" class="form-control" name="role"
                                                        placeholder="e.g. Hair Stylist" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Experience (Years)</label>
                                                    <input type="number" class="form-control" name="experience_years"
                                                        placeholder="e.g. 5">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Specialties (comma separated)</label>
                                                    <textarea class="form-control" name="specialties" rows="2" placeholder="e.g. Haircut, Coloring, Styling"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="number" class="form-control" name="phone"
                                                        placeholder="9999999999" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Bio</label>
                                                    <textarea class="form-control" name="bio" rows="3" placeholder="Write a short bio"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Certifications (comma separated)</label>
                                                    <textarea class="form-control" name="certifications" rows="2"
                                                        placeholder="e.g. Diploma in Hairdressing, Styling Workshop"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Profile Photo</label>
                                                    <input type="file" class="form-control filepond" name="icon">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <input type="text" class="form-control" name="state"
                                                        placeholder="State">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text" class="form-control" name="city"
                                                        placeholder="City">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Taluko</label>
                                                    <input type="text" class="form-control" name="taluko"
                                                        placeholder="Taluko">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Village</label>
                                                    <input type="text" class="form-control" name="village"
                                                        placeholder="Village">
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <textarea class="form-control" name="address" rows="2" placeholder="Full Address"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Popular</label>
                                                    <select name="is_popular" class="form-control">
                                                        <option value="0" selected>No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3" style="text-align: right;">
                                                <button type="submit" class="btn btn-primary">Submit</button>
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
