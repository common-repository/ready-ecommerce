<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('by_price')?>"><?php lang::_e('By Price')?>:</label>
    <?php
        echo html::checkbox($this->widget->get_field_name('by_price'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('by_price'). '"',
            'value' => 1,
            'checked' => $this->data['by_price'],
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('by_title_desc')?>"><?php lang::_e('By Title and Description')?>:</label>
    <?php
        echo html::checkbox($this->widget->get_field_name('by_title_desc'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('by_title_desc'). '"',
            'value' => 1,
            'checked' => $this->data['by_title_desc'],
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('by_options')?>"><?php lang::_e('By Options')?>:</label>
    <?php
        echo html::checkbox($this->widget->get_field_name('by_options'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('by_options'). '"',
            'value' => 1,
            'checked' => $this->data['by_options'],
        ));
    ?><br />
</p>