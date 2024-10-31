<?php
class shortcodes extends module {
    protected $_codesPrepared = false;
    protected $_codes = array(
        'one_fourth' => array('func' => 'oneFourthShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'one_fourth_last' => array('func' => 'oneFourthLastShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'two_third' => array('func' => 'twoThirdShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'one_third' => array('func' => 'oneThirdShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'one_third_last' => array('func' => 'oneThirdLastShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'one_half' => array('func' => 'oneHalfShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'one_half_last' => array('func' => 'oneHalfLastShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'three_fourth_last' => array('func' => 'threeFourthLastShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'three_fourth' => array('func' => 'threeFourthShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'full_width' => array('func' => 'fullWidthShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        
        'youtube' => array('func' => 'youtubeShortcode', 'atts' => array('id' => array('label' => 'ID'), 'width' => array('label' => 'Width'), 'height' => array('label' => 'Height')), 'tpl' => '[%code%%atts%]'),
        'vimeo' => array('func' => 'videoShortcode', 'atts' => array('id' => array('label' => 'ID'), 'width' => array('label' => 'Width'), 'height' => array('label' => 'Height')), 'tpl' => '[%code%%atts%]'),
        'dailymotion' => array('func' => 'dailymotionShortcode', 'atts' => array('id' => array('label' => 'ID'), 'width' => array('label' => 'Width'), 'height' => array('label' => 'Height')), 'tpl' => '[%code%%atts%]'),

        'googlemap' => array('func' => 'googleMapsShortcode', 'atts' => array('src' => array('label' => 'Src'), 'width' => array('label' => 'Width'), 'height' => array('label' => 'Height')), 'tpl' => '[%code%%atts%]'),
        'image' => array('func' => 'imageShortcode', 'atts' => array('size' => array('label' => 'Size', 'type' => 'selectbox', 'params' => array('options' => array('small' => 'small', 'medium' => 'medium', 'large' => 'large', 'fullwidth' => 'fullwidth'))), 'lightbox' => array('label' => 'Lightbox', 'type' => 'checkbox', 'value' => 'true')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'social' => array('func' => 'socialShortcode', 'atts' => array('type' => array('label' => 'Type', 'type' => 'selectbox', 'params' => array('options' => array('twitter' => 'twitter', 'facebook' => 'facebook', 'vimeo' => 'vimeo', 'dribble' => 'dribble', 'skype' => 'skype', 'forrst' => 'forrst', 'flickr' => 'flickr', 'youtube' => 'youtube', 'linkedin' => 'linkedin', 'rss' => 'rss', 'soundcloud' => 'soundcloud'))), 'size' => array('label' => 'Size', 'type' => 'selectbox', 'params' => array('options' => array('small' => 'small', 'large' => 'large'))), 'href' => array('label' => 'href')), 'tpl' => '[%code%%atts%]'),
        
        'dropcap' => array('func' => 'dropcapShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'quote' => array('func' => 'quoteShortcode', 'atts' => array('author' => array('label' => 'quote author')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'highlight' => array('func' => 'highlightShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'size' => array('func' => 'sizeShortcode', 'atts' => array('px' => array('label' => 'size, px')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'special_font' => array('func' => 'specialFontShortcode', 'atts' => array('size' => array('label' => 'Size'), 'unit' => array('label' => 'Unit')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'success' => array('func' => 'successShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'arrow' => array('func' => 'arrowShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'alert' => array('func' => 'alertShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'error' => array('func' => 'errorShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'notice' => array('func' => 'noticeShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'info' => array('func' => 'infoShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        'button_icon' => array('func' => 'buttonIconShortcode', 'atts' => array('icon' => array('label' => 'Icon type', 'type' => 'selectbox', 'params' => array('options' => array('arrow' => 'arrow', 'arrow-left' => 'arrow-left', 'ok' => 'ok', 'download' => 'download', 'cancel' => 'cancel', 'notify' => 'notify', 'info' => 'info', 'discount' => 'discount', 'cart' => 'cart'))), 'href' => array('label' => 'Link')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'button' => array('func' => 'buttonShortcode', 'atts' => array('color' => array('label' => 'Color', 'type' => 'selectbox', 'params' => array('options' => array('brown' => 'brown', 'blue' => 'blue', 'violet' => 'violet', 'yellow' => 'yellow'))), 'href' => array('label' => 'Link')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'table' => array('func' => 'tableShortcode', 'atts' => array('color' => array('label' => 'Color (white, red, blue, grey or HEX)'), 'width' => array('label' => 'Width, %')), 'tpl' => '[%code%%atts%]%content%[/%code%]'),

        'price' => array('func' => 'priceShortcode', 'atts' => array('title' => array('label' => 'Title'), 'price' => array('label' => 'Price'), 'href' => array('label' => 'Link'), 'buttontext' => array('label' => 'Button text'), 'color' => array('label' => 'Color', 'type' => 'selectbox', 'params' => array('options' => array('white' => 'white', 'grey' => 'grey', 'blue' => 'blue', 'red' => 'red', 'green' => 'green', 'yellow' => 'yellow')))), 'tpl' => '[%code%%atts%]%content%[/%code%]'),
        'styled_table' => array('func' => 'styledTableShortcode', 'atts' => array(), 'tpl' => '[%code%]%content%[/%code%]'),
        
        'product' => array(
			'func' => 'productShortcode', 
			'atts' => array(
				'id' => array('label' => 'ID'), 
				'theme' => array('label' => 'Theme name'), 
				'show' => array('label' => 'Show only fields', 'type' => 'selectlist', 'params' => array('options' => array('full_image' => 'Full Image', 'preview_images' => 'Prev. Images', 'title' => 'Title', 'price' => 'Price', 'sku' => 'SKU', 'short_descr' => 'Short description', 'full_descr' => 'Full description', 'details' => 'Details', 'add_to_cart' => '"Add to cart" buttons', 'quantity' => 'Quantity', 'show_extra_fields' => 'Extra fields', 'show_twitter' => 'Twitter', 'show_gplus' => 'Google+', 'show_facebook' => 'Facebook'))), 
				'exclude' => array('label' => 'Exclude fields', 'type' => 'selectlist', 'params' => array('options' => array('full_image' => 'Full Image', 'preview_images' => 'Prev. Images', 'title' => 'Title', 'price' => 'Price', 'sku' => 'SKU', 'short_descr' => 'Short description', 'full_descr' => 'Full description', 'details' => 'Details', 'add_to_cart' => '"Add to cart" buttons', 'quantity' => 'Quantity', 'show_extra_fields' => 'Extra fields', 'show_twitter' => 'Twitter', 'show_gplus' => 'Google+', 'show_facebook' => 'Facebook'))),
				'add_to_cart_text' => array('label' => 'Add to cart text color'),
				'gallery_position' => array('label' => 'Gallery position', 'type' => 'selectbox', 'params' => array('options' => array('left' => 'Left', 'right' => 'Right'))),
			), 
			'tpl' => '[%code%%atts%]'),
        'category' => array(
			'func' => 'categoriesShortcode', 
			'atts' => array(
				'alias' => array('label' => 'Category Alias'), 
				'id' => array('label' => 'or Category ID'), 
				'shownum' => array('label' => 'Number of products to show'),
				'catalog_view' => array('label' => 'Catalog Items view', 'type' => 'selectbox', 'params' => array('options' => array('grid' => 'Grid View', 'list' => 'List View'))),
				
				'grid_preview_size' => array('label' => 'Preview size (grid)'),
				'list_preview_size' => array('label' => 'Preview size (list)'),
				
				'grid_vert_distance' => array('label' => 'Vertical distance (grid)'),
				'grid_hor_distance' => array('label' => 'Horizontal distance (grid)'),
				'list_vert_distance' => array('label' => 'Vertical distance (list)'),
				
				'short_descr_size' => array('label' => 'Short description size (in lines)'),
				
				'hover_item_bg' => array('label' => 'Background when Hovering on product item (grid)'),
				'short_descr_color' => array('label' => 'Short Description text color'),
				'price_color' => array('label' => 'Price color'),
				'image_border_color' => array('label' => 'Product image border color'),
				'title_color' => array('label' => 'Product title color'),
				
				'show' => array('label' => 'Show params', 'type' => 'selectlist', 'params' => array('options' => array('shadow_border' => 'Shadow border on product item hover', 'short_descr' => 'Short description', 'catalog_image' => 'Product Image', 'title' => 'Product Title', 'price' => 'Price', 'more' => 'Read More', 'add_to_cart' => 'Add to Cart'))), 
				'exclude' => array('label' => 'Exclude params', 'type' => 'selectlist', 'params' => array('options' => array('shadow_border' => 'Shadow border on product item hover', 'short_descr' => 'Short description', 'catalog_image' => 'Product Image', 'title' => 'Product Title', 'price' => 'Price', 'more' => 'Read More', 'add_to_cart' => 'Add to Cart'))),
			), 
			'tpl' => '[%code%%atts%]'),
    );
	private function _prepareProdCatOpts($params, $for) {
		if(isset($params['show']) && !empty($params['show']))
			$params['show'] = array_map('trim', explode(',', $params['show']));
		else
			$params['show'] = array();
		if(isset($params['exclude']) && !empty($params['exclude']))
			$params['exclude'] = array_map('trim', explode(',', $params['exclude']));
		else
			$params['exclude'] = array();
		$viewOptions = array();
		$prodViewOptions = $for == S_PRODUCT 
			? frame::_()->getModule('products')->getView('productViewTab')->getProductViewOptions()
			: frame::_()->getModule('products')->getView('productViewTab')->getProductCategoryViewOptions();
		foreach($prodViewOptions as $key => $optVal) {
			if(in_array($key, $params['show'])) {									// Force show
				$viewOptions[ $key ] = 1;
			} elseif(in_array($key, $params['exclude'])) {							// Force hide
				$viewOptions[ $key ] = 0;
			} elseif(!empty($params['show']) && in_array($optVal, array(0, 1))) {	// If show is precent - show only those elements, disable all other, array(0, 1) - 
				$viewOptions[ $key ] = 0;
			} elseif(array_key_exists($key, $params)) {								// Parameter is present in shortcode - take it's value from there
				$viewOptions[ $key ] = $params[ $key ];
			} else {																// Just save original value from options page
				$viewOptions[ $key ] = $optVal;
			}
		}
		return $viewOptions;
	}
    public function productShortcode($params = array()) {
        extract(shortcode_atts(array(
            'id' => '',
            'theme' => '',
            'show' => '',
            'exclude' => '',
        ), $params));
        if(isset($id) && is_numeric($id)) {
            $productPost = get_post($id);
            if(!empty($productPost)) {
                if(!empty($theme)) {
                    frame::_()->getModule('products')->getController()->getView()->setTheme($theme);
                }
                if(empty($show))
					$show = array();
				else
                    $show = array_map('trim', explode(',', $show));
                if(empty($exclude))
					$exclude = array();
				else
                    $exclude = array_map('trim', explode(',', $exclude));
				
				$viewOptions = $this->_prepareProdCatOpts($params, S_PRODUCT);
                $content = frame::_()->getModule('products')->getController()->getView()->getProductContent(array(
                                'productPost' => $productPost,
                                'show' => $show,
                                'exclude' => $exclude,
                                'viewOptions' => $viewOptions,
                ));
                if(!empty($theme)) {
                    frame::_()->getModule('products')->getController()->getView()->setTheme('');
                }
				// Wrap this to more comfortable work for coders
				$content = '<div class="toeProductShortcodeWrapper">'. $content. '</div>';
				
                return $content;
            }
        }
        return false;
    }
    public function categoriesShortcode($params = array()) {
        global $wp_query, $post;
        extract(shortcode_atts(array(
            'id' => '',
            'alias' => '',
            'shownum' => '',
        ), $params));
        if(empty($alias) && !empty($id)) {
            $term = get_term_by('id', $id, frame::_()->getModule('products')->getConstant('CATEGORIES'));
            if(!empty($term) && is_object($term)) {
                $alias = $term->slug;
            }
        }
        if(!empty($alias)) {
            $wpQueryAttrs = array( frame::_()->getModule('products')->getConstant('CATEGORIES') => $alias );
            
            if(!empty($shownum) && is_numeric($shownum)) {
                $wpQueryAttrs['posts_per_page'] = (int) $shownum;
            }
			$viewOptions = $this->_prepareProdCatOpts($params, 'category');
			$content = frame::_()->getModule('products')->getView()->getAllProductsListHtml('', 
					$wpQueryAttrs, 
					array('viewOptions' => $viewOptions));
			
			// Wrap this to more comfortable work for coders
			$content = '<div class="toeCategoryShortcodeWrapper">'. $content. '</div>';
        }
        return $content;
    }
    public function init() {
        parent::init();
        frame::_()->addStyle('shortcodes', S_CSS_PATH. 'shortcodes.css');
        frame::_()->addStyle('lightbox', S_CSS_PATH. 'lightbox.css');
        frame::_()->addScript('lightbox', S_JS_PATH. 'lightbox.js');
        foreach($this->_codes as $code => $cInfo) {
            add_shortcode($code, array($this, $cInfo['func']));
        }

        add_action('init', array($this, 'addTextEditorButtons'));
        add_filter('tiny_mce_version', array($this, 'my_refresh_mce'));
    }
    public function addTextEditorButtons() {
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
            return;
        if ( get_user_option('rich_editing') == 'true') {
            frame::_()->addJSVar('adminForm', 'toeShortcodesText', array(
                'adminTextEditorPopup' => $this->getController()->getView()->adminTextEditorPopup()
            ));
            
            add_filter('mce_external_plugins', array($this, 'addTextEditorButtonsPlugin'));
            add_filter('mce_buttons', array($this, 'registerTextEditorButtons'));
        }
    }
    public function registerTextEditorButtons($buttons) {
        array_push($buttons, '|', 'toeshortcodes');
        return $buttons;
    }

    public function addTextEditorButtonsPlugin($plugin_array) {
       $plugin_array['toeshortcodes'] = $this->getModPath(). 'js/textEditorPlugin.js';
       return $plugin_array;
    }
    //As I understand - this is temp function for developing phase
    public function my_refresh_mce($ver) {
        $ver += 3;
        return $ver;
    }
    public function getCodes() {
        if(!$this->_codesPrepared) {
            foreach($this->_codes as $code => $cInfo) {
                if(!empty($cInfo['atts'])) {
                    foreach($cInfo['atts'] as $attId => $attInfo) {
                        if(empty($attInfo['type']))
                            $this->_codes[ $code ]['atts'][ $attId ]['type'] = 'text';
                        if(empty($attInfo['params']))
                            $this->_codes[ $code ]['atts'][ $attId ]['params'] = array();
                        if(!isset($attInfo['label']))
                            $attInfo['label'] = '';
                        if(!empty($attInfo['label']))
                            $attInfo['label'] = lang::_($attInfo['label']);
                        $htmlMethod = $this->_codes[ $code ]['atts'][ $attId ]['type'];
                        if(method_exists('html', $htmlMethod)) {
                            $htmlData = array($attId);
                            if(isset($this->_codes[ $code ]['atts'][ $attId ]['params']))
                                    $this->_codes[ $code ]['atts'][ $attId ]['params'] = array($this->_codes[ $code ]['atts'][ $attId ]['params']);
                            $htmlData = array_merge($htmlData, $this->_codes[ $code ]['atts'][ $attId ]['params']);
                            $this->_codes[ $code ]['atts'][ $attId ]['html'] = call_user_func_array(array('html', $htmlMethod), $htmlData);
                        }
                    }
                }
            }
            $this->_codesPrepared = true;
        }
        return $this->_codes;
    }

    ////////////////////////////////////////////////////////////////////////////
    // COLUMNS SHORTCODES //////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
     * One Fourth shortcode
     * Usage: [one_fourth]your text[/one_fourth]
     */
    public function oneFourthShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_one_fourth">' . $content . '</div>';
    }
    /*
     * One Fourth Last shortcode
     * Usage: [one_fourth_last]your text[/one_fourth_last]
     */
    public function oneFourthLastShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_one_fourth toe_shortcode_last_column">' . $content . '</div><div class="clear"></div>';
    }
    /*
     * Two Third shortcode
     * Usage: [two_third]your text[/two_third]
     */
    public function twoThirdShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_two_third">' . $content . '</div>';
    }
    /*
     * One Third shortcode
     * Usage: [one_third]your text[/one_third]
     */
    public function oneThirdShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_one_third">' . $content . '</div>';
    }
    /*
     * One Third Last shortcode
     * Usage: [one_third_last]your text[/one_third_last]
     */
    public function oneThirdLastShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_one_third toe_shortcode_last_column">' . $content . '</div><div class="clear"></div>';
    }
    /*
     * One Half shortcode
     * Usage: [one_half]your text[/one_half]
     */
    public function oneHalfShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_one_half">' . $content . '</div>';
    }
    /*
     * One Half Last shortcode
     * Usage: [one_half_last]your text[/one_half_last]
     */
    public function oneHalfLastShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_one_half toe_shortcode_last_column">' . $content . '</div><div class="clear"></div>';
    }
    /*
     * Three Fourth shortcode
     * Usage: [three_fourth]your text[/three_fourth]
     */
    public function threeFourthShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_three_fourth">' . $content . '</div><div class="clear"></div>';
    }
    /*
     * Three Fourth Last shortcode
     * Usage: [three_fourth_last]your text[/three_fourth_last]
     */
    public function threeFourthLastShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_three_fourth toe_shortcode_last_column">' . $content . '</div><div class="clear"></div>';
    }
    /*
     * Full width shortcode
     * Usage: [full_width]your text[/full_width]
     */
    public function fullWidthShortcode( $atts, $content = null ) {
       return '<div class="toe_shortcode_full_width">' . $content . '</div><div class="clear"></div>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // VIDEO SHORTCODES ////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
    * Youtube video shortcode
    * Usage: [youtube id="VideoID", width="540", height="300"], params "width" and "height" - not REQUIRED
    */
    public function youtubeShortcode($params = array()) {
        extract(shortcode_atts(array(
            'id' => '',
            'width' => '540',
            'height' => '300'
        ), $params));

        if ($id != ''){
            $str = '';
                $str .= '<div class="video_youtube" style="width:'.$width.'px; height:'.$height.'px;">';
                $str .= '<object type="application/x-shockwave-flash" data="http://www.youtube.com/v/'.$id.'" width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube.com/v/'.$id.'" /></object>';
                $str .= '</div>';
        }

        return $str;
    }
    /*
    * Vimeo video shortcode
    * Usage: [vimeo id="VideoID", width="540", height="300"], params "width" and "height" - not REQUIRED
    */
    public function videoShortcode($params = array()) {

        extract(shortcode_atts(array(
            'id' => '',
            'width' => '540',
            'height' => '300'
        ), $params));

        if ($id != ''){
            $str = '';
                $str .= '<div class="video_vimeo" style="width:'.$width.'px; height:'.$height.'px;">';
                $str .= '<iframe src="http://player.vimeo.com/video/'.$id.'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                $str .= '</div>';
        }

        return $str;
    }
    /*
    * Dailymotion video shortcode
    * Usage: [dailymotion id="VideoID", width="540", height="300"], params "width" and "height" - not REQUIRED
    */
    public function dailymotionShortcode($params = array()) {

        extract(shortcode_atts(array(
            'id' => '',
            'width' => '540',
            'height' => '300'
        ), $params));

        if ($id != ''){
            $str = '';
                $str .= '<div class="video_dailymotion" style="width:'.$width.'px; height:'.$height.'px;">';
                $str .= '<iframe frameborder="0" width="'.$width.'" height="'.$height.'" src="http://www.dailymotion.com/embed/video/'.$id.'"></iframe>';
                $str .= '</div>';
        }

        return $str;
    }
    ////////////////////////////////////////////////////////////////////////////
    // GOOGLE MAP, IMAGES and SOCIAL SHORTCODE /////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
    * Google map shortcode
    * Usage: [googlemap width="540", height="300", src="FULL LINK"], params "width" and "height" - not REQUIRED
    * Simple usage: [googlemap src="FULL LINK"]
    */
    public function googleMapsShortcode($atts, $content = null) {
        extract(shortcode_atts(array(
          "width" => '640',
          "height" => '480',
          "src" => ''
        ), $atts));

        return '<div class="google-map"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&output=embed"></iframe></div>';
    }
    /*
    * Images shortcode
    * Usage: [image size="small/medium/large/fullwidth" lightbox="true/false"]http://url.to/image.jpg[/image]
    */
    public function imageShortcode($atts, $content = null) {
        extract(shortcode_atts(array(
          "size" => 'large',
          "lightbox" => '#'
        ), $atts));
        switch($size){
            case 'small' : $width = '150px'; break;
            case 'medium' : $width = '300px'; break;
            case 'large' : $width = '700px'; break;
            case 'fullwidth' : $width = '100%'; break;
        }
        if ($lightbox) {return '<a href="'.$content.'" rel="lightbox" class="image-wrapper"><img src="'.$content.'" width="'.$width.'"/></a>';}
        else {return '<a href="#" class="image-wrapper"><img src="'.$content.'" width="'.$width.'"/></a>';}
    }
    /*
    * Social shortcode
    * Usage: [social type="twitter|facebook|rss|youtube|delicious|vimeo|flickr|stumble|linkedin|skype|lastfm|myspace|tumblr|digg|quora|dribble|forrst|google|ember|pinterest" size="small|large" href="#"]
    */
    public function socialShortcode($atts, $content = null) {
        extract(shortcode_atts(array(
          "type" => 'facebook',
          "size" => 'large',
          "href" => '#'
        ), $atts));

        return '<a href="'.$href.'" title="'.$type.'"><img class="social_'.$size.'" src="'.S_CSS_PATH.'/img/social/'.$type.'.png" alt="'.$type.'" /></a>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // TYPOGRAPHY //////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
    * Dropcap shortcode
    * Usage: [dropcap]your text[/dropcap]
    */
    public function dropcapShortcode( $atts, $content = null ) {
       return '<p class="toe_shortcode_dropcap">' . $content . '</p>';
    }
    /*
    * Quote shortcode
    * Usage: [quote author="Steave Jobs"]your text[/quote]
    */
    public function quoteShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "author" => ''
        ), $atts));
        return '<blockquote>' . $content . '<span class="author">'.$author.'</span><span class="quote"></span></blockquote>';
    }
    /*
    * Highlight shortcode
    * Usage: [highlight]your text[/highlight]
    */
    public function highlightShortcode( $atts, $content = null ) {
       return '<span class="toe_shortcode_highlight">' . $content . '</span>';
    }
    /*
    * Font size shortcode
    * Usage: [size px="16"]your text[/size]
    */
    public function sizeShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "px" => '16'
        ), $atts));
        return '<span style="font-size:'.$px.'px">' . $content . '</span>';
    }
    /*
    * Special Font shortcode
    * Usage: [special_font size="18" umit="px"]your text[/special_font]
    */
    public function specialFontShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "size" => '16',
          "unit" => 'px',
        ), $atts));
        return '<span class="special_font" style="font-size:'.$size.$unit.'">' . $content . '</span>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // ALERT BOX & BUTTONS SHORTCODES //////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
    * Success shortcode
    * Usage: [success]your text[/success]
    */
    public function successShortcode( $atts, $content = null ) {
        return '<div class="success_box">' . $content . '</div>';
    }
    /*
    * Arrow shortcode
    * Usage: [arrow]your text[/arrow]
    */
    public function arrowShortcode( $atts, $content = null ) {
        return '<div class="arrow_box">' . $content . '</div>';
    }
    /*
    * Alert shortcode
    * Usage: [alert]your text[/alert]
    */
    public function alertShortcode( $atts, $content = null ) {
        return '<div class="alert_box">' . $content . '</div>';
    }
    /*
    * Error shortcode
    * Usage: [error]your text[/error]
    */
    public function errorShortcode( $atts, $content = null ) {
        return '<div class="error_box">' . $content . '</div>';
    }
    /*
    * Notice shortcode
    * Usage: [notice]your text[/notice]
    */
    public function noticeShortcode( $atts, $content = null ) {
        return '<div class="notice_box">' . $content . '</div>';
    }
    /*
    * Info shortcode
    * Usage: [info]your text[/info]
    */
    public function infoShortcode( $atts, $content = null ) {
        return '<div class="info_box">' . $content . '</div>';
    }
    /*
    * Button icon shortcode
    * Usage: [button_icon icon="arrow|arrow-left|calc|gift|offer|remove" href="LINK"]your text[/button_icon]
    */
    public function buttonIconShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "icon" => 'arrow',
          "href" => '#',
        ), $atts));
        return '<a href="'.$href.'" class="shortcode_button icon_'.$icon.'"><span class="button-icon"></span>' . $content . '</a>';
    }
    /*
    * Button shortcode
    * Usage: [button color="green|blue|magenta|red|orange|yellow" href="LINK"]your text[/button]
    */
    public function buttonShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "color" => 'green',
          "href" => '#',
        ), $atts));
        
        return '<a href="'.$href.'" class="button_color btn_color_'.$color.'">' . $content . '</a>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // TABLES, BOXES TABS PRICES ///////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
    * Table shortcode
    * Usage: [table color="white|red|blue|grey|HEX" width="XXX%"]TABLE CONTENT without TABLE TAG[/table]
    */
    public function tableShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "color" => 'white',
          "width" => '100%',
        ), $atts));
        if ($color != 'white' and $color != 'red' and $color != 'blue' and $color != 'grey') {
            return '<style type="text/css">#shorttable'.$color.' thead tr td {background-color:#'.$color.'}</style><table style="width: '.$width.';" id="shorttable'.$color.'" class="short_table" cellspacing="0" cellpadding="0">' . $content . '</table>';
        } else {
            return '<table style="width: '.$width.'%;" class="short_table color_'.$color.'" cellspacing="0" cellpadding="0">' . $content . '</table>';
        }
    }
    /*
    * Price shortcode
    * Usage: [price title="Standard Plan" price="45.0 $" href="#" buttontext="More info" color="white|grey|blue|red|green|yellow"] CONTENT [/price]
    */
    public function priceShortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
          "title" => 'Sample Title',
          "price" => '0.00',
          "href" => '#',
          "buttontext" => '100%',
          "color" => 'white',
        ), $atts));      
        $str = '';
        $str .= '<div class="price-table"><div class="price_box_head price_box_'.$color.'"><p>'.$title.'</p><h2 class="price_box_price">'.$price.'</h2></div>';
        $str .= '<div class="price_box_body">'.$content.'<p class="price_box_more"><a href="'.$href.'">'.$buttontext.'</a></p></div></div>';
        return $str;
    }
    /*
    * Styled table shortcode
    * Usage: [styled_table]TABLE CONTENT without TABLE TAG[/styled_table]
    */
    public function styledTableShortcode( $atts, $content = null ) {
        return '<table class="styled_table" cellspacing="0" cellpadding="0">' . $content . '</table>';       
    }
}
?>