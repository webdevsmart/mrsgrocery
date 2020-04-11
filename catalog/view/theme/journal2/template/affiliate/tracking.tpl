<?php echo $header; ?>
<div id="container" class="container j-container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="<?php echo $breadcrumb['href']; ?>" itemprop="url"><span itemprop="title"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?><?php echo $column_right; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
      <h1 class="heading-title"><?php echo $heading_title; ?></h1>
      <?php echo $content_top; ?>
      <p><?php echo $text_description; ?></p>
      <form class="form-horizontal">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-code"><?php echo $entry_code; ?></label>
          <div class="col-sm-10">
            <textarea cols="40" rows="5" placeholder="<?php echo $entry_code; ?>" id="input-code" class="form-control"><?php echo $code; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-generator"><span data-toggle="tooltip" title="<?php echo $help_generator; ?>"><?php echo $entry_generator; ?></span></label>
          <div class="col-sm-10">
            <input type="text" name="product" value="" placeholder="<?php echo $entry_generator; ?>" id="input-generator" class="form-control" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-link"><?php echo $entry_link; ?></label>
          <div class="col-sm-10">
            <textarea name="link" cols="40" rows="5" placeholder="<?php echo $entry_link; ?>" id="input-link" class="form-control"></textarea>
          </div>
        </div>
      </form>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary button"><?php echo $button_continue; ?></a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    </div>
</div>
<script type="text/javascript"><!--
$('input[name=\'product\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=affiliate/tracking/autocomplete&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['link']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'product\']').val(item['label']);
		$('textarea[name=\'link\']').val(item['value']);	
	}	
});
//--></script> 
<?php echo $footer; ?> 
