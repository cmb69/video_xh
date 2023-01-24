<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
    <meta itemprop="name" content="<?=$this->escape($title)?>">
    <meta itemprop="description" content="<?=$this->escape($description)?>">
    <meta itemprop="contentURL" content="<?=$this->escape($contentUrl)?>">
<?php if (isset($thumbnailUrl)):?>
    <meta itemprop="thumbnailUrl" content="<?=$this->escape($thumbnailUrl)?>">
<?php endif?>
    <meta itemprop="uploadDate" content="<?=$this->escape($uploadDate)?>">
    <video class="<?=$this->escape($className)?>" <?=$this->escape($attributes)?>>
<?php foreach ($sources as $source):?>
        <source src="<?=$this->escape($source->url)?>" type="video/<?=$this->escape($source->type)?>">
<?php endforeach?>
<?php if ($track):?>
        <track src="<?=$this->escape($track)?>" srclang="<?=$this->escape($langCode)?>" label="<?=$this->text('subtitle_label')?>">
<?php endif?>
        <a href="<?=$this->escape($filename)?>"><?=$this->escape($downloadLink)?></a>
    </video>
</div>
