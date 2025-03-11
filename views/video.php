<?php

use Video\Infra\View;

/**
 * @var View $this
 * @var string $className
 * @var string $attributes
 * @var list<array{url:string,type:string}> $sources
 * @var string $track
 * @var string $langCode
 * @var string $contentUrl
 * @var string $filename
 * @var string $downloadLink
 * @var string $title
 * @var string $description
 * @var string $uploadDate
 * @var ?string $thumbnailUrl
 */
?>

<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
  <meta itemprop="name" content="<?=$this->esc($title)?>">
  <meta itemprop="description" content="<?=$this->esc($description)?>">
  <meta itemprop="contentURL" content="<?=$this->esc($contentUrl)?>">
<?if (isset($thumbnailUrl)):?>
  <meta itemprop="thumbnailUrl" content="<?=$this->esc($thumbnailUrl)?>">
<?endif?>
  <meta itemprop="uploadDate" content="<?=$this->esc($uploadDate)?>">
  <video class="<?=$this->esc($className)?>" <?=$this->raw($attributes)?>>
<?foreach ($sources as $source):?>
    <source src="<?=$this->esc($source['url'])?>" type="video/<?=$this->esc($source['type'])?>">
<?endforeach?>
<?if ($track):?>
    <track src="<?=$this->esc($track)?>" srclang="<?=$this->esc($langCode)?>" label="<?=$this->text('subtitle_label')?>">
<?endif?>
    <a href="<?=$this->esc($filename)?>"><?=$this->raw($downloadLink)?></a>
  </video>
</div>
