<?php
class adminmenu extends module {
    public function init() {
        parent::init();
        $this->getView('adminmenu')->init();
    }
}
?>
