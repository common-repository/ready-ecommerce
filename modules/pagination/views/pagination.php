<?php
class paginationView extends view {
    public function display($d = array('show' => array('navigation', 'perPage', 'ordering'), 'nav_id' => '')) {
        if(!is_array($d) && !empty($d)) 
            $d = array('nav_id' => $d);
        if(!is_array($d['show'])) 
            $d['show'] = array('navigation', 'perPage', 'ordering');
		
		$this->displayStart();
		
        if(in_array('navigation', $d['show']))
            $this->displayNavigation(array('nav_id' => $d['nav_id']));
        if(in_array('perPage', $d['show']))
            $this->displayPerPage();
        if(in_array('ordering', $d['show']))
            $this->displayOrdering();
		
		$this->displayEnd();
    }
	public function displayStart() {
		parent::display('paginationStart');
	}
	public function displayEnd() {
		parent::display('paginationEnd');
	}
    public function displayNavigation($d = array('nav_id' => '')) {
        global $wp_query, $paged;
        $this->assign('nav_id', $d['nav_id']);
        $this->assign('wp_query', $wp_query);
        $this->assign('paged', $paged);
        parent::display('navigation');
    }
    public function displayPerPage() {
        $postsPerPageValues = array(20, 50, 100, 'all');
        $this->assign('postsPerPageValues', $postsPerPageValues);
        $this->assign('baseHrefForPerPage', remove_query_arg('posts_per_page'));
        $this->assign('posts_per_page', req::getVar('posts_per_page'));
        parent::display('perPage');
    }
    public function displayOrdering() {
        $orderByValues = array(
            'price' => array(
                'orderby' => 'price', 
                'desc' => array('ASC' => '&darr;', 'DESC' => '&uarr;'), 
                'label' => 'Price'
            ),
            'date' => array(
                'orderby' => 'date', 
                'desc' => array('ASC' => '&darr;', 'DESC' => '&uarr;'), 
                'label' => 'Date'
            ),
        );
        $this->assign('orderByValues', $orderByValues);
        $this->assign('baseHrefForOrdering', remove_query_arg(array('orderby', 'order')));
        parent::display('ordering');
    }
}
?>
