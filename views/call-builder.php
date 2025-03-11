<?php

use Video\Infra\View;

/**
 * @var View $this
 * @var list<string> $videos
 * @var string $title
 * @var string $description
 * @var list<array{id:string,label:string,selected:string}> $preloadOptions
 * @var string $autoplay
 * @var string $loop
 * @var string $controls
 * @var string $width
 * @var string $height
 * @var string $className
 */
?>

<h1>Video – <?=$this->text('menu_main')?></h1>
<template id="video_call_builder">
  <form id="video_call_builder">
    <p>
      <label for="video_name"><?=$this->text('label_name')?></label>
      <select id="video_name">
<?php foreach ($videos as $video):?>
        <option><?=$this->esc($video)?></option>
<?php endforeach?>
      </select>
    </p>
    <p>
      <label for="video_title"><?=$this->text('label_title')?></label>
      <input id="video_title" type="text" value="<?=$this->esc($title)?>">
    </p>
    <p>
      <label for="video_description"><?=$this->text('label_description')?></label>
      <textarea id="video_description"><?=$this->esc($description)?></textarea>
    </p>
    <p>
      <label for="video_preload"><?=$this->text('label_preload')?></label>
      <select id="video_preload">
<?php foreach ($preloadOptions as $option):?>
        <option value="<?=$this->esc($option['id'])?>" <?=$this->esc($option['selected'])?>><?=$this->esc($option['label'])?></option>
<?php endforeach?>
      </select>
    </p>
    <p>
      <label for="video_autoplay"><?=$this->text('label_autoplay')?></label>
      <input id="video_autoplay" type="checkbox" <?=$this->esc($autoplay)?>>
    </p>
    <p>
      <label for="video_loop"><?=$this->text('label_loop')?></label>
      <input id="video_loop" type="checkbox" <?=$this->esc($loop)?>>
    </p>
    <p>
      <label for="video_controls"><?=$this->text('label_controls')?></label>
      <input id="video_controls" type="checkbox" <?=$this->esc($controls)?>>
    </p>
    <p>
      <label for="video_width"><?=$this->text('label_width')?></label>
      <input id="video_width" type="text" value="<?=$this->esc($width)?>">
    </p>
    <p>
      <label for="video_height"><?=$this->text('label_height')?></label>
      <input id="video_height" type="text" value="<?=$this->esc($height)?>">
    </p>
    <p>
      <label for="video_class"><?=$this->text('label_class')?></label>
      <input id="video_class" type="text" value="<?=$this->esc($className)?>">
    </p>
    <p>
      <textarea id="video_call"></textarea>
    </p>
  </form>
</template>
