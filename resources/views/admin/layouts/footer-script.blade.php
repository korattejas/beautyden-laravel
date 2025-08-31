<!-- BEGIN: Vendor JS -->
<script src="{{ URL::asset('panel-assets/vendors/js/vendors.min.js') }}"></script>
<!-- END: Vendor JS -->

<!-- BEGIN: Page Vendor JS -->
<script src="{{ URL::asset('panel-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/extensions/moment.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ URL::asset('panel-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/vendors/js/tables/datatable/responsive.bootstrap5.min.js') }}"></script>
<script src="https://cdn.datatables.net/rowgroup/1.1.4/js/dataTables.rowGroup.min.js"></script>

<!-- Select2 -->
<script src="{{ URL::asset('panel-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<!-- File Upload (FilePond) -->
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

<!-- Tags Input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

<!-- END: Page Vendor JS -->

<!-- BEGIN: Theme JS -->
<script src="{{ URL::asset('panel-assets/js/core/app-menu.js') }}"></script>
<script src="{{ URL::asset('panel-assets/js/core/app.js') }}"></script>
<script src="{{ URL::asset('panel-assets/js/scripts/axios.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/js/scripts/blockUI.js') }}"></script>
<script src="{{ URL::asset('panel-assets/js/scripts/parsley.min.js') }}"></script>
<script src="{{ URL::asset('panel-assets/js/core/form.js') }}?v={{ time() }}"></script>
<script src="{{ URL::asset('panel-assets/js/core/custom.js') }}"></script>
<script src="{{ URL::asset('panel-assets/js/scripts/customizer.js') }}"></script>
<!-- END: Theme JS -->

<!-- Feather Icons Init -->
<script>
    $(window).on('load', function () {
        if (window.feather) {
            window.feather.replace({ width: 14, height: 14 });
        }
    });
</script>
