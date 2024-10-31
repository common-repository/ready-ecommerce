<?php
class slider_widgetView extends view {
    public function display($instance) {
        $instance['mode'] = utils::checkString($instance['mode'], 'scroller');

        $instance['speed'] = utils::checkNum($instance['speed'], 5);
        $instance['interval'] = utils::checkNum($instance['interval'], 5000);
        $instance['width'] = utils::checkNum($instance['width'], 940);
        $instance['height'] = utils::checkNum($instance['height'], 360);
        $instance['slideSpace'] = utils::checkNum($instance['slideSpace'], 5);
        $instance['paddingRight'] = utils::checkNum($instance['paddingRight'], 0);
        $instance['startIndex'] = utils::checkNum($instance['startIndex'], 0);
        
        $instance['checkString'] = utils::checkString($instance['checkString'], 'Next');
        $instance['prevText'] = utils::checkString($instance['prevText'], 'Prev');
        $instance['controlNav'] = utils::checkString($instance['controlNav'], 'true');
        $instance['controlNavMode'] = utils::checkString($instance['controlNavMode'], 'bullets');
        $instance['controlNavPosition'] = utils::checkString($instance['controlNavPosition'], 'inside');
        $instance['controlNavVerticalAlign'] = utils::checkString($instance['controlNavVerticalAlign'], 'left');

        $instance['controlSpace'] = utils::checkNum($instance['controlSpace'], 5);
        
        $instance['rotatorThumbsAlign'] = utils::checkString($instance['rotatorThumbsAlign'], 'left');
        $instance['classBtnNext'] = utils::checkString($instance['classBtnNext'], 'evo_next');
        $instance['classBtnPrev'] = utils::checkString($instance['classBtnPrev'], 'evo_prev');
        $instance['classExtLink'] = utils::checkString($instance['classExtLink'], 'evo_link');
        $instance['outerTextPosition'] = utils::checkString($instance['outerTextPosition'], 'right');
        
        $instance['outerTextSpace'] = utils::checkNum($instance['outerTextSpace'], 5);
        
        $instance['linkTarget'] = utils::checkString($instance['linkTarget'], '_blank');
        $instance['imageScale'] = utils::checkString($instance['imageScale'], 'fullSize');

        $this->assign('uniqBoxId', 'toeSliderBox'. mt_rand(1, 9999));
        
        $this->assign('images', $this->prepareImages(frame::_()->getModule('slider_widget')->imagesDataToArr($instance)));
        $this->assign('instance', $instance);
        parent::display('slider');
    }
    public function displayForm($data, $widget) {
        $this->assign('uniqBoxId', 'toeSliderBox'. mt_rand(1, 9999));
        $this->assign('images', frame::_()->getModule('slider_widget')->imagesDataToArr($data));
		$this->displayWidgetForm($data, $widget, 'sliderForm');
    }
    public function addToCart($post) {
        $output = '<a href="'.uri::mod('user', '', 'addToCart', array('pid' => $post->ID)).'">'.lang::_('Add to Cart').'</a>';
        return $output;
    }
    public function prepareImages($images) {
        usort($images, array($this, 'sortImagesCallbackFunc'));
        $useProductsId = array();
        foreach($images as $slideId => $img) {
            if(isset($img['type']) && $img['type'] == 'product') {
                $useProductsId[$slideId] = (int) $img['path'];  //As we save in path parameter product ID for product slides
            }
        }
        if(!empty($useProductsId)) {
            $products = frame::_()->getModule('products')->getProductsPosts(array('include' => $useProductsId));
            if(!empty($products)) {
                global $post;
                if(!empty($post) && is_object($post))
                    $currentPost = clone($post);    //Save current global post data
                $pFields = array(
                    'quantity' => toeCreateObj('field', array('quantity')),
                );
                foreach($products as $p) {
                    $post = $p;
                    $pFields['quantity']->setValue($p->quantity);
                    $this->assign('product', $p);
                    $this->assign('prodImage', frame::_()->getModule('products')->getView()->getProductImage($p));
                    //$this->assign('actionButtons', frame::_()->getModule('products')->getView()->getActionButtons($pFields, true, $p));
                    $this->assign('specialData', frame::_()->getModule('products')->getSpecialModData($p->ID, $p->price));
                    $images[ array_search($p->ID, $useProductsId) ]['desc'] = parent::getContent('productSlide');
                }
                if(!empty($post) && is_object($post))
                    $post = $currentPost;   //restore current global post data
            }
        }
        return $images;
    }
    public function sortImagesCallbackFunc($img1, $img2) {
        if($img1['order'] < $img2['order'])
            return -1;
        elseif($img1['order'] > $img2['order'])
            return 1;
        else
            return 0;
    }
    public function getProductsListHtml() {
        $this->assign('uniqBoxId', req::getVar('uniqBoxId'));
        return parent::getContent('productsListHtml');
    }
}
?>
