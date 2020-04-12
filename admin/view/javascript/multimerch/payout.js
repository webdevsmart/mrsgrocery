$(function() {
	$(document).on('click', '.select-all-invoices', function() {
		$(this).closest('table').find('input[name*="selected"]').prop('checked', $(this).prop('checked'));
	});

	// @todo: temporary deprecated. Do this in payout-view for invoice-payment linkings.
	function format(d) {
		var tableHolder = $('<div></div>').addClass('col-sm-12 table-responsive');
		var invoices_table = $('<table></table>').addClass('list table table-bordered table-hover').attr('id', 'table-invoices-' + d.payout_id);
		var tHead = $('<thead></thead>');
		var tRow = $('<tr></tr>');
		var tBody = $('<tbody></tbody>');

		tRow.append($('<td></td>').attr('width', '1').html('<input type="checkbox" class="select-all-invoices" />'));
		tRow.append($('<td></td>').addClass('small').text(msGlobals.ms_invoice + ' ' + msGlobals.ms_id));
		tRow.append($('<td></td>').addClass('medium').text(msGlobals.ms_seller));
		tRow.append($('<td></td>').addClass('small').text(msGlobals.ms_amount));
		tRow.append($('<td></td>').addClass('small').text(msGlobals.ms_status));

		tHead.append(tRow);
		invoices_table.append(tHead);
		invoices_table.append(tBody);
		tableHolder.append(invoices_table);

		invoices_table.dataTable({
			"sAjaxSource": "index.php?route=multimerch/payout/getInvoiceTableData&token=" + msGlobals.token + "&payout_id=" + d.payout_id,
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

		return tableHolder;
	}

	var dt = $('#list-payouts').dataTable({
		"sAjaxSource": "index.php?route=multimerch/payout/getPayoutTableData&token=" + msGlobals.token,
		"aoColumns": [
			/*{
				"class":          "details-control",
				"orderable":      false,
				"data":           null,
				"defaultContent": "<i class='fa fa-plus'></i>"
			},*/
			{ "mData": "payout_id" },
			{ "mData": "date_created" },
			{ "mData": "date_payout_period" },
			{ "mData": "name" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-center" },
		],
		"aaSorting": [[3, 'desc']],
		"iDisplayLength": 10
	});

	/*$('#list-payouts tbody').on('click', 'td.details-control', function() {
		var $this = $(this);
		var tr = $this.closest('tr');
		var row = dt.api().row( tr );

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
			tr.removeClass('shown');
			$this.find('i').removeClass('fa-minus').addClass('fa-plus');
		} else {
			// Open this row
			row.child(format(row.data())).show();
			tr.addClass('shown');
			$this.find('i').removeClass('fa-plus').addClass('fa-minus');
		}
	});*/


	/*************************************** Generate new payout (sellers list) ***************************************/


	$(document).on('click', '.ms-payouts-topbar li', function() {
		var $this = $(this);

		if ($this.find('a').attr('href') == '#tab-payout-new') {
			if ($(document).find('#datepicker').length < 1) {
				var html = '';
				html += '<div id="datepicker">';
				html += '	<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;';
				html += '	<input type="text" name="date_filter" value="" /> <b class="caret"></b>';
				html += '</div>';
				html += '<a id="refresh_table" data-toggle="tooltip" title="' + msGlobals.ms_payout_seller_list_refresh + '" class="btn btn-warning"><i class="fa fa-refresh" aria-hidden="true"></i></a>';

				$('.ms-payouts-topbar').siblings('div.date-filter').html(html);

				$('input[name="date_filter"]').daterangepicker({
					singleDatePicker: true,
					showDropdowns: true
				});
			}
		}

		if ($this.find('a').attr('href') == '#tab-payout-list') {
			if ($(document).find('#datepicker').length > 0)
				$('.ms-payouts-topbar').siblings('div.date-filter').empty();

			$(document).find('#list-sellers input[type="checkbox"]').prop('checked', false);
			$('#ms-create-payout').hide();
		}
	})

	$('#list-sellers').dataTable({
		"sAjaxSource": "index.php?route=multimerch/payout/getSellerTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "seller" },
			{ "mData": "balance" },
			{ "mData": "date_last_paid" }
		],
		"aaSorting": [[1, 'desc']],
		"paging": false
	});

	$(document).on('click', '#refresh_table', function() {
		$('#ms-create-payout').hide();

		if ($.fn.DataTable.isDataTable('#list-sellers')) {
			$('#list-sellers').dataTable().api().destroy();
		}

		var date_filter = $('input[name="date_filter"]').val();

		$('#list-sellers').dataTable({
			"sAjaxSource": "index.php?route=multimerch/payout/getSellerTableData&token=" + msGlobals.token + "&date_filter=" + encodeURIComponent(date_filter),
			"aoColumns": [
				{ "mData": "checkbox", "bSortable": false },
				{ "mData": "seller" },
				{ "mData": "balance" },
				{ "mData": "date_last_paid" }
			],
			"aaSorting": [[1, 'desc']],
			"paging": false
		});
	});

	$(document).on('click', '#ms-create-payout', function(e) {
		e.preventDefault();
		var selected_sellers = [];
		var error_holder = $('.alert-danger');

		$.map($('#list-sellers').children('tbody').find('input:checkbox:checked'), function(item) {
			selected_sellers.push($(item).val());
		});

		if(selected_sellers.length > 0) {
			console.log(123)
			$('#payout-form').submit();
		} else {
			error_holder.empty().append(msGlobals.ms_error_seller_notselected).show();
		}
	});

	$(document).on('click', '#list-sellers input:checkbox', function() {
		var selected_sellers = $('#list-sellers').children('tbody').find('input:checkbox:checked');
		if(selected_sellers.length > 0) {
			$('#ms-create-payout').show();
		} else {
			$('#ms-create-payout').hide();
		}
	});

	$('#datepicker').click(function() {
		$('input[name="date_filter"]').focus();
	});
});