<?php if(!empty($this->instance['title'])) { ?>
    <div class="toeWidgetTitle">
        <h2><?php echo $this->instance['title']?></h2>
    </div>
<?php }?>
<div id="<?php echo $this->uniqBoxId?>"  class="evoslider default<?php echo (empty($this->instance['boxClass']) ? '' : ' '. $this->instance['boxClass'])?>">
    <dl>
        <?php foreach($this->images as $img) {?>
        <dt><?php echo $img['title']?></dt>
        <dd data-src="<?php echo $img['path']?>" data-text="overlay"<?php if(!empty($img['link'])) {?> data-url="<?php echo $img['link']?>" <?php }?>>
            <?php if(!empty($img['desc'])) {?><div class="evoText"><?php echo $img['desc']?></div><?php }?>
        </dd>
        <?php }?>
    </dl>
</div>
<script type="text/javascript">
// <!--
    var <?php echo $this->uniqBoxId?> = jQuery("#<?php echo $this->uniqBoxId?>").evoSlider({
        mode: '<?php echo $this->instance['mode']?>',
        speed: <?php echo $this->instance['speed']?>,
        interval: <?php echo $this->instance['interval']?>,
        pauseOnHover: <?php echo $this->instance['pauseOnHover'] ? 'true' : 'false'?>,
        showPlayButton: <?php echo $this->instance['showPlayButton'] ? 'true' : 'false'?>,
        directionNav: <?php echo $this->instance['directionNav'] ? 'true' : 'false'?>,                 // Shows directional navigation when initialized
        directionNavAutoHide: <?php echo $this->instance['directionNavAutoHide'] ? 'true' : 'false'?>,        // Shows directional navigation on hover and hide it when mouseout


        width: <?php echo $this->instance['width']?>,
        height: <?php echo $this->instance['height']?>,
        slideSpace: <?php echo $this->instance['slideSpace']?>,                      // The space between slides
        paddingRight: <?php echo $this->instance['paddingRight']?>,                    // Padding right of the container/frame
        titleClockWiseRotation: <?php echo $this->instance['directionNavAutoHide'] ? 'true' : 'false'?>,      // Rotates title bar by clock wise
        hideCurrentTitle: <?php echo $this->instance['hideCurrentTitle'] ? 'true' : 'false'?>,            // Hides active title bar
        startIndex: <?php echo $this->instance['startIndex']?>,                      // Start index when initialized
        showIndex: true,                    // Displays index in toggle icon and bullets control
        mouse: <?php echo $this->instance['mouse'] ? 'true' : 'false'?>,                        // Enables mousewheel scroll navigation
        keyboard: true,                     // Enables keyboard navigation (left and right arrows)
        easing: "swing",                    // Defines the easing effect mode
        loop: true,                         // Rotate slideshow
        lazyLoad: <?php echo $this->instance['lazyLoad'] ? 'true' : 'false'?>,                    // Enables lazy load feature
        autoplay: true,                     // Sets EvoSlider to play slideshow when initialized
        pauseOnClick: true,                 // Stop slideshow if playing
        playButtonAutoHide: <?php echo $this->instance['playButtonAutoHide'] ? 'true' : 'false'?>,          // Shows play/pause button on hover and hide it when mouseout
        toggleIcon: true,                   // Enables toggle icon
        showDirectionText: <?php echo $this->instance['showDirectionText'] ? 'true' : 'false'?>,           // Shows text on direction navigation
        nextText: "<?php echo $this->instance['nextText']?>",                   // Next button text
        prevText: "<?php echo $this->instance['prevText']?>",                   // Prev button text
        controlNav: <?php echo $this->instance['controlNav']?>,                   // Enables control navigation
        controlNavMode: "<?php echo $this->instance['controlNavMode']?>",          // Sets control navigation mode ("bullets", "thumbnails", or "rotator")
        controlNavVertical: <?php echo $this->instance['controlNavVertical'] ? 'true' : 'false'?>,          // Defines control navigation to display vertically
        controlNavPosition: "<?php echo $this->instance['controlNavPosition']?>",       // Sets control navigation position ("inside" or "outside")
        controlNavVerticalAlign: "<?php echo $this->instance['controlNavVerticalAlign']?>",   // Sets position of the vertical control navigation ("left" or "right")
        controlSpace: <?php echo $this->instance['controlSpace']?>,                    // The space between outside control navigation with slides 
        controlNavAutoHide: <?php echo $this->instance['controlNavAutoHide'] ? 'true' : 'false'?>,          // Shows control navigation on mouseover and hide it when mouseout
        showRotatorTitles: true,            // Shows rotator titles
        showRotatorThumbs: true,            // Shows rotator thumbnails
        rotatorThumbsAlign: "<?php echo $this->instance['rotatorThumbsAlign']?>",         // Thumbnails float position
        classBtnNext: "<?php echo $this->instance['classBtnNext']?>",           // The CSS class used for the next button
        classBtnPrev: "<?php echo $this->instance['classBtnPrev']?>",           // The CSS class used for the previous button
        classExtLink: "<?php echo $this->instance['classExtLink']?>",           // The CSS class used for the external links
        permalink: <?php echo $this->instance['permalink'] ? 'true' : 'false'?>,                   // Enable or disable linking to slides via the url
        autoHideText: <?php echo $this->instance['autoHideText'] ? 'true' : 'false'?>,                // Shows overlay text on mouseover and hide it on mouseout    
        outerText: <?php echo $this->instance['outerText'] ? 'true' : 'false'?>,                   // Enables outer text
        outerTextPosition: "<?php echo $this->instance['outerTextPosition']?>",         // Outer text align ("left" or "right")
        outerTextSpace: <?php echo $this->instance['outerTextSpace']?>,                  // Space between text and slide
        linkTarget: "<?php echo $this->instance['linkTarget']?>",               // The target attribute of the image link ("_blank", "_parent", "_self", or "_top")
        responsive: <?php echo $this->instance['responsive'] ? 'true' : 'false'?>,                  // Enables responsive layout
        imageScale: "<?php echo $this->instance['imageScale']?>",             // Sets image scale option ("fullSize", "fitImage", "fitWidth", "fitHeight", "none")
        before: function(){},               // Callback, triggers before slide transition
        after: function(){},                // Callback, triggers after slide transition
                youtube: {                          // Customize the YouTube player parameters
            autoplay: 0,
            showinfo: 1,
            autohide: 2
        },
        vimeo: {                            // Customize the Vimeo player parameters
            title: 1,
            byline: 1,
            portrait: 1,
            autoplay: 0
        },
        dailymotion: {                      // Customize the Dailymotion player parameters 
            autoplay: 0,
            logo: 0,
            foreground: "%23F7FFFD",
            background: "%23FFC300",
            highlight: "%23171D1B",
            info: 1
        }                  
    });
// -->
</script>
