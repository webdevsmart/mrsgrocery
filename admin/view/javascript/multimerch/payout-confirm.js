$(function() {
	if($('ul.sellers').length) {
		$('#ms-confirm-payout').show();
	}

	$(document).on('click', '#ms-confirm-payout', function() {
		var error_holder = $('.alert-danger');

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/payment-request/jxCreate&token=' + msGlobals.token,
			data: $('#payout-confirm-form').serialize(),
			beforeSend: function() {
				error_holder.empty();
			},
			success: function (json) {
				if(json.success) {
					window.location.href = $('base').attr('href') + "index.php?route=multimerch/payout&token=" + msGlobals.token;
				} else if (json.errors) {
					$.map(json.errors, function(error){
						error_holder.append(error + '<BR>');
					});
					error_holder.show();
				}
			}
		});
	});
});