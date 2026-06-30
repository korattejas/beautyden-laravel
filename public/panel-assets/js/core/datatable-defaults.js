/**
 * Global DataTables defaults — loaded on every admin page.
 * Pages can still override via $.extend(true, $.fn.dataTable.defaults, { ... })
 */
(function ($) {
    'use strict';

    if (typeof $.fn.dataTable === 'undefined') {
        return;
    }

    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
        autoWidth: false,
        responsive: false,
        dom:
            '<"pa-dt-toolbar pa-dt-toolbar-top"' +
            '<"pa-dt-length"l><"pa-dt-pagination-top"p>>' +
            '<"pa-dt-table-wrap"t>' +
            '<"pa-dt-toolbar pa-dt-toolbar-bottom"' +
            '<"pa-dt-info"i><"pa-dt-pagination-bottom"p>>',
        language: {
            lengthMenu: '_MENU_',
            info: 'Showing _START_–_END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 entries',
            infoFiltered: '(filtered from _MAX_ total)',
            search: '',
            searchPlaceholder: 'Search…',
            paginate: {
                previous: '‹',
                next: '›',
                first: '«',
                last: '»',
            },
            processing: '<span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading…',
            emptyTable: 'No records found',
            zeroRecords: 'No matching records found',
        },
        drawCallback: function () {
            var $wrapper = $(this.api().table().container());
            $wrapper.find('.pagination .page-link').addClass('pa-dt-page-link');
        },
        initComplete: function () {
            var api = this.api();
            var $wrapper = $(api.table().container());

            $wrapper.addClass('pa-dt-wrapper');
            $wrapper.closest('.card-datatable').addClass('pa-dt-card');

            var $table = $(api.table().node());
            $table.addClass('pa-dt-table');
            $table.closest('.pa-dt-table-wrap').addClass('pa-dt-table-wrap-ready');
        },
    });
})(jQuery);
