<div id="journal-accordion-<?php echo $module; ?>" class="journal-accordion journal-accordion-<?php echo $module_id; ?> box <?php echo implode(' ', $disable_on_classes); ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <?php if ($title): ?>
    <div class="box-heading"><?php echo $title; ?></div>
    <?php endif; ?>
    <div data-accordion-group>
    <?php foreach ($sections as $section): ?>
    <div data-accordion>
        <div class="journal-accordion-heading" data-control><?php echo $section['title']; ?></div>
        <div class="journal-accordion-content" data-content style="text-align: <?php echo $section['content_align']; ?>"><div><?php echo $section['content']; ?></div></div>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<script>
    $(function() {
        $('#journal-accordion-<?php echo $module; ?> [data-accordion]').accordion2({
            "transitionSpeed": 400,
            "singleOpen": <?php echo $close_others? 'true' : 'false'; ?>
        });
    });
</script>