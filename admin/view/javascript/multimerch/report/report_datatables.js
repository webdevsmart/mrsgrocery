function initDatatable(id, picker) {
    if ($.fn.DataTable.isDataTable(id)) {
        $(id).dataTable().api().destroy();
    }

	var url = $('base').attr('href');
	var aoColumns = [];
	var aaSorting = [];

	switch(id) {
		case "#list-reports-sales":
			url += "index.php?route=multimerch/report/sales/getTableData&token=" + msGlobals.token;
			aoColumns = [
                { "mData": "date" },
                { "mData": "order_id" },
                { "mData": "product" },
                { "mData": "seller" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "tax" },
                { "mData": "shipping" },
                { "mData": "total" }
			];
			aaSorting = [[0, 'desc']];
			break;

        case "#list-reports-sales-day":
            url += "index.php?route=multimerch/report/sales-day/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "date" },
                { "mData": "total_sales" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "tax" },
                { "mData": "shipping" },
                { "mData": "total" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-reports-sales-month":
            url += "index.php?route=multimerch/report/sales-month/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "date" },
                { "mData": "total_sales" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "tax" },
                { "mData": "shipping" },
                { "mData": "total" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-reports-sales-product":
            url += "index.php?route=multimerch/report/sales-product/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "product" },
                { "mData": "seller" },
                { "mData": "total_sales" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "tax" },
                { "mData": "shipping" },
                { "mData": "total" }
            ];
            aaSorting = [[8, 'desc']];
            break;

        case "#list-reports-sales-seller":
            url += "index.php?route=multimerch/report/sales-seller/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "seller" },
                { "mData": "total_sales" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "total" }
            ];
            aaSorting = [[5, 'desc']];
            break;

        case "#list-reports-sales-customer":
            url += "index.php?route=multimerch/report/sales-customer/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "customer" },
                { "mData": "email" },
                { "mData": "total_orders" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "total" }
            ];
            aaSorting = [[6, 'desc']];
            break;

        case "#list-reports-finances-transaction":
            url += "index.php?route=multimerch/report/finances-transaction/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "date" },
                { "mData": "transaction_id" },
                { "mData": "seller" },
                { "mData": "description" },
                { "mData": "gross" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-reports-finances-seller":
            url += "index.php?route=multimerch/report/finances-seller/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "seller" },
                { "mData": "balance_in" },
                { "mData": "balance_out" },
                { "mData": "marketplace_earnings" },
                { "mData": "seller_earnings" },
                { "mData": "payments_received" },
                { "mData": "payouts_paid" },
                { "mData": "current_balance" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-reports-finances-payment":
            url += "index.php?route=multimerch/report/finances-payment/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "date" },
                { "mData": "payment_id" },
                { "mData": "seller" },
                { "mData": "method" },
                { "mData": "description" },
                { "mData": "gross" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-reports-finances-payout":
            url += "index.php?route=multimerch/report/finances-payout/getTableData&token=" + msGlobals.token;
            aoColumns = [
                { "mData": "date" },
                { "mData": "request_id" },
                { "mData": "seller" },
                { "mData": "method" },
                { "mData": "description" },
                { "mData": "gross" }
            ];
            aaSorting = [[0, 'desc']];
            break;

		default:
			console.error('Specified id does not exist in DOM!');
			break;
	}

	if(picker) {
		url += "&date_start=" + picker.startDate.format('MMMM D, YYYY') + "&date_end=" + picker.endDate.format('MMMM D, YYYY');
	}

	$(id).dataTable({
		"sAjaxSource": url,
		"aoColumns": aoColumns,
		"aaSorting": aaSorting
	});
}