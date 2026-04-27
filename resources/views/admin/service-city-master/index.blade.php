@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; --mst-radius: 12px; --mst-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    body { background-color: var(--mst-bg); font-family: 'Poppins', sans-serif; }
    .header-btn {
        padding: 0 24px !important; font-weight: 700 !important; border-radius: 12px !important;
        display: flex; align-items: center; gap: 10px; height: 48px;
        background: linear-gradient(135deg, #1a237e 0%, #311b92 100%) !important;
        border: none !important; color: #fff !important;
        box-shadow: 0 4px 15px rgba(26, 35, 126, 0.25) !important;
    }
    .card { border-radius: var(--mst-radius); border: none; box-shadow: var(--mst-shadow); }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">App Service City Master</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Service City Master (App)</li>
                    </ol>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-flex align-items-center justify-content-end d-none">
                <a href="{{ route('admin.service-city-master.create') }}" class="btn btn-primary header-btn">
                    <i class="bi bi-plus-lg"></i> Add App Pricing
                </a>
            </div>
        </div>

        <div class="content-body">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Filter by City</label>
                            <select id="filter-city" class="form-select select2">
                                <option value="">All Cities</option>
                                @foreach($cities as $city) <option value="{{ $city->id }}">{{ $city->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="master-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>City</th>
                                    <th>Service</th>
                                    <th>Category</th>
                                    <th>Price (Base)</th>
                                    <th>App Disc. (%)</th>
                                    <th>Avail.</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
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
    $(document).ready(function() {
        var table = $('#master-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.service-city-master.getData') }}",
                data: function(d) {
                    d.city_id = $('#filter-city').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'city_name', name: 'c.name' },
                { data: 'service_name', name: 'sm.name' },
                { data: 'category_name', name: 'sc.name' },
                { data: 'price', name: 'scm.price' },
                { data: 'app_discount_percentage', name: 'scm.app_discount_percentage' },
                { data: 'is_available', name: 'scm.is_available', orderable: false },
                { data: 'status', name: 'scm.status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        $('#filter-city').change(function() { table.draw(); });
    });

    function deleteRecord(id) {
        if(confirm("Are you sure?")) {
            $.ajax({
                url: "{{ url('admin/service-city-master') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    toastr.success(res.message);
                    $('#master-table').DataTable().ajax.reload();
                }
            });
        }
    }
</script>
@endsection
