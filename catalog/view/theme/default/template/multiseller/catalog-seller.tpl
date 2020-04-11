<?php echo $header; ?>
<div class="container catalog-seller">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<?php if (isset($sellers) && $sellers) { ?>
			<div class="row">
				<div class="col-sm-12">
					<div class="row mm-product-filter">
						<div class="col-sm-6">
                            <?php if ($this->config->get('msconf_google_api_key')){ ?>
                                <div class="btn-group">
                                    <button type="button" id="ms-grid-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th"></i></button>
                                    <button type="button" id="ms-map-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $ms_catalog_sellers_map_view; ?>"><i class="fa fa-map-marker"></i></button>
                                </div>
                            <?php } ?>
						</div>
						<div class="mm_top_products_right col-sm-6">
							<div class="mm_sort_group">
								<label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>
								<select id="input-sort" class="form-control" onchange="location = this.value;" >
									<?php foreach ($sorts as $sorts) { ?>
									<?php if ($sorts['value'] == $sort . '-' . $order) { ?>
									<option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class="mm_sort_group">
								<label class="control-label" for="input-limit"><?php echo $text_limit; ?></label>
								<select id="input-limit" class="form-control" onchange="location = this.value;">
									<?php foreach ($limits as $limits) { ?>
									<?php if ($limits['value'] == $limit) { ?>
									<option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div id="ms-sellers-panel-grid" style="display: none">
						<div class="row ms-sellers-panel">
							<?php foreach ($sellers as $seller) { ?>
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 one-seller-panel">
								<div class="panel panel-default mm-one-seller">
									<div class="panel-heading" style="background-image: url('<?php echo $seller['banner']; ?>');"></div>
									<div class="panel-body text-center">
										<a href="<?php echo $seller['href']; ?>"><img class="panel-profile-img" src="<?php echo $seller['thumb']; ?>" title="<?php echo $seller['nickname']; ?>" alt="<?php echo $seller['nickname']; ?>"></a>
										<h5 class="panel-title"><a href="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></a></h5>
										<ul class="list-unstyled">
											<li class="seller-country"><?php echo trim($seller['settings']['slr_city'] . ', ' . $seller['settings']['slr_country'], ',') ;?></li>
											<li class="seller-website"><a target="_blank" href="<?php echo $seller['settings']['slr_website'] ;?>"><?php echo $seller['settings']['slr_website'] ;?></a></li>
											<li class="seller-total-products"><a href="<?php echo $seller['products_href']; ?>"><?php echo $seller['total_products']; ?> <?php echo mb_strtolower($ms_account_products);?></a></li>
										</ul>
										<div class="mm-one-seller-products">
											<?php foreach($seller['products'] as $product) :?>
											<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
												<a href="<?php echo $product['href'] ;?>"><img class="img-thumbnail img-responsive" src="<?php echo $product['p.image'] ? $product['p.image'] : '' ;?>"></a>
											</div>
											<?php endforeach ;?>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
						</div>
					</div>
					<div id="ms-sellers-panel-map" style="display: none">
						<div class="ms-sellers-panel">
							<div id="map_canvas" style="width: 100%; height: 450px;"></div>
						</div>
					</div>
				</div>
			</div>
			<?php } else { ?>
			<div class="content"><?php echo $ms_catalog_sellers_empty; ?></div>
			<div class="buttons">
				<div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
			</div>
			<?php } ?>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<script>
	var msGlobals = {
		google_api_key: '<?php echo $this->config->get("msconf_google_api_key"); ?>'
	};
	$(function(){
		var url = document.location.toString();
		if (url.match('#map-view')) {
			$('#ms-grid-view').removeClass('active');
			$('#ms-map-view').addClass('active');
			$('#ms-sellers-panel-grid').hide();
			$.getScript('catalog/view/javascript/multimerch/ms-google-map.js', function(){
				$('#ms-sellers-panel-map').show();
			});
		}else{
			$('#ms-grid-view').addClass('active');
			$('#ms-sellers-panel-grid').show();
		}
		$('#ms-map-view').click(function() {
			$('#ms-grid-view').removeClass('active');
			$('#ms-map-view').addClass('active');
			$('#ms-sellers-panel-grid').hide();
			if ($('#map_canvas').html().trim() === ''){
				$.getScript('catalog/view/javascript/multimerch/ms-google-map.js', function(){
					$('#ms-sellers-panel-map').show();
				});
			}else{
				$('#ms-sellers-panel-map').show();
			}
		});
		$('#ms-grid-view').click(function() {
			$('#ms-map-view').removeClass('active');
			$('#ms-grid-view').addClass('active');
			$('#ms-sellers-panel-map').hide();
			$('#ms-sellers-panel-grid').show();
		});
	});
</script>
<?php echo $footer; ?>