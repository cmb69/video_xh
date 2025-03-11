<?php

use Plib\View;

/**
 * @var View $this
 * @var string $version
 * @var list<array{class:string,label:string,stateLabel:string}> $checks
 */
?>

<h1>Video <?=$this->esc($version)?></h1>
<div class="video_syscheck">
  <h2><?=$this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="<?=$this->esc($check['class'])?>"><?=$this->text('syscheck_message', $check['label'], $check['stateLabel'])?></p>
<?endforeach?>
</div>
