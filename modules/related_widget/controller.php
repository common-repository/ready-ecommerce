<?php
class related_widgetController extends controller {
    
        /**
         * Add Relation
         */
        public function relatedAdd(){
            $administrator = current_user_can('administrator');
            if (!$administrator){
                die(lang::_e("You don't have permissions to access this page"));
            }
			$id = $_POST['id'];
			$rel_to = $_POST['rel_to'];
			
			frame::_()->getModule('related_widget')->addRelated($id, $rel_to);
			$this->getView('relatedProduct')->updateAjax('downloads',$id,"New relation added");
              //  die(lang::_e("You don't have permissions to access this page"));
            die();
        }
        /**
         * Deletes Relation
         */
        public function relatedDelete(){
            $administrator = current_user_can('administrator');
            if (!$administrator){
                die(lang::_e("You don't have permissions to access this page"));
            }
			$id = $_POST['id'];
			$rel_from = $_POST['rel_from'];
			
			frame::_()->getModule('related_widget')->removeRelated($rel_from);
			$this->getView('relatedProduct')->updateAjax('downloads',$id,"Relation deleted");
              //  die(lang::_e("You don't have permissions to access this page"));
            die();
        }
}
?>