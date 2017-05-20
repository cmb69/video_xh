<video class="video_video" <?=$this->attributes()?>>
<?php foreach ($this->sources as $source):?>
    <source src="<?=$this->escape($source->url)?>" type="video/<?=$this->escape($source->type)?>">
<?php endforeach?>
<?php if ($this->track):?>
    <track src="<?=$this->track()?>" srclang="<?=$this->langCode()?>" label="<?=$this->text('subtitle_label')?>">
<?php endif?>
    <a href="<?=$this->filename()?>"><?=$this->downloadLink()?></a>
</video>
