<?php

/**
 * Class for breadcrumbs module
 * Module represents the widget of breadcrumbs
 * Block with breadcrumbs have id "breadcrumbs"
 * First crumb have class "first-crumb"
 * Last crumb have class "last-crumb"
 * Spliter have class "spliter"
 */

class breadcrumbs extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeBrCWidget');
    }
    public function toe_breadcrumbs($home, $sep='<div class="crumb-spliter"></div>', $term=false, $taxonomies=false ){
        global $post, $wp_query, $wp_post_types, $wpdb;
		$data = array(
			array('title' => $home, 'href' => get_bloginfo('url'))
		);
		$addData = $this->getCurrData($post);
		if(!empty($addData) && is_array($addData)) {
			$data = array_merge($data, $addData);
		}
		$data = dispatcher::applyFilters('breadcrumbsData', $data);
		$links = array();
		$lastElIter = count($data) - 1;		//Iterator value for last element in data array
		foreach($data as $i => $d) {
			if($lastElIter == $i)
				$links[] = '<span class="last-crumb">'.$d['title'].'</span>';
			else
				$links[] = '<a href="'. $d['href']. '">'. $d['title']. '</a>';
		}
		return implode($sep, $links);
      /*  
		$l = (object) array(
            'home' => $home
            ,'paged' => __('Page %s')
            ,'p404' => __('Error 404')
            ,'search' => __('Search results for - <b>%s</b>')
            ,'author' => __('Autor\'s archive: <b>%s</b>')
            ,'year' => __('Archive by <b>%s</b> year')
            ,'month' => __('Archive by: <b>%s</b>')
            ,'attachment' => __('Attachment: %s')
            ,'tag' => __('Post by tag: <b>%s</b>')
            ,'tax_tag' => __('%s from "%s" by tag: <b>%s</b>')
        );
        
        $catPageTempl = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = 'template-categories.php'");
            $catPageId = $catPageTempl->post_id;
            $catPageLink = get_permalink($catPageId);
            $catPageTitle = get_the_title($catPageId);
        $brandPageTempl = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = 'template-brands.php'");
            $brandPageId = $brandPageTempl->post_id;
            $brandPageLink = get_permalink($brandPageId);
            $brandPageTitle = get_the_title($brandPageId);
                                  
        if( $paged = $wp_query->query_vars['paged'] ){
            $pg_patt = '<a href="%s">';
            $pg__nd = '</a>'. $sep . sprintf($l->paged, $paged);
        }

        if( is_front_page() )
            return print ($paged?sprintf($pg_patt, get_bloginfo('url')):'') . $l->home . $pg__nd;

        if( is_404() )
            $out = $l->p404; 

        elseif( is_search() ){
            $s = preg_replace('@<script.*@i', 'invalid characters', $GLOBALS['s']);
            $out = sprintf($l->search, $s);
        }
        elseif( is_author() ){
            $q_obj = &$wp_query->queried_object;
            $out = ($paged?sprintf( $pg_patt, get_author_posts_url($q_obj->ID, $q_obj->user_nicename) ):'') . sprintf($l->author, $q_obj->display_name) . $pg__nd;
        }
        elseif( is_year() || is_month() || is_day() ){
            $y_url = get_year_link( $year=get_the_time('Y') );
            $m_url = get_month_link( $year, get_the_time('m') );
            $y_link = '<a href="'. $y_url .'">'. $year .'</a>';
            $m_link = '<a href="'. $m_url .'">'. get_the_time('F') .'</a>';
            if( is_year() )
                $out = ($paged?sprintf($pg_patt, $y_url):'') . sprintf($l->year, $year) . $pg__nd;
            elseif( is_month() )
                $out = $y_link . $sep . ($paged?sprintf($pg_patt, $m_url):'') . sprintf($l->month, get_the_time('F')) . $pg__nd;
            elseif( is_day() )
                $out = $y_link . $sep . $m_link . $sep . get_the_time('l');
        }

        // Pages and tree viewed posts
        elseif( $wp_post_types[$post->post_type]->hierarchical ){
            $parent = $post->post_parent;
            $crumbs=array();
            while($parent){
              $page = &get_post($parent);
              $crumbs[] = '<a href="'. get_permalink($page->ID) .'" title="">'. $page->post_title .'</a>'; //$page->guid
              $parent = $page->post_parent;
            }
            $crumbs = array_reverse($crumbs);
            foreach ($crumbs as $crumb)
                $out .= $crumb.$sep;
            $out = $out . '<span class="last-crumb">' . $post->post_title . '</span>';
        }
        else // Taxonomy, attachments and non-tree viewed
        {
            // definition of terms
            if(!$term){
                if( is_single() ){
                    if( !$taxonomies ){
                        $taxonomies = get_taxonomies( array('hierarchical'=>true, 'public'=>true) );
                        if( count($taxonomies)==1 ) $taxonomies = 'category';
                    }
                    if ( !$post ) $post = $wp_query->get_queried_object(); // define a global variable to work without a pattern!
                    if( $term = get_the_terms( $post->post_parent?$post->post_parent:$post->ID, $taxonomies ) )
                        $term = array_shift($term);
                }
                else
                    $term = $wp_query->get_queried_object();
            }
            if( !$term && !is_attachment() )
                $out = "{$sep}<span class=\"last-crumb\">{$post->post_title}</span>"; 

            $pg_term_start = $paged ? sprintf( $pg_patt, get_term_link( (int)$term->term_id, $term->taxonomy ) ) : '';

            if( is_attachment() ){
                if(!$post->post_parent)
                    $out = sprintf($l->attachment, $post->post_title);
                else
                    $out = frame::_()->getModule('breadcrumbs')->crumbs_tax( (int)$term->term_id, $term->taxonomy, $sep) . "<a href='". get_permalink($post->post_parent) ."'>". get_the_title($post->post_parent) ."</a>{$sep}<span class=\"last-crumb\">{$post->post_title}</span>"; //$ppost->guid
            }
            elseif( is_single() ){
                if($term->taxonomy == 'products_categories') {
                    $out = '<a href="'.$catPageLink.'">'.$catPageTitle.'</a>'.$sep;
                } elseif ($term->taxonomy == 'products_brands') {
                    $out = '<a href="'.$brandPageLink.'">'.$brandPageTitle.'</a>'.$sep;
                }
                if ($term->term_id != '') {
                    $out .= frame::_()->getModule('breadcrumbs')->crumbs_tax($term->term_id, $term->taxonomy, $sep) . "<span class=\"last-crumb\">{$post->post_title}</span>";
                } else {$out = "<span class=\"last-crumb\">{$post->post_title}</span>";}
            }
            // Tags sibling or arbitrary taxonomy
            elseif( !is_taxonomy_hierarchical($term->taxonomy) ){
                if( is_tag() )
                    $out = $pg_term_start . sprintf($l->tag, $term->name) . $pg__nd;
                else {
                    $post_label = $wp_post_types[$post->post_type]->labels->name;
                    $tax_label = $GLOBALS['wp_taxonomies'][$term->taxonomy]->labels->name;
                    $out = $pg_term_start . sprintf($l->tax_tag, $post_label, $tax_label, $term->name) .  $pg__nd;
                }
            }// Category and taxonomy
            else
                $out = frame::_()->getModule('breadcrumbs')->crumbs_tax($term->parent, $term->taxonomy, $sep) . $pg_term_start . $term->name . $pg__nd;
        }

        $home = '<a href="'. get_bloginfo('url') .'">'. $l->home .'</a>' . $sep;

        return $home . $out;*/
    }
	public function getCurrData($post) {
		$res = array(
			//array('title' => 'Home', 'href' => ''),
		);
		$selectedTax = get_queried_object();
		if(!empty($selectedTax) && is_object($selectedTax) && isset($selectedTax->slug)) {
			$res[] = array('title' => $selectedTax->name, 'href' => get_term_link($selectedTax, $selectedTax->slug));
			$parentsIds = get_ancestors($selectedTax->term_id, $selectedTax->taxonomy);
			if(!empty($parentsIds) && is_array($parentsIds)) {
				foreach($parentsIds as $parentId) {
					$parentTerm = get_term($parentId, $selectedTax->taxonomy);
					if(!empty($parentTerm) && is_object($parentTerm) && !is_wp_error($parentTerm)) {
						$res[] = array(
							'title' => $parentTerm->name, 
							'href' => get_term_link($parentTerm, $parentTerm->slug)
						);
					}
				}
				$res = array_reverse($res);
			}
		} elseif((is_single() || is_page()) && !empty($post) && is_object($post) && isset($post->post_type)) {
			$taxes = array();
			if($post->post_type == S_PRODUCT) {
				$taxes = frame::_()->getModule('products')->getCategories(array('pid' => $post->ID, 'orderByParents' => true));	//Try to get categories at first
				if(empty($taxes)) {
					$taxes = frame::_()->getModule('products')->getBrands(array('pid' => $post->ID, 'orderByParents' => true));	//Than try to get brands
				}
			} else {
				$catIds = wp_get_post_categories($post->ID);
				if(!empty($catIds) && is_array($catIds)) {
					foreach($catIds as $cid) {
						$taxes[] = get_category($cid);
					}
				}
			}
			if(!empty($taxes) && is_array($taxes)) {
				foreach($taxes as $t) {
					$res[] = array(
						'title' => $t->name, 
						'href' => get_term_link($t, $t->slug)
					);
				}
			}
			$res = array_merge($res, $this->getListOfParents($post));
			$res[] = array(
				'title' => get_the_title(),
			);
		}
		return $res;
	}
	public function getListOfParents($post) {
		$res = array();
		if(isset($post->post_parent) && !empty($post->post_parent)) {
			$parent = get_post($post->post_parent);
			$res[] = array(
				'title' => $parent->post_title,
				'href' => get_permalink($parent->ID),
			);
			if(isset($parent->post_parent) && !empty($parent->post_parent)) {
				$res = array_merge($this->getListOfParents($parent), $res);
			}
		}
		return $res;
	}
    public function crumbs_tax($term_id, $tax, $sep){
        $termlink = array();
        $prev = '';
        while( (int)$term_id ){
            $term2 = get_term( $term_id, $tax );
            $termlink[] = '<a href="'. get_term_link( (int)$term2->term_id, $term2->taxonomy ) .'">'. $term2->name .'</a>'. $sep;
            $term_id = (int)$term2->parent;
        }
        $termlinks = array_reverse($termlink);
        return implode('', $termlinks);
    }
}
/**
 * Featured Products Widget Class
 */
class toeBrCWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeBrCWidget', 
            'description' => lang::_('Displays breadcrumbs')
        );
        $control_ops = array(
            'id_base' => 'toeBrCWidget'
        );
	parent::__construct( 'toeBrCWidget', lang::_('Ready! Breadcrumbs'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('breadcrumbs')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('breadcrumbs')->getView()->displayForm($instance, $this);
    }
}