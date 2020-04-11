function initDaterange(type) {
	var ranges = {};

	// Daterange selector initialization
	var start = moment().subtract(29, 'days');
	var end = moment();

	function cb(start, end) {
		$('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
	}

	if(type == 'month') {
		$.each(moment()._locale._monthsShort, function(i, item) {
			var mm_var_name = 'msGlobals.ms_report_date_range_month_' + item.toLowerCase();
			ranges[eval(mm_var_name)] = [moment().month(i).startOf('month'), moment().month(i).endOf('month')];
		});
	} else if (type == 'default') {
		ranges[msGlobals.ms_report_date_range_today] = [moment(), moment()];
		ranges[msGlobals.ms_report_date_range_yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
		ranges[msGlobals.ms_report_date_range_last7days] = [moment().subtract(6, 'days'), moment()];
		ranges[msGlobals.ms_report_date_range_last30days] = [moment().subtract(29, 'days'), moment()];
		ranges[msGlobals.ms_report_date_range_thismonth] = [moment().startOf('month'), moment().endOf('month')];
		ranges[msGlobals.ms_report_date_range_lastmonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
	}

	$('#reportrange').daterangepicker({
		startDate: start,
		endDate: end,
		locale: {
			format: 'YYYY-MM-DD',
			"customRangeLabel": msGlobals.ms_report_date_range_custom,
			"applyLabel": msGlobals.ms_report_date_range_apply,
			"cancelLabel": msGlobals.ms_report_date_range_cancel,
			"daysOfWeek": [
				msGlobals.ms_report_date_range_day_su,
				msGlobals.ms_report_date_range_day_mo,
				msGlobals.ms_report_date_range_day_tu,
				msGlobals.ms_report_date_range_day_we,
				msGlobals.ms_report_date_range_day_th,
				msGlobals.ms_report_date_range_day_fr,
				msGlobals.ms_report_date_range_day_sa
			],
			"monthNames": [
				msGlobals.ms_report_date_range_month_jan,
				msGlobals.ms_report_date_range_month_feb,
				msGlobals.ms_report_date_range_month_mar,
				msGlobals.ms_report_date_range_month_apr,
				msGlobals.ms_report_date_range_month_may,
				msGlobals.ms_report_date_range_month_jun,
				msGlobals.ms_report_date_range_month_jul,
				msGlobals.ms_report_date_range_month_aug,
				msGlobals.ms_report_date_range_month_sep,
				msGlobals.ms_report_date_range_month_oct,
				msGlobals.ms_report_date_range_month_nov,
				msGlobals.ms_report_date_range_month_dec
			],
			"firstDay": 1
		},
		ranges: ranges
	}, cb);

	cb(start, end);
}
