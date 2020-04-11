<div id="multi-module-<?php echo $module; ?>" class="multi-module-<?php echo $module_id; ?> multi-modules-wrapper <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?> <?php echo implode(' ', $disable_on_classes); ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <div class="box multi-modules" style="height:<?php echo $height; ?>px; <?php echo $module_spacing ? 'margin-right: -' . $module_spacing : ''; ?>">
        <?php foreach ($columns as $column): ?>
            <div class="multi-modules-column <?php echo $column['classes']; ?>" style="<?php echo $module_spacing ? 'padding-right: ' . $module_spacing : ''; ?>">
                <?php $row_index = 0; foreach ($column['modules'] as $m): ?>
                    <?php if ($row_index === count($column['modules']) - 1): ?>
                        <div class="multi-modules-row <?php echo implode(' ', $m['class']); ?>" data-ratio="<?php echo $m['height']; ?>" style="height: calc(100% / <?php echo (100 / $m['height']);?> - <?php echo $module_spacing?>);">
                            <?php echo $m['content']; ?>
                        </div>
                    <?php else: ?>
                        <div class="multi-modules-row <?php echo implode(' ', $m['class']); ?>" data-ratio="<?php echo $m['height']; ?>" style="height: calc(100% / <?php echo (100 / $m['height']);?> - <?php echo $module_spacing?>); <?php echo $module_spacing ? 'margin-bottom: ' . $module_spacing : ''; ?>">
                            <?php echo $m['content']; ?>
                        </div>
                    <?php endif; ?>
                    <?php $row_index++; endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php $sw        = $this->journal2->settings->get('site_width', 1024) ?>
    <?php $ratio     = $height / $sw ?>

    <style>

        @media only screen and (min-width: <?php echo $sw; ?>px){
            #top-modules .multi-modules-wrapper,
            #bottom-modules .multi-modules-wrapper{
                margin-bottom:-<?php echo $module_spacing?> !important;
            }
        }
        @media only screen and (max-width: <?php echo $sw; ?>px){
            #multi-module-<?php echo $module; ?> .multi-modules{
                height: calc(100% * <?php echo $ratio;?>) !important;
            }
        }
        @media only screen and (max-width: 760px){
            #multi-module-<?php echo $module; ?> .multi-modules,
            #multi-module-<?php echo $module; ?> .multi-modules-column,
            #multi-module-<?php echo $module; ?> .multi-modules-row{
                height: auto !important;
            }
            <?php foreach ($columns as $i => $column): ?>
            #multi-module-<?php echo $module; ?> .multi-modules-column:nth-child(<?php echo $i + 1; ?>) .row-rs{
               /* height: calc(100vw * <?php echo $height / ($sw * $column['width'] / 100 + 40);?>) !important; */
            }
            <?php endforeach; ?>
            #multi-module-<?php echo $module; ?> .column-banner .multi-modules-row:first-of-type:not(:only-of-type){
                margin-bottom:<?php echo $module_spacing; ?>;
            }
        }
    </style>
    <?php if ($grid_dimensions && $this->journal2->html_classes->hasClass('is-admin')): ?>
    <script>
        (function(){
            var $row = $('.multi-modules-row');
            $row.find('.container-dimensions').remove();
            $row.each(function(){
                $(this).append('<span class="container-dimensions">' + $(this).width() + 'x' + $(this).height() + '</span>');
            });
        })();
    </script>
    <?php endif; ?>
</div>

