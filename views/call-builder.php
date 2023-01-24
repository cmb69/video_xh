<?php

use Video\View;

/**
 * @var View $this
 * @var array<string> $videos
 * @var string $title
 * @var string $description
 * @var array<stdClass> $preloadOptions
 * @var string $autoplay
 * @var string $loop
 * @var string $controls
 * @var string $width
 * @var string $height
 * @var string $className
 */
?>

<h1>Video – <?=$this->text('menu_main')?></h1>
<script type="text/x-template" id="video_call_builder">
    <form id="video_call_builder">
        <p>
            <label for="video_name"><?=$this->text('label_name')?></label>
            <select id="video_name">
<?php foreach ($videos as $video):?>
                <option><?=$this->escape($video)?></option>
<?php endforeach?>
            </select>
        </p>
        <p>
            <label for="video_title"><?=$this->text('label_title')?></label>
            <input id="video_title" type="text" value="<?=$this->escape($title)?>">
        </p>
        <p>
            <label for="video_description"><?=$this->text('label_description')?></label>
            <textarea id="video_description"><?=$this->escape($description)?></textarea>
        </p>
        <p>
            <label for="video_preload"><?=$this->text('label_preload')?></label>
            <select id="video_preload">
<?php foreach ($preloadOptions as $option):?>
                <option value="<?=$this->escape($option->id)?>" <?=$this->escape($option->selected)?>><?=$this->escape($option->label)?></option>
<?php endforeach?>
            </select>
        </p>
        <p>
            <label for="video_autoplay"><?=$this->text('label_autoplay')?></label>
            <input id="video_autoplay" type="checkbox" <?=$this->escape($autoplay)?>>
        </p>
        <p>
            <label for="video_loop"><?=$this->text('label_loop')?></label>
            <input id="video_loop" type="checkbox" <?=$this->escape($loop)?>>
        </p>
        <p>
            <label for="video_controls"><?=$this->text('label_controls')?></label>
            <input id="video_controls" type="checkbox" <?=$this->escape($controls)?>>
        </p>
        <p>
            <label for="video_width"><?=$this->text('label_width')?></label>
            <input id="video_width" type="text" value="<?=$this->escape($width)?>">
        </p>
        <p>
            <label for="video_height"><?=$this->text('label_height')?></label>
            <input id="video_height" type="text" value="<?=$this->escape($height)?>">
        </p>
        <p>
            <label for="video_class"><?=$this->text('label_class')?></label>
            <input id="video_class" type="text" value="<?=$this->escape($className)?>">
        </p>
        <p>
            <textarea id="video_call"></textarea>
        </p>
    </form>
</script>
