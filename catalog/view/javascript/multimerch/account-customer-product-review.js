$(document).on('ready', function() {
    var maxlength = parseInt($('#rating-comment').attr('maxlength'));
    $(".rating-comment-note").html(maxlength + " " + msGlobals.ms_customer_product_rate_characters_left);

    $(document).on('keyup', '#rating-comment', function() {
        if(this.value.length > maxlength) {
            return false;
        }
        $(".rating-comment-note").html((maxlength - this.value.length) + " " + msGlobals.ms_customer_product_rate_characters_left);
    });

    // Form validation
    $(document).on('click', '.form-rate-submit', function(e) {
        if($('#rating-input-xs').val() == 0 || $('#rating-comment').val() == '') {
            e.preventDefault();
            $('.form-rate-error').html(msGlobals.ms_customer_product_rate_form_error);
            $('.form-rate-error').show();
        } else {
            $('.form-rate-error').hide();
        }
    });

    // Attachments upload
    $(document).on('click', '.image-holder .ms-remove', function() {
        var holder = $(this).closest('#image-holder');
        $(this).parent().remove();
        if(!holder.find('.image-holder').length){
            $('#ms-image').show();
        }
    });

    var images = initUploader('ms-image', 'index.php?route=customer/review/jxAddUpload', 'large', true, false);

});

