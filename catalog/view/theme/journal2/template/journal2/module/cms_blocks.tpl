<div id="journal-cms-block-<?php echo $module; ?>" class="journal-cms-block-<?php echo $module_id; ?> box cms-blocks <?php echo implode(' ', $disable_on_classes); ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <style>
        #journal-cms-block-<?php echo $module; ?> .editor-content h1,
        #journal-cms-block-<?php echo $module; ?> .editor-content h2,
        #journal-cms-block-<?php echo $module; ?> .editor-content h3 {
            <?php echo $headings_style; ?>
        }

        #journal-cms-block-<?php echo $module; ?> .editor-content p {
        <?php echo $paragraphs_style; ?>
        }
    </style>
    <?php if ($title): ?>
    <div class="box-heading"><?php echo $title; ?></div>
    <?php endif; ?>
    <div class="blocks">
<?php foreach ($sections as $section): ?>
    <div class="cms-block <?php echo $grid_classes; ?>" style="<?php echo $section['block_css']; ?>">
        <?php if ($section['title']): ?>
        <h3><?php echo $section['title']; ?></h3>
        <?php endif; ?>
        <span class="block-content block-content-<?php echo $section['icon_position']; ?>" style="<?php echo $section['css']; ?>">
            <?php if ($section['has_icon']): ?>
            <div class="block-icon block-icon-<?php echo $section['icon_position']; ?>" style="<?php echo $section['icon_css']; ?>"><?php echo $section['icon']; ?></div>
            <?php endif; ?>
            <div class="editor-content" style="text-align: <?php echo $section['content_align']; ?>"> <?php echo $section['content']; ?></div>
        </span>
    </div>
<?php endforeach; ?>
</div>
</div>
<script>
    if (!Journal.isFlexboxSupported) {
        Journal.equalHeight($('#journal-cms-block-<?php echo $module; ?> .cms-block'), '.block-content');
    }
</script>