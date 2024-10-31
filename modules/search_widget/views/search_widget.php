<?php
class search_widgetView extends view {
    public function display($instance) {
        global $wp_query;
        $fields = array();
        $toeSearch = req::getVar('toeSearch');
        if($instance['by_price']) {
            $fields['priceFrom'] = toeCreateObj('field', array('toeSearch[priceFrom]'));
            $fields['priceTo'] = toeCreateObj('field', array('toeSearch[priceTo]'));
            $toeSearch['priceFrom'] = (float) $toeSearch['priceFrom'];
            $toeSearch['priceTo'] = (float) $toeSearch['priceTo'];
            if($toeSearch['priceFrom'])
                $fields['priceFrom']->setValue($toeSearch['priceFrom']);
            if($toeSearch['priceTo'])
                $fields['priceTo']->setValue($toeSearch['priceTo']);
        }
        if($instance['by_title_desc']) {
            $s = req::getVar('s');
            $fields['s'] = toeCreateObj('field', array('s'));
            if(!empty($s))
                $fields['s']->setValue($s);
        }
        if($instance['by_options']) {
            $currentCatSlug = get_query_var( frame::_()->getModule('products')->getConstant('CATEGORIES') );
            $getForCategory = array();
            if(!empty($currentCatSlug)) {
                $term = get_term_by('slug', $currentCatSlug, frame::_()->getModule('products')->getConstant('CATEGORIES'));
                if(!empty($term->term_taxonomy_id)) {
                    $getForCategory[] = $term->term_taxonomy_id;
                    $fields['taxonomy'] = toeCreateObj('field', array('taxonomy', 'hidden'));
                    $fields['taxonomy']->setValue($currentCatSlug);
                }
            }
            $extraOptionsFromDb = frame::_()->getModule('options')->getModel('productfields')->getProductExtraField(array(), array('getForCategory' => $getForCategory));
            if(!empty($extraOptionsFromDb)) {
                $i = 0;
                foreach($extraOptionsFromDb as $opt) {
                    if(in_array($opt->getHtml(), array('checkboxlist', 'selectbox', 'radiobuttons', 'selectlist'))) {
                        $fields['exOptions'][$i] = toeCreateObj('field', array('toeSearch[exOptions]['. $opt->getID(). ']', 'selectbox'));
                        switch($opt->getHtml()) {
                            case 'checkboxlist':
                                $htmlOptions = array();
                                foreach($opt->getHtmlParam('options') as $htmOpt) {
                                    $htmlOptions[ $htmOpt['id'] ] = $htmOpt['text'];
                                }
                                break;
                            default:
                                $htmlOptions = $opt->getHtmlParam('options');
                                break;
                        }
                        $fields['exOptions'][$i]->addHtmlParam('options', $htmlOptions);
                        $fields['exOptions'][$i]->setLabel( $opt->getLabel() );
                        if(!empty($toeSearch['exOptions'][ $opt->getID() ]))
                            $fields['exOptions'][$i]->setValue( $toeSearch['exOptions'][ $opt->getID() ] );
                        $i++;
                    }
                }
            }
        }
        $this->assign('fields', $fields);
        $this->assign('uniqID', mt_rand(1, 99999));
        $this->assign('params', $instance);
        parent::display('searchWidgetForm');
    }
    public function displayForm($data, $widget) {
        $this->displayWidgetForm($data, $widget);
    }
    public function addToCart($post) {
        $output = '<a href="'.uri::mod('user', '', 'addToCart', array('pid' => $post->ID)).'">'.lang::_('Add').'</a>';
        return $output;
    }
    protected function _extractPost($posts, $pid) {
        foreach($posts as $p) {
            if($p->ID == $pid)
                return $p;
        }
        return false;
    }
}
?>