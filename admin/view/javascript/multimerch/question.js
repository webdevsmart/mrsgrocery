$(function() {


    // Latest Questions list

    $('#list-questions').dataTable({
        "sAjaxSource": $('base').attr('href') + "index.php?route=multimerch/question/getTableData&token=" + msGlobals.token,
        "aoColumns": [
            {"mData": "product_name"},
            {"mData": "customer"},
            {"mData": "answer", "bSortable": false},
            {"mData": "date_created"},
            {"mData": "actions", "bSortable": false, "sClass": "text-right"}
        ],
        "aaSorting": [[3, 'desc']]
    });

    if($('input[name="question_id"]').length > 0) {
        var question_id = $('input[name="question_id"]').val();

        $('.answers .ms-remove-answer').click(function() {
            var $this = $(this);
            var answer_id = $this.closest('.answer').find('input[name="answer_id"]').val();

            $.ajax({
                url: 'index.php?route=multimerch/question/jxDeleteAnswer&answer_id=' + answer_id + '&token=' + msGlobals.token,
                dataType: 'json',
                success: function(json) {
                    if(json.success)
                        $this.closest('.answer').remove();
                }
            });
        });
    }
});