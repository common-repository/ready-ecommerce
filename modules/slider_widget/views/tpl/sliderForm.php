<script type="text/javascript">
// <!--
jQuery(function(){
    toeSliderDrawImageDataList(<?php echo utils::jsonEncode($this->images)?>, '<?php echo $this->uniqBoxId?>');
});
// -->
</script>
<div class="toeSliderWidgetOptions" id="<?php echo $this->uniqBoxId?>">
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Slider Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('mode')?>"><?php lang::_e('Slider Mode')?>:</label>
    <?php 
        echo html::selectbox($this->widget->get_field_name('mode'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('mode'). '"', 
            'options' => array('accordion' => 'accordion', 'slider' => 'slider', 'scroller' => 'scroller'),
            'value' => $this->data['mode']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('boxClass')?>"><?php lang::_e('Slider Box class')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('boxClass'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('boxClass'). '"', 
            'value' => $this->data['boxClass']));
    ?><br />
    
    <label for="<?php echo $this->widget->get_field_id('speed')?>"><?php lang::_e('Slider Speed')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('speed'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('speed'). '"', 
            'value' => $this->data['speed']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('interval')?>"><?php lang::_e('Slider Interval')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('interval'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('interval'). '"', 
            'value' => $this->data['interval']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('pauseOnHover')?>"><?php lang::_e('Pause On Hover')?>:</label>
    <?php 
        echo html::checkbox($this->widget->get_field_name('pauseOnHover'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('pauseOnHover'). '"', 
            'checked' => $this->data['pauseOnHover']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('showPlayButton')?>"><?php lang::_e('Show Play Button')?>:</label>
    <?php 
        echo html::checkbox($this->widget->get_field_name('showPlayButton'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('showPlayButton'). '"', 
            'checked' => $this->data['showPlayButton']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('directionNav')?>"><?php lang::_e('Direction Nav')?>:</label>
    <?php 
        echo html::checkbox($this->widget->get_field_name('directionNav'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('directionNav'). '"', 
            'checked' => $this->data['directionNav']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('directionNavAutoHide')?>"><?php lang::_e('Direction Nav Auto Hide')?>:</label>
    <?php 
        echo html::checkbox($this->widget->get_field_name('directionNavAutoHide'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('directionNavAutoHide'). '"', 
            'checked' => $this->data['directionNavAutoHide']));
    ?><br />
    <label for=""><?php lang::_e('Add File')?>:</label>
    <?php 
        echo html::ajaxfile($this->widget->get_field_id('new_file'), array(
            'url' => uri::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'slider_widget', 'action' => 'addFile', 'reqType' => 'ajax')),
            'responseType' => 'json',
            'buttonName' => 'Upload',
            'onComplete' => 'toeSliderCompleteSubmitNewFile',
        ));
    ?><br />
    <label for=""><?php lang::_e('Add Product')?>:</label>
    <?php 
        echo html::button(array(
            'value' => lang::_('Select'),
            'attrs' => 'onclick="toeSliderGetProductsList({msgElID: \''. $this->widget->get_field_id('toeSliderAddProductMsg'). '\', uniqBoxId: \''. $this->uniqBoxId. '\'}); return false;"',
        ));
    ?><div id="<?php echo $this->widget->get_field_id('toeSliderAddProductMsg')?>"></div><br />
    <label for="<?php echo $this->widget->get_field_id('width')?>"><?php lang::_e('Slider Width')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('width'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('width'). '"', 
            'value' => $this->data['width']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('height')?>"><?php lang::_e('Slider Height')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('height'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('height'). '"', 
            'value' => $this->data['height']));
    ?><br />
    <fieldset style="border: 2px solid #E78F08; padding: 5px;">
        <legend><a href="#" onclick="jQuery('#<?php echo html::nameToClassId($this->widget->get_field_id('toeSliderProSettings'))?>').slideToggle(); return false;"><?php lang::_e('Pro-settings')?></a></legend>
        <div id="<?php echo html::nameToClassId($this->widget->get_field_id('toeSliderProSettings'))?>" style="display: none;">
            <label for="<?php echo $this->widget->get_field_id('slideSpace')?>"><?php lang::_e('The space between slides')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('slideSpace'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('slideSpace'). '"', 
                    'value' => $this->data['slideSpace']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('paddingRight')?>"><?php lang::_e('Padding right of the container/frame')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('paddingRight'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('paddingRight'). '"', 
                    'value' => $this->data['paddingRight']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('titleClockWiseRotation')?>"><?php lang::_e('Rotates title bar by clock wise')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('titleClockWiseRotation'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('titleClockWiseRotation'). '"', 
                    'checked' => $this->data['titleClockWiseRotation']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('hideCurrentTitle')?>"><?php lang::_e('Hides active title bar')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('hideCurrentTitle'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('hideCurrentTitle'). '"', 
                    'checked' => $this->data['hideCurrentTitle']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('startIndex')?>"><?php lang::_e('Start index when initialized')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('startIndex'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('startIndex'). '"', 
                    'value' => $this->data['startIndex']));
            ?><br />
			<label for="<?php echo $this->widget->get_field_id('mouse')?>"><?php lang::_e('Use mouse to scroll slides')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('mouse'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('mouse'). '"', 
                    'checked' => $this->data['mouse']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('lazyLoad')?>"><?php lang::_e('Enables lazy load feature')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('lazyLoad'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('lazyLoad'). '"', 
                    'checked' => $this->data['lazyLoad']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('playButtonAutoHide')?>"><?php lang::_e('Shows play/pause button on hover and hide it when mouseout')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('playButtonAutoHide'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('playButtonAutoHide'). '"', 
                    'checked' => $this->data['playButtonAutoHide']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('showDirectionText')?>"><?php lang::_e('Shows text on direction navigation')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('showDirectionText'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('showDirectionText'). '"', 
                    'checked' => $this->data['showDirectionText']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('nextText')?>"><?php lang::_e('Next button text')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('nextText'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('nextText'). '"', 
                    'value' => $this->data['nextText']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('prevText')?>"><?php lang::_e('Prev button text')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('prevText'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('prevText'). '"', 
                    'value' => $this->data['prevText']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlNavMode')?>"><?php lang::_e('Sets control navigation mode')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('controlNav'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlNav'). '"', 
                    'options' => array('true' => 'true', 'false' => 'false'),
                    'value' => $this->data['controlNav']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlNavMode')?>"><?php lang::_e('Sets control navigation mode')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('controlNavMode'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlNavMode'). '"', 
                    'options' => array('bullets' => 'bullets', 'thumbnails' => 'thumbnails', 'rotator' => 'rotator'),
                    'value' => $this->data['controlNavMode']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlNavVertical')?>"><?php lang::_e('Defines control navigation to display vertically')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('controlNavVertical'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlNavVertical'). '"', 
                    'checked' => $this->data['controlNavVertical']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlNavPosition')?>"><?php lang::_e('Sets control navigation position')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('controlNavPosition'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlNavPosition'). '"', 
                    'options' => array('inside' => 'inside', 'outside' => 'outside'),
                    'value' => $this->data['controlNavPosition']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlNavVerticalAlign')?>"><?php lang::_e('Sets position of the vertical control navigation')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('controlNavVerticalAlign'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlNavVerticalAlign'). '"', 
                    'options' => array('left' => 'left', 'right' => 'right'),
                    'value' => $this->data['controlNavVerticalAlign']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlSpace')?>"><?php lang::_e('The space between outside control navigation with slides')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('controlSpace'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlSpace'). '"', 
                    'value' => $this->data['controlSpace']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('controlNavAutoHide')?>"><?php lang::_e('Shows control navigation on mouseover and hide it when mouseout')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('controlNavAutoHide'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('controlNavAutoHide'). '"', 
                    'checked' => $this->data['controlNavAutoHide']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('rotatorThumbsAlign')?>"><?php lang::_e('Thumbnails float position')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('rotatorThumbsAlign'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('rotatorThumbsAlign'). '"', 
                    'value' => $this->data['rotatorThumbsAlign']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('classBtnNext')?>"><?php lang::_e('The CSS class used for the next button')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('classBtnNext'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('classBtnNext'). '"', 
                    'value' => $this->data['classBtnNext']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('classBtnPrev')?>"><?php lang::_e('The CSS class used for the previous button')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('classBtnPrev'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('classBtnPrev'). '"', 
                    'value' => $this->data['classBtnPrev']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('classExtLink')?>"><?php lang::_e('The CSS class used for the external links')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('classExtLink'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('classExtLink'). '"', 
                    'value' => $this->data['classExtLink']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('permalink')?>"><?php lang::_e('Enable or disable linking to slides via the url')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('permalink'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('permalink'). '"', 
                    'checked' => $this->data['permalink']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('autoHideText')?>"><?php lang::_e('Shows overlay text on mouseover and hide it on mouseout')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('autoHideText'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('autoHideText'). '"', 
                    'checked' => $this->data['autoHideText']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('outerText')?>"><?php lang::_e('Enables outer text')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('outerText'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('outerText'). '"', 
                    'checked' => $this->data['outerText']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('outerTextPosition')?>"><?php lang::_e('Outer text align')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('outerTextPosition'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('outerTextPosition'). '"', 
                    'options' => array('right' => 'right', 'left' => 'left'),
                    'value' => $this->data['outerTextPosition']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('outerTextSpace')?>"><?php lang::_e('Space between text and slide')?>:</label>
            <?php 
                echo html::text($this->widget->get_field_name('outerTextSpace'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('outerTextSpace'). '"', 
                    'value' => $this->data['outerTextSpace']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('linkTarget')?>"><?php lang::_e('The target attribute of the image link')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('linkTarget'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('linkTarget'). '"', 
                    'options' => array('_blank' => '_blank', '_parent' => '_parent', '_self' => '_self', '_top' => '_top'),
                    'value' => $this->data['linkTarget']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('responsive')?>"><?php lang::_e('Enables responsive layout')?>:</label>
            <?php 
                echo html::checkbox($this->widget->get_field_name('responsive'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('responsive'). '"', 
                    'checked' => $this->data['responsive']));
            ?><br />
            <label for="<?php echo $this->widget->get_field_id('imageScale')?>"><?php lang::_e('The target attribute of the image link')?>:</label>
            <?php 
                echo html::selectbox($this->widget->get_field_name('imageScale'), array(
                    'attrs' => 'id="'. $this->widget->get_field_id('imageScale'). '"', 
                    'options' => array('fullSize' => 'fullSize', 'fitImage' => 'fitImage', 'fitWidth' => 'fitWidth', 'fitHeight' => 'fitHeight', 'none' => 'none'),
                    'value' => $this->data['imageScale']));
            ?><br />
        </div>
    </fieldset>
    <div class="toeSliderImagesInputsBox">
        <div class="toeSliderWidgetImagesInputs toeSliderWidgetImagesInputsExample">
            <div><span class="toeDeleteButt" onclick="toeRemoveSlide(this);"></span></div>
            <div>
                <img src="" class="toeSliderWidgetImg" />
            </div>
            <div>
                <label for="<?php echo $this->widget->get_field_name('images__replId__title')?>"><?php lang::_e('Title')?>:</label>
                <?php 
                    echo html::text($this->widget->get_field_name('images__replId__title'), array(
                        'attrs' => 'id="'. $this->widget->get_field_id('images__replId__title'). '" disabled="true"'));
                ?><br />
                <label for="<?php echo $this->widget->get_field_name('images__replId__link')?>"><?php lang::_e('Link')?>:</label>
                <?php 
                    echo html::text($this->widget->get_field_name('images__replId__link'), array(
                        'attrs' => 'id="'. $this->widget->get_field_id('images__replId__link'). '" disabled="true"'));
                ?><br />
                <label for="<?php echo $this->widget->get_field_name('images__replId__desc')?>"><?php lang::_e('Description')?>:</label>
                <?php
                    echo html::textarea($this->widget->get_field_name('images__replId__desc'), array(
                        'attrs' => 'id="'. $this->widget->get_field_id('images__replId__desc'). '" disabled="true"',
                        'cols' => 30));
                ?><br />
                <label for="<?php echo $this->widget->get_field_name('images__replId__order')?>"><?php lang::_e('Order')?>:</label>
                <?php 
                    echo html::text($this->widget->get_field_name('images__replId__order'), array(
                        'attrs' => 'id="'. $this->widget->get_field_id('images__replId__order'). '" disabled="true"'));
                ?>
            </div>
            <?php echo html::hidden($this->widget->get_field_name('images__replId__path'), array('attrs' => 'disabled="true"'))?>
            <?php echo html::hidden($this->widget->get_field_name('images__replId__type'), array('attrs' => 'disabled="true"', 'value' => 'slide'))?>
        </div>
    </div>
</div>