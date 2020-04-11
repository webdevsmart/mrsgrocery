<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-question-form">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $this->url->link('multimerch/question', 'token=' . $this->session->data['token']); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>

			<h1><?php echo $heading; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div style="display: none" class="alert alert-danger" id="error-holder"><i class="fa fa-exclamation-circle"></i>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading; ?></h3>
			</div>

			<div class="panel-body">
				<form id="ms-question-form" class="form-horizontal">
					<input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_question_general; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_question_edit_product; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('catalog/product/edit', 'product_id=' . $question['product_id'] . '&token=' . $this->session->data['token']); ?>" target="_blank"><?php echo $question['product_name']; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_question_edit_customer; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('customer/customer/edit', 'customer_id=' . $question['author_id'] . '&token=' . $this->session->data['token']); ?>" target="_blank"><?php echo $question['author_name']; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_question_edit_question; ?></label>
							<div class="col-sm-10">
								<p><?php echo $question['text']; ?></p>
							</div>
						</div>
					</fieldset>

					<fieldset id="mm_answers">
						<legend><?php echo $ms_question_edit_seller_answer; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_question_edit_answer; ?></label>
							<div class="col-sm-10 question-answers">
								<?php if (!empty($question['answers'])) { ?>
									<?php foreach($question['answers'] as $answer) { ?>
										<div class="answer">
											<input type="hidden" name="answer_id" value="<?php echo $answer['answer_id']; ?>" />
											<div class="body">
												<?php echo nl2br($answer['text']); ?>
											</div>
										</div>
									<?php } ?>
								<?php } else { ?>
									<div style="font-size: 14px;">
										<?php echo $ms_question_no_answers; ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>

	<script>
        var msGlobals = {
            token : "<?php echo $this->session->data['token']; ?>"
        };
	</script>
</div>
<?php echo $footer; ?>