<p>
    <label for="<?php echo $this->widget->get_field_id('home_title')?>"><?php lang::_e('Home title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('home_title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('home_title'). '"', 
            'value' => $this->data['home_title']));
    ?><br />
</p>