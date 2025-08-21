<?php

use Plib\View;

/**
 * @var View $this
 * @var string $className
 * @var string $attributes
 * @var list<array{url:string,type:string}> $sources
 * @var string $track
 * @var string $langCode
 * @var string $subtitles_enabled
 * @var string $contentUrl
 * @var array<string,string> $filenames
 * @var string $basename
 * @var string $title
 * @var string $description
 * @var string $uploadDate
 * @var ?string $thumbnailUrl
 */
?>

<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject" class="<?=$this->esc($className)?>">
  <meta itemprop="name" content="<?=$this->esc($title)?>">
  <meta itemprop="description" content="<?=$this->esc($description)?>">
  <meta itemprop="contentURL" content="<?=$this->esc($contentUrl)?>">
<?if (isset($thumbnailUrl)):?>
  <meta itemprop="thumbnailUrl" content="<?=$this->esc($thumbnailUrl)?>">
<?endif?>
  <meta itemprop="uploadDate" content="<?=$this->esc($uploadDate)?>">
  <video class="<?=$this->esc($className)?>" <?=$this->raw($attributes)?>>
<?foreach ($sources as $source):?>
    <source src="<?=$this->esc($source['url'])?>" type="<?=$this->esc($source['type'])?>">
<?endforeach?>
<?if ($track):?>
    <track src="<?=$this->esc($track)?>" srclang="<?=$this->esc($langCode)?>" label="<?=$this->text('subtitle_label')?>" <?=$this->esc($subtitles_enabled)?>>
<?endif?>
<?if (isset($thumbnailUrl)):?>
    <img src="<?=$this->esc($thumbnailUrl)?>" alt="<?=$this->esc($title)?>">
<?endif?>
    <p><?=$this->text("label_download", $basename)?></p>
    <ul>
<?foreach ($filenames as $filename => $basename):?>
      <li><a href="<?=$this->esc($filename)?>"><?=$this->esc($basename)?></a></li>
<?endforeach?>
    </ul>
  </video>
</div>
