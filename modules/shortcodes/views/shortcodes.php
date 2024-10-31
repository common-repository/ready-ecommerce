<?php
class shortcodesView extends view {
    public function adminTextEditorPopup() {
        $shortcodes = frame::_()->getModule('shortcodes')->getCodes();
        $shortcodesSelectOptions = array('' => lang::_('Select'));
        foreach($shortcodes as $code => $cinfo) {
            if(in_array($code, array('product', 'category'))) continue;
            $shortcodesSelectOptions[ $code ] = $code;
        }
        $this->assign('shortcodes', $shortcodes);
        $this->assign('shortcodesSelectOptions', $shortcodesSelectOptions);
        return parent::getContent('adminTextEditorPopup');
    }
}
?>
