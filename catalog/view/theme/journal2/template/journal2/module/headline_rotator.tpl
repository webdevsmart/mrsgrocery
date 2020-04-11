<div id="journal-headline-rotator-<?php echo $module; ?>" class="journal-headline-rotator-<?php echo $module_id; ?> journal-rotator headline-mode box <?php echo implode(' ', $disable_on_classes); ?> bullets-<?php echo $bullets ? $bullets_position : ''; ?> align-<?php echo $text_align; ?> bullets-<?php echo $bullets ? 'on' : 'off'; ?>" style="display: none; <?php echo $rotator_css; ?>; <?php echo isset($css) ? $css : ''; ?>">
    <?php foreach ($sections as $section): ?>
    <div class="quote <?php echo $section['text'] ? '' : 'no-text'; ?> <?php echo $section['cta'] ? 'has-cta' : ''; ?> <?php echo $section['cta'] && $section['cta_position'] ? 'cta-' . $section['cta_position'] : ''; ?>" style="<?php echo $quote_css; ?>;">
        <div style="min-height:<?php echo $headline_rotator_height?>px;">
        <?php if ($section['cta'] && $section['cta_position'] === 'left'): ?>
        <?php if ($section['cta_link']): ?>
        <a href="<?php echo $section['cta_link']; ?>" class="cta button button-left" <?php echo $section['cta_target']; ?> style="<?php echo $section['cta_style']; ?>"><?php echo $section['cta_icon']; ?><?php echo $section['cta_text']; ?></a>
        <?php else: ?>
        <a class="cta button" style="<?php echo $section['cta_style']; ?>"><?php echo $section['cta_icon']; ?><?php echo $section['cta_text']; ?></a>
        <?php endif; ?>
        <?php endif; ?>
        <div class="rotator-text <?php echo $section['line_through'] ? 'line-through' : ''; ?>"><?php echo $section['icon']; ?><span style="<?php echo $section['style']; ?>"><?php echo $section['text']; ?></span></div>
        <?php if ($section['cta'] && $section['cta_position'] !== 'left'): ?>
            <?php if ($section['cta_link']): ?>
            <a href="<?php echo $section['cta_link']; ?>" class="cta button button-<?php echo $section['cta_position']; ?> button-icon-<?php echo $section['cta_icon_position']; ?>" <?php echo $section['cta_target']; ?> style="<?php echo $section['cta_style']; ?>"><?php echo $section['cta_icon']; ?><?php echo $section['cta_text']; ?></a>
            <?php else: ?>
            <a class="cta button button-icon-<?php echo $section['cta_icon_position']; ?>" style="<?php echo $section['cta_style']; ?>"><?php echo $section['cta_icon']; ?><?php echo $section['cta_text']; ?></a>
            <?php endif; ?>
        <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<script>
    (function () {
        var single_quote = parseInt('<?php echo count($sections); ?>', 10) <= 1;

        $('#journal-headline-rotator-<?php echo $module; ?>').show().quovolver({
            children        : '.quote',
            equalHeight     : false,
            navPosition     : single_quote ? '' : 'below',
            navNum          : '<?php echo $bullets; ?>' ? true : false,
            pauseOnHover    : parseInt('<?php echo $pause_on_hover; ?>', 10) ? true : false,
            autoPlay        : !single_quote,
            autoPlaySpeed   : '<?php echo $transition_delay; ?>',
            transitionSpeed : 300
        });
    })();
</script>