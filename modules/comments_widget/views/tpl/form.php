<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('order')?>"><?php lang::_e('Show comments in next Order')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('order'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('order'). '"',
            'value' => $this->data['order'],
            'options' => array('recent' => 'recent', 'top_rated' => 'top_rated'),
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('number_of_comments')?>"><?php lang::_e('Number of Comments')?>:</label>
    <?php
        echo html::text($this->widget->get_field_name('number_of_comments'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('number_of_comments'). '"',
            'value' => $this->data['number_of_comments'],
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('comment_len')?>"><?php lang::_e('Comment content Max Length')?>:</label>
    <?php
        echo html::text($this->widget->get_field_name('comment_len'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('comment_len'). '"',
            'value' => $this->data['comment_len'],
        ));
    ?><br />
    <?php $options = array('0' => lang::_('No'), '1' => lang::_('Yes'));?>
    <label for="<?php echo $this->widget->get_field_id('show_product_link')?>"><?php lang::_e('Show Product Link')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('show_product_link'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('show_product_link'). '"',
            'value' => $this->data['show_product_link'],
            'options' => $options,
        ));
    ?><br />
</p>