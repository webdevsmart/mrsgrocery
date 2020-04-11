<!--microdata open graph start-->
<meta property="og:type" content="product">
<meta property="og:title" content="<?php echo $ms_microdata_name; ?>">
<meta property="og:url" content="<?php echo $ms_microdata_url; ?>">
<meta property="og:image" content="<?php echo $ms_microdata_popup; ?>">
<!--microdata open graph end -->

<!--microdata JSON-LD start-->
<script type="application/ld+json">
{
"@context": "http://schema.org/",
	"@type": "Product",
	<?php if($ms_microdata_manufacturer){ ?>
		"brand": {
			"@type": "Brand",
			"name": "<?php echo $ms_microdata_manufacturer; ?>"
		},
	<?php } ?>

	<?php if($ms_microdata_rating){ ?>
		"aggregateRating": {
		"@type": "AggregateRating",
		"ratingValue": "<?php echo $ms_microdata_rating['ratingValue']; ?>",
		"reviewCount": "<?php echo $ms_microdata_rating['reviewCount']; ?>"
		},
	<?php } ?>

	"name": "<?php echo $ms_microdata_name; ?>",
	"description": "<?php echo $ms_microdata_description; ?>",
	"model": "<?php echo $ms_microdata_model; ?>",
	"sku": "<?php echo $ms_microdata_sku; ?>",
	"url": "<?php echo $ms_microdata_url; ?>",
	"image": "<?php echo $ms_microdata_popup; ?>",

	"offers": {
		"@type": "Offer",
		<?php if($ms_microdata_seller){ ?>
			"offeredBy": {
				"@type": "<?php echo $ms_microdata_seller['type']; ?>",
				"name": "<?php echo $ms_microdata_seller['nickname']; ?>"
			},
		<?php } ?>
		"priceCurrency": "<?php echo $ms_microdata_currency_code; ?>",
		"price": "<?php echo $ms_microdata_price; ?>",
		"availability": "http://schema.org/<?php echo ($ms_microdata_stock > 0) ? "InStock" : "OutOfStock"; ?>"
	}
}
</script>
<!--microdata JSON-LD end -->
