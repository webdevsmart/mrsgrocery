$(function() {
    var id = '#list-report-sales-month';

    initDaterange('month');
    initDatatable(id);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        initDatatable(id, picker);
    });
});