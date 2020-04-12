<div id="cs-<?php echo $module; ?>" class="cs-<?php echo $module_id; ?> box custom-sections section-brand <?php echo implode(' ', $disable_on_classes); ?> <?php echo $single_class; ?> <?php echo $show_title_class; ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <?php if ($spacing): ?>
        <style>
            #cs-<?php echo $module; ?> .product-grid-item {
                margin-bottom: <?php echo $spacing; ?>px;
            }

            #cs-<?php echo $module; ?> .product-wrapper {
                margin-right: <?php echo $spacing; ?>px;
            }

            #cs-<?php echo $module; ?> .product-grid, #content #cs-<?php echo $module; ?> .box-product {
                padding-top: <?php echo $spacing; ?>px;
                margin-right: -<?php echo $spacing; ?>px;
            }
        </style>
    <?php endif; ?>
    <?php if ($show_title): ?>
    <div class="box-heading box-sections box-block">
        <ul>
            <?php foreach ($sections as $section): ?>
            <?php if ($section['is_link']): ?>
            <li><a href="<?php echo $section['url']; ?>" <?php echo $section['target']; ?>><?php echo $section['section_name']; ?></a></li>
            <?php else: ?>
            <?php if (!count($section['items'])) continue; ?>
            <li><a href="javascript:;" data-option-value="section-<?php echo $section['section_class']; ?>"><?php echo $section['section_name']; ?></a></li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="box-content">
        <div class="product-grid">
            <?php foreach ($items as $item) { ?>
            <div class="product-grid-item isotope-element <?php echo implode(' ', $item['section_class']); ?> <?php echo $grid_classes; ?>">
                <div class="product-wrapper" data-respond="start: 150px; end: 300px; interval: 20px;" style="<?php echo $image_bgcolor; ?>">
                    <?php if (isset($item['thumb'])) { ?>
                    <div class="image">
                        <a href="<?php echo $item['href']; ?>" style="<?php echo $image_border_css; ?>">
                            <img class="first-image" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $item['thumb']; ?>" title="<?php echo $item['name']; ?>" alt="<?php echo $item['name']; ?>" />
                        </a>
                    </div>
                    <?php } ?>
                    <?php if ($brand_name): ?>
                    <div class="product-details">
                        <div class="name"><a href="<?php echo $item['href']; ?>"><?php echo $item['name']; ?></a></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <script>
        (function(){
            var $isotope = $('#cs-<?php echo $module; ?> .product-grid');
            var $filters = $('#cs-<?php echo $module; ?> .box-heading a[data-option-value]');
            var default_section = '<?php echo count($sections) > 1 ? $default_section : ''; ?>';


            $isotope.each(function () {
                Journal.equalHeight($(this).find('.product-grid-item'), '.name');
            });

            $('#cs-<?php echo $module; ?> img').imagesLoaded(function () {
                $isotope.isotope({
                    itemSelector: '.isotope-element',
                    initLayout: default_section === ''
                });
            });

            $filters.click(function () {
                var $this = $(this);
                if ($this.hasClass('selected')) {
                    return false;
                }
                $filters.removeClass('selected');
                $this.addClass('selected');
                $isotope.isotope({
                    filter: '.' + $this.attr('data-option-value')
                })
            });

            if (default_section !== '') {
                $('#cs-<?php echo $module; ?> .box-heading a[data-option-value="section-' + default_section + '"]').click();
            } else {
                $('#cs-<?php echo $module; ?> .box-heading a').first().click();
            }
        }());
    </script>
</div>
