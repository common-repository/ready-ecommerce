<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
</p>