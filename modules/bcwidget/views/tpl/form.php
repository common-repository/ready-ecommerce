<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('list')?>"><?php lang::_e('List')?>:</label>
    <?php
        $options = array(
            products::CATEGORIES => lang::_('Categories'),
            products::BRANDS => lang::_('Brands')
        );
        echo html::selectbox($this->widget->get_field_name('list'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('list'). '"',
            'value' => $this->data['list'],
            'options' => $options
        ));
    ?> <br />
    <label for="<?php echo $this->widget->get_field_id('view')?>"><?php lang::_e('View')?>:</label>
    <?php
        $options = array(
            '0' => lang::_('Tree View'),
            '1' => lang::_('Image View')
        );
        echo html::selectbox($this->widget->get_field_name('view'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('view'). '"',
            'value' => $this->data['view'],
            'options' => $options
        ));
    ?> <br />
</p>