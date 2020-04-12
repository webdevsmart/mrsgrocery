<div class="row">
	<div class="data-container col-sm-12">
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
				<?php echo $mm_question_no_answers; ?>
			</div>
		<?php } ?>
	</div>
</div>

<?php if($this->MsLoader->MsProduct->productOwnedBySeller($question['product_id'], $this->customer->getId()) && empty($question['answers'])) { ?>
	<div class="row">
		<?php if(!$is_logged) { ?>
			<div class="col-sm-12">
				<?php echo $mm_question_signin; ?>
			</div>
		<?php } else { ?>
			<form id="ms-question-answer-form" class="ms-form form-horizontal">
				<div class="col-sm-12">
					<textarea class="form-control" rows="3" name="text" placeholder="<?php echo $mm_question_answers_textarea_placeholder; ?>" style="margin-top: 10px;"></textarea>
					<div class="buttons text-left">
						<button type="button" class="btn btn-default" id="answer-btn"><?php echo $mm_question_submit; ?></button>
					</div>
				</div>
			</form>
		<?php } ?>
	</div>
<?php } ?>
