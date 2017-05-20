<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
    <meta itemprop="name" content="<?=$this->title()?>">
    <meta itemprop="description" content="<?=$this->description()?>">
    <meta itemprop="contentURL" content="<?=$this->contentUrl()?>">
<?php if (isset($this->thumbnailUrl)):?>
    <meta itemprop="thumbnailUrl" content="<?=$this->thumbnailUrl()?>">
<?php endif?>
    <meta itemprop="uploadDate" content="<?=$this->uploadDate()?>">
    <video class="<?=$this->className()?>" <?=$this->attributes()?>>
<?php foreach ($this->sources as $source):?>
        <source src="<?=$this->escape($source->url)?>" type="video/<?=$this->escape($source->type)?>">
<?php endforeach?>
<?php if ($this->track):?>
        <track src="<?=$this->track()?>" srclang="<?=$this->langCode()?>" label="<?=$this->text('subtitle_label')?>">
<?php endif?>
        <a href="<?=$this->filename()?>"><?=$this->downloadLink()?></a>
    </video>
</div>
