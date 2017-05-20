<h1>Video – <?=$this->text('menu_main')?></h1>
<script type="text/x-template" id="video_call_builder">
    <div id="video_call_builder">
        <p>
            <label for="video_name"><?=$this->text('label_name')?></label>
            <select id="video_name">
<?php foreach ($this->videos as $video):?>
                <option><?=$this->escape($video)?></option>
<?php endforeach?>
            </select>
        </p>
        <p>
            <label for="video_preload"><?=$this->text('label_preload')?></label>
            <select id="video_preload">
<?php foreach ($this->preloadOptions as $option):?>
                <option value="<?=$this->escape($option->id)?>" <?=$this->escape($option->selected)?>><?=$this->escape($option->label)?></option>
<?php endforeach?>
            </select>
        </p>
        <p>
            <label for="video_autoplay"><?=$this->text('label_autoplay')?></label>
            <input id="video_autoplay" type="checkbox" <?=$this->autoplay()?>>
        </p>
        <p>
            <label for="video_loop"><?=$this->text('label_loop')?></label>
            <input id="video_loop" type="checkbox" <?=$this->loop()?>>
        </p>
        <p>
            <label for="video_controls"><?=$this->text('label_controls')?></label>
            <input id="video_controls" type="checkbox" <?=$this->controls()?>>
        </p>
        <p>
            <label for="video_width"><?=$this->text('label_width')?></label>
            <input id="video_width" type="text" value="<?=$this->width()?>">
        </p>
        <p>
            <label for="video_height"><?=$this->text('label_height')?></label>
            <input id="video_height" type="text" value="<?=$this->height()?>">
        </p>
        <p>
            <label for="video_resize"><?=$this->text('label_resize')?></label>
            <select id="video_resize">
<?php foreach ($this->resizeOptions as $option):?>
                <option value="<?=$this->escape($option->id)?>" <?=$this->escape($option->selected)?>><?=$this->escape($option->label)?></option>
<?php endforeach?>
            </select>
        </p>
        <p>
            <textarea id="video_call" readonly></textarea>
        </p>
    </div>
</script>
