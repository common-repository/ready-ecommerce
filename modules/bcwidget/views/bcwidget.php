<?php
class bcwidgetView extends view {
    public function display($instance) {
        switch($instance['list']) {
            case products::BRANDS:
                $data = frame::_()->getModule('products')->getBrands();
                break;
            case products::CATEGORIES:
            default:
                $data = frame::_()->getModule('products')->getCategories();
                break;
        }
        if(empty($data)) return;
        $selectedId = 0;
        $currentCatSlug = get_query_var( frame::_()->getModule('products')->getConstant('CATEGORIES') );
        if(!empty($currentCatSlug)) {
            $selectedCat = get_term_by('slug', $currentCatSlug, frame::_()->getModule('products')->getConstant('CATEGORIES'));
            $selectedId = $selectedCat->term_taxonomy_id;
        }
        
        $parents = array();
        $mustBeOpened = array();
        for($i = 0; $i < count($data); $i++) {
            
			switch($instance['list']) {
				case products::BRANDS:
					$data[$i]->href = frame::_()->getModule('products')->getLinkToBrand($data[$i]);
					$data[$i]->image = frame::_()->getModule('products')->getBrandImage($data[$i]);
					break;
				case products::CATEGORIES:
				default:
					$data[$i]->href = frame::_()->getModule('products')->getLinkToCategory($data[$i]);
					$data[$i]->image = frame::_()->getModule('products')->getCategoryImage($data[$i]);
					break;
			}
            $data[$i]->allParents = $this->_searchAllParents($data, $data[$i]);
            if($data[$i]->parent)  $parents[] = $data[$i]->parent;
            if($data[$i]->cat_ID == $selectedId) {
                $mustBeOpened[] = (int) $data[$i]->cat_ID;
                if(!empty($data[$i]->allParents)) {
                    //foreach($data[$i]->allParents as $parentId) {}
                    $mustBeOpened = array_merge($mustBeOpened, $data[$i]->allParents);
                }
            }
        }
        for($i = 0; $i < count($data); $i++) {
            if(in_array($data[$i]->cat_ID, $parents)) 
                $data[$i]->haveChildren = true;
            else
                $data[$i]->haveChildren = false;
            
            if(in_array($data[$i]->cat_ID, $mustBeOpened)) 
                $data[$i]->opened = true;
            else
                $data[$i]->opened = false;
        }

		if(frame::_()->getModule('options')->get('show_subcategories_if_exist')) {
			$tempData = array();
			// Use only top-level categories for this case
			foreach($data as $i => $cat) {
				if(empty($cat->parent))
					$tempData[] = $cat;
			}
			$data = $tempData;
		}

        $this->assign('data', $data);
        $this->assign('uniqID', mt_rand(1, 99999));
        $this->assign('title', $instance['title']);
        $this->assign('imgWidth', frame::_()->getModule('options')->get('product_preview_width'));
        $this->assign('mustBeOpened', $mustBeOpened);
        if ($instance['view']) {
            parent::display('imageList');
        } else {
            parent::display('list');
        }
    }
    public function displayForm($data, $widget) {
		$this->displayWidgetForm($data, $widget);
    }
    protected function _searchAllParents($data, $element) {
        $res = array();
        if(empty($element->parent)) return $res;
        for($i = 0; $i < count($data); $i++) {
            if($data[$i]->cat_ID == $element->parent) {
                $res[] = (int) $data[$i]->cat_ID;
                if(!empty($data[$i]->parent))
                    $res = array_merge($res, $this->_searchAllParents($data, $data[$i]));   
            }
        }
        return $res;
    }
}
?>