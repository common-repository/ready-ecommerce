<?php
class toecimg extends module {
	public function init() {
		parent::init();
		dispatcher::addFilter('imgSrc', array($this, 'prepareImgSrc'));
	}
    public function getImgData($d = array('imgId' => 0, 'for' => '')) {
        $res = array();
        if($d['imgId']) {
            switch($d['for']) {
                case 'thumb':
                    $res = wp_get_attachment_image_src($d['imgId'], 'product-display');
                    if (empty($res)) {
                        $res = image_downsize($d['imgId'], 'product-display');
                    }
                    break;
            case 'big':
            default:
                $res = wp_get_attachment_image_src($d['imgId'], 'full');
                break;
            }
        }
        return $res;
    }
	// TODO: Insert validation of img path
	public function prepareImgSrc($src) {
		/*$fileSrc = $src;
		if(strpos($fileSrc, 'http')) {
			$fileSrc = $fileSrc
		}*/
		return $src;
	}
}
?>
