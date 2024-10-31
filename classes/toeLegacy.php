<?php
/**
 * Provide some deprecated methods and events for wordpress that is need in our opinion
 */
class toeLegacy {
	static public function init() {
		if(version_compare(get_bloginfo('version'), '3.4.9.9', '>')) {
			add_action('admin_bar_menu', array('toeLegacy', 'adminBarEditContentMenu'), 90);
		}
	}
	static public function adminBarEditContentMenu($wp_admin_bar) {
		global $post, $tag, $wp_the_query;

		if ( is_admin() ) {
			$current_screen = get_current_screen();

			if ( 'post' == $current_screen->base
				&& 'add' != $current_screen->action
				&& ( $post_type_object = get_post_type_object( $post->post_type ) )
				&& current_user_can( $post_type_object->cap->read_post, $post->ID )
				&& ( $post_type_object->public ) )
			{
				$wp_admin_bar->add_menu( array(
					'id' => 'view',
					'title' => $post_type_object->labels->view_item,
					'href' => get_permalink( $post->ID )
				) );
			} elseif ( 'edit-tags' == $current_screen->base
				&& isset( $tag ) && is_object( $tag )
				&& ( $tax = get_taxonomy( $tag->taxonomy ) )
				&& $tax->public )
			{
				$wp_admin_bar->add_menu( array(
					'id' => 'view',
					'title' => $tax->labels->view_item,
					'href' => get_term_link( $tag )
				) );
			}
		} else {
			$current_object = $wp_the_query->get_queried_object();

			if ( empty( $current_object ) )
				return;

			if ( ! empty( $current_object->post_type )
				&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
				&& current_user_can( $post_type_object->cap->edit_post, $current_object->ID )
				&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type ) )
			{
				$wp_admin_bar->add_menu( array(
					'id' => 'edit',
					'title' => $post_type_object->labels->edit_item,
					'href' => get_edit_post_link( $current_object->ID )
				) );
			} elseif ( ! empty( $current_object->taxonomy )
				&& ( $tax = get_taxonomy( $current_object->taxonomy ) )
				&& current_user_can( $tax->cap->edit_terms )
				&& $tax->show_ui )
			{
				$wp_admin_bar->add_menu( array(
					'id' => 'edit',
					'title' => $tax->labels->edit_item,
					'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy )
				) );
			}
		}
	}
}
?>
