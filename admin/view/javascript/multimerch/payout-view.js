$(function() {
	$('#list-invoices').dataTable({
		"sAjaxSource": "index.php?route=multimerch/payout/getInvoiceTableData&token=" + msGlobals.token + "&payout_id=" + msGlobals.payout_id,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "request_id" },
			{ "mData": "seller" },
			{ "mData": "amount" },
			{ "mData": "request_status" }
		],
		"aaSorting": [[1, 'desc']],
		"iDisplayLength": 10
	});

	$(document).on('click', 'table[id="list-invoices"] input:checkbox', function() {
		var selected_invoices = $('table[id="list-invoices"]').find('tbody input[name="selected[]"]:checked');
		if(selected_invoices.length > 0) {
			$('#ms-pay').show();
		} else {
			$('#ms-pay').hide();
		}
	});

	$(document).on('click', '#ms-pay', function() {
		var invoice_ids = [];
		var selected_invoices = $('table[id="list-invoices"]').find('tbody input[name="selected[]"]:checked');

		$.map(selected_invoices, function(selected_invoice) {
			invoice_ids.push($(selected_invoice).val())
		});

		if(invoice_ids.length > 0) {
			var urlGetParams = '&request_ids=' + invoice_ids.join(',');
			window.location.href = $('base').attr('href') + "index.php?route=multimerch/payment/create&token=" + msGlobals.token + urlGetParams;
		}
	});
});