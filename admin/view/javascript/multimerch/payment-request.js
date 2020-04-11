$(function() {
	$('#list-payment-requests').dataTable( {
		"sAjaxSource": "index.php?route=multimerch/payment-request/getTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "request_id" },
			{ "mData": "request_type" },
			{ "mData": "seller" },
			{ "mData": "amount" },
			{ "mData": "description", "bSortable": false },
			{ "mData": "date_created" },
			{ "mData": "request_status" },
			{ "mData": "payment_id" },
			{ "mData": "date_modified" }
		]
	});

	$(document).on('click', '#list-payment-requests input:checkbox', function() {
		var selected_invoices = $('#list-payment-requests').children('tbody').find('input:checkbox:checked');
		if(selected_invoices.length > 0) {
			$('#ms-button-delete').show();
		} else {
			$('#ms-button-delete').hide();
		}
	});

	$("#ms-button-pay").click(function(e) {
		e.preventDefault();
		if($("#list-payment-requests tbody input:checkbox:checked").length == 0) {
			show_error(msGlobals.ms_pg_request_error_select_payment_request);
		} else {
			$(".error-holder").html('');
			$("#form").submit();
		}
	});

	function show_error(error) {
		var html = '';
		html += '<div class="cl"></div>';
		html += '<div class="alert alert-danger">'
		html += '<i class="fa fa-exclamation-circle"></i> ';
		html += error;
		html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		html += '</div>';
		$(".error-holder").html(html);
	}
});