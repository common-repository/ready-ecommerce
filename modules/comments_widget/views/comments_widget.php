<?php
class comments_widgetView extends view {
    public function display($instance) {
        global $wpdb;
        $rTable = frame::_()->getTable('rating')->getTable(true);    //rates table name
        $fields = array('c.*', 'r.rate AS toeRate', 'p.post_title');
        $tables = array($wpdb->comments. ' c');
        $join = array('INNER JOIN '. $wpdb->posts. ' p ON p.ID = c.comment_post_ID', 'LEFT JOIN '. $rTable. ' r ON r.comment_id = c.comment_ID');
        $where = array('p.post_type = "'. S_PRODUCT. '"', 'p.post_status = "publish"', 'c.comment_approved = "1"');
        $order = '';
        $limit = '';
        switch($instance['order']) {
            case 'top_rated':
                $order = 'r.rate DESC';
                break;
            case 'recent':
            default:
                $order = 'c.comment_ID DESC';
                break;
        }
        $query = 'SELECT '. implode(', ', $fields). 
                ' FROM '. implode(', ', $tables). 
                ' '. implode(' ', $join). 
                ' WHERE '. implode(' AND ', $where);
        if(!empty($order)) {
            $query .= ' ORDER BY '. $order;
        }
        if(!empty($instance['number_of_comments']) && is_numeric($instance['number_of_comments'])) {
            $query .= ' LIMIT '. (int) $instance['number_of_comments'];
        }
        $comments = db::get($query);

        $rateStarsCount = 0;    //Number of stars from rating module, as this module can not be installed by default - it is in licensed version
        if(frame::_()->getModule('rating')) {
            $rateStarsCount = frame::_()->getModule('rating')->getParam('maxStars');
        }
        $this->assign('rateStarsCount', $rateStarsCount);
        $this->assign('instance', $instance);
        $this->assign('comments', $comments);
        parent::display('list');
    }
    public function displayForm($data, $widget) {
        $this->displayWidgetForm($data, $widget);
    }
}