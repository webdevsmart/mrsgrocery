$(function() {


    // Latest Reviews list

    $('#list-reviews').dataTable( {
        "sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-review/getTableData",
        "aoColumns": [
            { "mData": "product_name" },
            { "mData": "rating" },
            { "mData": "comment", "bSortable": false },
            { "mData": "date_created" },
            { "mData": "status" },
            { "mData": "actions", "bSortable": false, "sClass": "text-right" }
        ],
        "aaSorting":  [[3,'desc']]
    });

    $(document).on('click', '.icon-remove.ms_remove_review', function(e) {
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

    // Review edit form
    if($('input[name="review_id"]').length > 0) {
        $('.thumbnails').magnificPopup({
            type:'image',
            delegate: 'a',
            gallery: {
                enabled:true
            }
        });

        var review_id = $('input[name="review_id"]').val();
        var $review_comments = $('.review-comments');

        $review_comments.load('index.php?route=multimerch/product_review/jxGetReviewComments&review_id=' + parseInt(review_id));

        $(document).on('click', '#comment-btn', function(e) {
            e.preventDefault();

            var $form = $(this).closest('form');

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: "index.php?route=multimerch/product_review/jxAddReviewComment",
                data: {review_id: review_id, text: $form.find('textarea[name="text"]').val()},
                success: function (json) {
                    if(json.errors) {
                        for(var i = 0, length = json.errors.length; i < length; i++) {
                            console.error(json.errors[i]);
                        }
                    } else if(json.success) {
                        $review_comments.load('index.php?route=multimerch/product_review/jxGetReviewComments&review_id=' + parseInt(review_id));
                    }
                }
            });
        });
    }
});