<p>
	<label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title:')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <?php $options = array('0' => lang::_('With images'), 
                           '1' => lang::_('Only text'));?>
    <label for="<?php echo $this->widget->get_field_id('list_view')?>"><?php lang::_e('List type:')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('list_view'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('list_view'). '"',
            'value' => $this->data['list_view'],
            'options' => $options,
        ));
    ?><br />
    <?php $options = array('0' => lang::_('Thumb'), 
                           '1' => lang::_('Big'));?>
    <label for="<?php echo $this->widget->get_field_id('img_size')?>"><?php lang::_e('Image size:')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('img_size'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('img_size'). '"',
            'value' => $this->data['img_size'],
            'options' => $options,
        ));
    ?><br />
</p>