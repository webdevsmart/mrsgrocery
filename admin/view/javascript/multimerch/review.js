$(function() {


    // Latest Reviews list

    $('#list-reviews').dataTable({
        "sAjaxSource": $('base').attr('href') + "index.php?route=multimerch/review/getTableData&token=" + msGlobals.token,
        "aoColumns": [
            {"mData": "product_name"},
            {"mData": "customer"},
			{"mData": "nickname"},
            {"mData": "order"},
            {"mData": "rating"},
            {"mData": "comment", "bSortable": false},
            {"mData": "date_created"},
            {"mData": "status"},
            {"mData": "actions", "bSortable": false, "sClass": "text-right"}
        ],
        "aaSorting": [[5, 'desc']]
    });

    if($('input[name="review_id"]').length > 0) {
        var review_id = $('input[name="review_id"]').val();

        $('.attachments .ms-remove-image').click(function() {
            var $this = $(this);
            var attachment_id = $this.siblings('input[name="review_attachment_id"]').val();

            $.ajax({
                url: 'index.php?route=multimerch/review/jxDeleteAttachment&review_attachment_id=' + attachment_id + '&token=' + msGlobals.token,
                dataType: 'json',
                success: function(json) {
                    if(json.success)
                        $this.closest('li').remove();
                }
            });
        });

        $('.review-comments .ms-remove-comment').click(function() {
            var $this = $(this);
            var comment_id = $this.closest('.review-comment').find('input[name="review_comment_id"]').val();

            $.ajax({
                url: 'index.php?route=multimerch/review/jxDeleteComment&comment_id=' + comment_id + '&token=' + msGlobals.token,
                dataType: 'json',
                success: function(json) {
                    if(json.success)
                        $this.closest('.review-comment').remove();
                }
            });
        });
    }
});