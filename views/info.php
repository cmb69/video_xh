<?php

use Video\View;

/**
 * @var View $this
 * @var string $version
 * @var array $checks
 */
?>

<h1>Video <?=$this->escape($version)?></h1>
<div class="video_syscheck">
    <h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
    <p class="xh_<?=$this->escape($check->state)?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>
