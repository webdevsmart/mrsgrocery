$(function() {

	$('#list-coupons').dataTable( {
		"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-coupon/getTableData",
		"aoColumns": [
			{ "mData": "code" },
			{ "mData": "value" },
			{ "mData": "total_uses"},
			{ "mData": "date_start" },
			{ "mData": "date_end" },
			{ "mData": "status" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
		]
	});

	$(document).on('click', '.icon-remove.ms_remove_coupon', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');

		if(confirm('Are you sure?')) {
			$.ajax({
				type: "get",
				dataType: "json",
				url: url,
				beforeSend: function () {
					if ($('.alert-success').length > 0)
						$('.alert-success').text('').hide();
				},
				success: function (json) {
					if (json.error) {
						$('#error-holder').text('').append(json.error + '<BR>').show();
					} else {
						window.location.reload();
					}
				}
			})
		}
	});

});