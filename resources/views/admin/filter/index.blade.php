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
                        <h2 class="content-header-title float-start mb-0">{{trans('admin_string.filter')}}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">{{trans('admin_string.filter')}}</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Column Search -->
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable">
                                <table class="dt-column-search table w-100 dataTable" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>{{trans('admin_string.id')}}</th>
                                            <th>{{trans('admin_string.name')}}</th>
                                            <th>{{trans('admin_string.value')}}</th>
                                            <th data-stuff="Active,InActive">{{trans('admin_string.status')}}</th>
                                            <th data-stuff="High Priority,Low Priority">
                                                {{trans('admin_string.priority_status')}}
                                            </th>
                                            <th data-search="false">{{trans('admin_string.action')}}</th>

                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Column Search -->
        </div>
    </div>
</div>
@endsection
@section('footer_script_content')
<script>
    const sweetalert_delete_title = "Delete Filter?";
    const sweetalert_change_status = "Change Status of Filter";
    const sweetalert_change_priority_status = "Change Priority Status of Filter";
    const form_url = '/filter';
    datatable_url = '/getDataFilter';

    $.extend(true, $.fn.dataTable.defaults, {
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'name', name: 'name' },
            {
                data: 'values',
                name: 'values',
                render: function (data) {
                    let valuesArray = data.split(',');

                    let formattedValues = valuesArray.map(function (value) {
                        return `<span class="badge badge-glow bg-danger">${value.trim()}</span>`;
                    }).join(' ');

                    return formattedValues;
                }
            },
            { data: 'status', name: 'status' },
            { data: 'is_main', name: 'is_main' },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'DESC']],
    });

</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection