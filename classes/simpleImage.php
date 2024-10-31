<?php
class simpleImage {
 
   protected $_image;
   protected $_imageType;
   protected $_filename;
   protected $_imageInfo;
   protected $_imageInfoToKeys = array('width' => 0, 'height' => 1, 'type' => 2, 'sizeStr' => 3, 'mime' => 'mime');

   public function __construct($filename = '') {
       if(!empty($filename)) {
           $this->setFilename( $filename );
           $this->load();
       }
   }
   protected function _prepareFilename($filename) {
       if(strpos($filename, S_URL) === 0) {   //Use file path instead of URL
            $filename = str_replace(S_URL, ABSPATH, $filename);
        }
        return $filename;
   }
   public function __destruct() {
        if($this->_image)
            imagedestroy($this->_image);
   }
   public function setFilename($filename) {
       $this->_filename = $this->_prepareFilename( $filename );
   }
   public function getFilename() {
       return $this->_filename;
   }
   public function getImgHandle() {
       return $this->_image;
   }
   public function setImgHandle($h) {
       $this->_image = $h;
   }
   public function getImageInfo($k = NULL) {
       if(!is_null($k)) {
           if(is_string($k) && isset($this->_imageInfoToKeys[ $k ])) {
               return $this->_imageInfo[ $this->_imageInfoToKeys[ $k ] ];
           } elseif(isset($this->_imageInfo[ $k ])) {
               return $this->_imageInfo[ $k ];
           }
       }
       return $this->_imageInfo;
   }
   public function calcInfo() {
       $this->_imageInfo = getimagesize($this->_filename);
   }
   public function load($filename = '') {
       if(!empty($filename))
           $this->setFilename( $filename );
      $this->_imageInfo = getimagesize($this->_filename);
      $this->_imageType = $this->_imageInfo[2];

      if( $this->_imageType == IMAGETYPE_JPEG ) {
 
         $this->_image = imagecreatefromjpeg($this->_filename);
      } elseif( $this->_imageType == IMAGETYPE_GIF ) {
 
         $this->_image = imagecreatefromgif($this->_filename);
      } elseif( $this->_imageType == IMAGETYPE_PNG ) {
 
         $this->_image = imagecreatefrompng($this->_filename);
      } elseif( $this->_imageType == IMAGETYPE_WBMP ) {
 
         $this->_image = imagecreatefromwbmp($this->_filename);
      }
   }
   public function save($filename, $compression=75, $permissions=null) {
      if( $this->_imageType == IMAGETYPE_JPEG ) {
         imagejpeg($this->_image,$filename,$compression);
      } elseif( $this->_imageType == IMAGETYPE_GIF ) {
 
         imagegif($this->_image,$filename);
      } elseif( $this->_imageType == IMAGETYPE_PNG ) {
 
         imagepng($this->_image,$filename);
      } elseif( $this->_imageType == IMAGETYPE_WBMP ) {
 
         imagewbmp($this->_image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename, $permissions);
      }
   }
    public function output($saveFilename = NULL) {
        $header = '';
        $func = '';
        if( $this->_imageType == IMAGETYPE_JPEG ) {
            $func = 'imagejpeg';
        } elseif( $this->_imageType == IMAGETYPE_GIF ) {
            $func = 'imagegif';
        } elseif( $this->_imageType == IMAGETYPE_PNG ) {
            $func = 'imagepng';
        } elseif( $this->_imageType == IMAGETYPE_WBMP ) {
            $func = 'imagewbmp';
        }
        if($func) {
            if($saveFilename) {
                $this->save( $saveFilename );
            }
            header('Content-Type: '. $this->getImageInfo('mime'));
            $func( $this->_image );
        }
    }
   public function getWidth() {
      return imagesx($this->_image);
   }
   public function getHeight() {
      return imagesy($this->_image);
   }
   public function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   public function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   public function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
    public function resize($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);

        imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        
        imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->_image = $new_image;
        $this->_imageInfo[0] = $width;
        $this->_imageInfo[1] = $height;
    }
   public function rotate($angle) {
       $this->_image = imagerotate($this->_image, $angle, imagecolorallocate($this->_image, 255, 255, 255));
   }
   public function makeWhiteTransparent() {
        imagecolortransparent($this->_image, imagecolorallocate($this->_image, 255, 255, 255));
   }
   public function imagesavealpha($flag) {
       imagesavealpha($this->_image, $flag);
   }
   public function imagealphablending($flag) {
       imagealphablending($this->_image, $flag);
   }
}
?>