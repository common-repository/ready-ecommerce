<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('number_of_products')?>"><?php lang::_e('Number of Products')?>:</label>
    <?php
        echo html::text($this->widget->get_field_name('number_of_products'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('number_of_products'). '"',
            'value' => $this->data['number_of_products'],
        ));
    ?><br />
    <?php $options = array('0' => lang::_('No'), '1' => lang::_('Yes'));?>
    <label for="<?php echo $this->widget->get_field_id('show_price')?>"><?php lang::_e('Show Price')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('show_price'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('show_price'). '"',
            'value' => $this->data['show_price'],
            'options' => $options,
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('show_add_to_cart')?>"><?php lang::_e('Show Add To Cart Button')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('show_add_to_cart'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('show_add_to_cart'). '"',
            'value' => $this->data['show_price'],
            'options' => $options,
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('categories')?>"><?php lang::_e('Use Categories')?>:</label>
    <?php
        echo html::selectlist($this->widget->get_field_name('categories'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('categories'). '"',
            'value' => $this->data['categories'],
            'options' => $this->categories,
        ));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('brands')?>"><?php lang::_e('Use Brands')?>:</label>
    <?php
        echo html::selectlist($this->widget->get_field_name('brands'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('brands'). '"',
            'value' => $this->data['brands'],
            'options' => $this->brands,
        ));
    ?><br />
</p>