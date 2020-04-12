$(function() {
    var id = '#list-report-sales-day';

    initDaterange('default');
    initDatatable(id);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        initDatatable(id, picker);
    });
});