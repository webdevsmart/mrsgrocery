<?php echo $header; ?>
<div class="container">

	<?php if (isset($success) && $success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<div class="alert alert-danger" id="error-holder" style="display: none;"></div>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
			<div class="mm_dashboard question">
				<h1><?php echo $heading; ?></h1>

				<form id="ms-new-question" class="tab-content ms-question form-horizontal">
					<input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_account_editquestion_question; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editquestion_product; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('product/product', 'product_id=' . $question['product_id']); ?>" target="_blank"><?php echo $question['product_name']; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editquestion_customer; ?></label>
							<div class="col-sm-10">
								<p><?php echo $question['author_name']; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editquestion_question; ?></label>
							<div class="col-sm-10">
								<p><?php echo $question['text']; ?></p>
							</div>
						</div>
					</fieldset>

					<fieldset id="mm_answers">
						<div class="alert alert-success" id="success-holder" style="display: none;"></div>

						<legend><?php echo $ms_account_editquestion_your_answer; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editquestion_answer; ?></label>
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
										<?php echo $ms_account_question_no_answers; ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</fieldset>
				</form>

				<div class="buttons">
					<div class="pull-left"><a class="btn btn-default" href="<?php echo $back; ?>"><span><?php echo $ms_button_back; ?></span></a></div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>
