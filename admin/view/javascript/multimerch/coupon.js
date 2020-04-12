$(function() {

	$('#list-coupons').dataTable({
		"sAjaxSource": "index.php?route=multimerch/coupon/getCouponTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "date_created" },
			{ "mData": "name" },
			{ "mData": "code" },
			{ "mData": "seller" },
			{ "mData": "value" },
			{ "mData": "total_uses" },
			{ "mData": "date_start" },
			{ "mData": "date_end" },
			{ "mData": "status" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		]
	});

	$(document).on('click', '#list-coupons input:checkbox', function() {
		var selected_coupons = $('#list-coupons').children('tbody').find('input:checkbox:checked');
		if(selected_coupons.length > 0) {
			$('#ms-coupon-delete').show();
		} else {
			$('#ms-coupon-delete').hide();
		}
	});
});