<?php
class slider_widgetController extends controller {
    public function addFile() {
        $res = new response();
        $uploader = toeCreateObj('fileuploader', array());
        $fieldName = key($_FILES);
        if($uploader->validate($fieldName, frame::_()->getModule('slider_widget')->getSlidesDir())) {
            $uploader->upload();
            $resData = array_merge( $uploader->getFileInfo(), array('fieldName' => $fieldName) );
            $resData['path'] = frame::_()->getModule('slider_widget')->getFullImgPath( $resData['path'] );
            $resData['type'] = 'slide';
            $res->addData( $resData );
            $res->addMessage(lang::_('Uploaded'));
        } else 
            $res->pushError( $upload->getError() );
        return $res->ajaxExec();
    }
    public function removeFile() {
        $res = new response();
        $filePath = explode('/', req::getVar('filePath'));
        $fileName = $filePath[ count($filePath)-1 ];
        if(!empty($fileName)) {
            frame::_()->getTable('product_files')->delete(array('path' => $fileName));
            utils::deleteFile( frame::_()->getModule('slider_widget')->getFullImgDir($fileName) );
        }
        return $res->ajaxExec();
    }
    public function getProductsListHtml() {
        $res = new response();
        $res->setHtml($this->getView()->getProductsListHtml());
        return $res->ajaxExec();
    }
}
?>