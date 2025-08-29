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
                        <h2 class="content-header-title float-start mb-0">{{trans('admin_string.setting')}}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">{{trans('admin_string.setting')}}</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
             <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.setting.create') }}" class="btn btn-primary">
                    Add Setting
                </a>
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
                                            <th>{{trans('admin_string.screen_name')}}</th>
                                            <th>{{trans('admin_string.key')}}</th>
                                            <th>{{trans('admin_string.value')}}</th>
                                            <th data-stuff="Active,InActive">{{trans('admin_string.status')}}</th>
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
    const sweetalert_delete_title = "Delete setting?";
    const sweetalert_change_status = "Change Status of setting";
    const sweetalert_change_priority_status = "Change Priority Status of setting";
    const form_url = '/setting';
    datatable_url = '/getDataSetting';

    $.extend(true, $.fn.dataTable.defaults, {
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'screen_name', name: 'screen_name' },
            { data: 'key', name: 'key' },
            {
                data: 'value',
                name: 'value',
                render: function (data, type, row, meta) {
                    if (/^#[0-9A-F]{6}$/i.test(data)) {
                        return `
                        <div style="display: flex; align-items: center;">
                            <div style="width: 20px; height: 20px; background-color: ${data}; border: 1px solid #000; margin-right: 5px;"></div>
                            ${data}
                        </div>`;
                    }
                    return data;
                }
            },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'DESC']],
    });

</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection