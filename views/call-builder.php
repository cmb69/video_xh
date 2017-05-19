<h1>Video – <?=$this->text('menu_main')?></h1>
<div id="video_call_builder">
    <p><label><span><?=$this->text('label_name')?></span><?=$this->nameSelect()?></label></p>
    <p><label><span><?=$this->text('label_preload')?></span><?=$this->preloadSelect()?></label></p>
    <p><label><span><?=$this->text('label_autoplay')?></span><?=$this->autoplayInput()?></label></p>
    <p><label><span><?=$this->text('label_loop')?></span><?=$this->loopInput()?></label></p>
    <p><label><span><?=$this->text('label_controls')?></span><?=$this->controlsInput()?></label></p>
    <p><label><span><?=$this->text('label_width')?></span><?=$this->widthInput()?></label></p>
    <p><label><span><?=$this->text('label_height')?></span><?=$this->heightInput()?></label></p>
    <p><label><span><?=$this->text('label_resize')?></span><?=$this->resizeSelect()?></label></p>
    <p><textarea id="video_call" readonly></textarea></p>
</div>
