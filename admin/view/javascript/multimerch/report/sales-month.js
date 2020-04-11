$(function() {
    var id = '#list-reports-sales-month';

    initDaterange('month');
    initDatatable(id);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        initDatatable(id, picker);
    });
});