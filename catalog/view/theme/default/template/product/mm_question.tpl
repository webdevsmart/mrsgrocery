<h3><?php echo $mm_question_title; ?></h3>

<form id="question-form" class="form-horizontal">
	<div class="data-container questions">
		<?php if($questions) { ?>
			<?php foreach($questions as $question) { ?>
				<div class="question panel panel-default">
					<div class="panel-heading" style="min-height: 40px;">
						<span class="pull-left">
							<?php echo $mm_question_posted_by;?> <b><?php echo $question['author_name']; ?></b>
						</span>
						<span class="pull-right">
							<b><?php echo $question['date_created']; ?></b>
						</span>
					</div>
					<div class="panel-body">
						<div class="question-text">
							<b><?php echo 'Q: '; ?></b><?php echo $question['text']; ?>
						</div>

						<div class="cl"></div>

						<div class="question-answers" style="margin-top: 15px;">
							<?php if(!empty($question['answers'])) { ?>
								<?php foreach($question['answers'] as $answer) { ?>
									<div class="answer">
										<b><?php echo 'A: '; ?></b><?php echo $answer['text']; ?>
									</div>
								<?php } ?>
							<?php } else { ?>
								<div class="answer">
									<?php echo $mm_question_no_answers; ?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<?php echo $mm_question_no_questions; ?>
		<?php } ?>
	</div>

	<div id="questions-pag" style="margin-bottom: 15px;"></div>

	<?php if(!$this->MsLoader->MsProduct->productOwnedBySeller($product_id, $this->customer->getId())) { ?>
		<div class="alert alert-danger" id="error-holder" style="display: none;"></div>
		<div class="alert alert-success" id="success-holder" style="display: none;"></div>

		<?php if($this->customer->isLogged()) { ?>
			<div class="form-group">
				<div class="col-sm-12">
					<input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
					<textarea name="question" class="form-control" placeholder="<?php echo $mm_question_ask; ?>" rows="5"></textarea>
				</div>
			</div>
			<button id="addQuestion" class="btn btn-primary"><?php echo $mm_question_submit; ?></button>
		<?php } else { ?>
			<?php echo $mm_question_signin; ?>
		<?php } ?>
	<?php } ?>
</form>