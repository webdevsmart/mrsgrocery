$(function() {
    var id = '#list-reports-sales-day';

    initDaterange('default');
    initDatatable(id);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        initDatatable(id, picker);
    });
});