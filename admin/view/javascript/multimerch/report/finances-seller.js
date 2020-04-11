$(function() {
    var id = '#list-reports-finances-seller';

    initDaterange('default');
    initDatatable(id);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        initDatatable(id, picker);
    });
});