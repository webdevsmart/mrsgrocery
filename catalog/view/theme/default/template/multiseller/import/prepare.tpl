<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php if (isset($import_result) && $import_result) { ?>
	<div class="alert alert-success"><?php echo $import_result; ?></div>
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
		<div id="content" class="<?php echo $class; ?> import_content"><?php echo $content_top; ?>
			<div class="mm_dashboard">
				<h1><?php echo $this->language->get('ms_import_text_header'); ?></h1>
				<div class="tabslide">
					<ul class="nav nav-wizard tabs">
						<li class="tab active"><a href="#"><?php echo $this->language->get('ms_import_text_title_step1'); ?></a></li>
						<li class="tab"><a href="#"><?php echo $this->language->get('ms_import_text_title_step2'); ?></a></li>
						<li class="tab"><a href="#"><?php echo $this->language->get('ms_import_text_title_step3'); ?></a></li>
					</ul>
					<div class="slider">
						<div id="slider">
							<div id="step1"></div>
							<div id="step2"></div>
							<div id="step3"></div>
						</div>
					</div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>