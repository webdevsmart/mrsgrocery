<?php $type = $this->journal2->settings->get(Journal2Utils::getDevice() === 'desktop' ? 'language_display' : 'language_display_mobile', 'flag'); ?>
<?php if (count($languages) > 1): ?>
<?php
    $current_language = '';
    foreach ($languages as $language) {
        if ($language['code'] == $code) {
            switch ($type) {
                case 'flag':
                    $current_language = "<img width=\"16\" height=\"11\" src=\"" . Journal2Utils::getLanguageFlag($language) . "\" alt=\"{$language['name']}\" />";
                    break;
                case 'text':
                    $current_language = "{$language['name']}";
                    break;
                case 'full':
                    $current_language = "<img width=\"16\" height=\"11\" src=\"" . Journal2Utils::getLanguageFlag($language) . "\" alt=\"{$language['name']}\" /><span class=\"language-text\">{$language['name']}</span>";
                    break;
            }
        }
    }
?>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <div id="language" class="<?php echo $this->journal2->settings->get('language_display', 'never') === 'full' ? 'full-text' : ''; ?>">
        <div class="btn-group">
            <button class="dropdown-toggle" type="button" data-hover="dropdown">
                <div><?php echo $current_language; ?></div>
            </button>
            <ul class="dropdown-menu">
                <?php foreach ($languages as $language): ?>
                    <?php if ($type === 'flag'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $language['code']; ?>'); $(this).closest('form').submit();"><img width="16" height="11" src="<?php echo Journal2Utils::getLanguageFlag($language); ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /></a></li>
                    <?php endif; ?>
                    <?php if ($type === 'text'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $language['code']; ?>'); $(this).closest('form').submit();"><?php echo $language['name']; ?></a></li>
                    <?php endif; ?>
                    <?php if ($type === 'full'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $language['code']; ?>'); $(this).closest('form').submit();"><img width="16" height="11" src="<?php echo Journal2Utils::getLanguageFlag($language); ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><span class="language-text"><?php echo $language['name']; ?></span></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <input type="hidden" name="code" value="" />
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
    </div>
</form>
<?php endif; ?>
