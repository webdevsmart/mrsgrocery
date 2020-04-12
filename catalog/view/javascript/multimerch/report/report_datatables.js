function initDatatable(id, picker) {
    if ($.fn.DataTable.isDataTable(id)) {
        $(id).dataTable().api().destroy();
    }

	var url = $('base').attr('href');
	var aoColumns = [];
	var aaSorting = [];

	switch(id) {
		case "#list-report-sales":
			url += "index.php?route=seller/report/sales/getTableData";
			aoColumns = [
				{ "mData": "date" },
				{ "mData": "order_id" },
				{ "mData": "product" },
				{ "mData": "gross" },
				{ "mData": "net_marketplace" },
				{ "mData": "net_seller" },
				{ "mData": "tax" },
				{ "mData": "shipping" },
				{ "mData": "total" }
			];
			aaSorting = [[0, 'desc']];
			break;

        case "#list-report-sales-day":
            url += "index.php?route=seller/report/sales-day/getTableData";
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

        case "#list-report-sales-month":
            url += "index.php?route=seller/report/sales-month/getTableData";
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

        case "#list-report-sales-product":
            url += "index.php?route=seller/report/sales-product/getTableData";
            aoColumns = [
                { "mData": "product" },
                { "mData": "total_sales" },
                { "mData": "gross" },
                { "mData": "net_marketplace" },
                { "mData": "net_seller" },
                { "mData": "tax" },
                { "mData": "shipping" },
                { "mData": "total" }
            ];
            aaSorting = [[7, 'desc']];
            break;

        case "#list-report-finances-transaction":
            url += "index.php?route=seller/report/finances-transaction/getTableData";
            aoColumns = [
                { "mData": "date" },
                { "mData": "transaction_id" },
                { "mData": "description" },
                { "mData": "gross" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-report-finances-payment":
            url += "index.php?route=seller/report/finances-payment/getTableData";
            aoColumns = [
                { "mData": "date" },
                { "mData": "payment_id" },
                { "mData": "method" },
                { "mData": "description" },
                { "mData": "gross" }
            ];
            aaSorting = [[0, 'desc']];
            break;

        case "#list-report-finances-payout":
            url += "index.php?route=seller/report/finances-payout/getTableData";
            aoColumns = [
                { "mData": "date" },
                { "mData": "request_id" },
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