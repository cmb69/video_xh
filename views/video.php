<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
    <meta itemprop="name" content="<?=$this->escape($this->title)?>">
    <meta itemprop="description" content="<?=$this->escape($this->description)?>">
    <meta itemprop="contentURL" content="<?=$this->escape($this->contentUrl)?>">
<?php if (isset($this->thumbnailUrl)):?>
    <meta itemprop="thumbnailUrl" content="<?=$this->escape($this->thumbnailUrl)?>">
<?php endif?>
    <meta itemprop="uploadDate" content="<?=$this->escape($this->uploadDate)?>">
    <video class="<?=$this->escape($this->className)?>" <?=$this->escape($this->attributes)?>>
<?php foreach ($this->sources as $source):?>
        <source src="<?=$this->escape($source->url)?>" type="video/<?=$this->escape($source->type)?>">
<?php endforeach?>
<?php if ($this->track):?>
        <track src="<?=$this->escape($this->track)?>" srclang="<?=$this->escape($this->langCode)?>" label="<?=$this->text('subtitle_label')?>">
<?php endif?>
        <a href="<?=$this->escape($this->filename)?>"><?=$this->escape($this->downloadLink)?></a>
    </video>
</div>
