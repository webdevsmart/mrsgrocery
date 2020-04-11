$(function() {
    var id = '#list-report-finances-payout';

    initDaterange('default');
    initDatatable(id);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        initDatatable(id, picker);
    });
});