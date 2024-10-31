<?php
class imgController extends controller {
    protected $_cacheDir = 'ready_cache';
    public function getProdImg() {
        $enableImgCache = frame::_()->getModule('options')->get('enable_img_cache');
        $cacheOutDone = false;
        if($enableImgCache) {
            $cacheOutDone = $this->tryGetCache();
        }
        if(!$cacheOutDone) {
            $pid = (int) req::getVar('pid');
            $imgId = (int) req::getVar('imgId');
            $for = req::getVar('for');
            $for = empty($for) ? 'big' : $for;
            if($pid && $imgId) {
                $imgData = frame::_()->getModule('img')->getImgData(array('imgId' => $imgId, 'for' => $for));
                if(!empty($imgData)) {
                    $w = req::getVar('w');
                    $h = req::getVar('h');
                    $imgObj = toeCreateObj('simpleImage', array($imgData[0]));
                    if(!empty($w) && !empty($h))
                        $imgObj->resize( $w, $h );
                    elseif(!empty($w))
                        $imgObj->resizeToWidth( $w );
                    elseif(!empty($h))
                        $imgObj->resizeToHeight( $h );
                    $imgObj = dispatcher::applyFilters('prodImgBeforeOutput', $imgObj);
                    $cacheFilePath = $this->_getCacheFilepath();
                    $imgObj->output($cacheFilePath);
                }
            }
        }
        exit();
    }
    public function tryGetCache() {
        if($file = $this->_getCacheFilepath()) {
            if(file_exists($file)) {
                $imageData = getimagesize($file);
                if(!empty($imageData) && !empty($imageData['mime'])) {
                    header('Content-Type: '. $imageData['mime']);
                    echo file_get_contents($file);
                    return true;
                }
            }
        }
        return false;
    }
    protected function _getCacheFilename() {
        $get = req::get('get');
        if(empty($get))
            return false;
        return md5(implode('', $get));
    }
    protected function _getCacheFilepath() {
        if($filename = $this->_getCacheFilename()) {
            $uploadsDir = wp_upload_dir();
            $cahceDir = $uploadsDir['basedir']. DS. $this->_cacheDir. DS;
            if(!is_dir($cahceDir)) {
                utils::createDir($cahceDir, array('chmod' => 755, 'httpProtect' => true));
            }
            $cacheFile = $cahceDir. $filename;
            return $cacheFile;
        }
        return false;
    }
}
?>
