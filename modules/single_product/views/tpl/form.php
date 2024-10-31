<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Title')?>:</label>
    <?php 
        echo html::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => $this->data['title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('product_id')?>"><?php lang::_e('Product to show')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('product_id'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('product_id'). '"',
            'value' => $this->data['product_id'],
            'options' => $this->products,
        ));
    ?><br />
    <?php $options = array('0' => lang::_('Only Thumbnail Image'), 
                           '1' => lang::_('Only Big Image'),
                           '2' => lang::_('Both Thumbnails and Big Image'),
                           '3' => lang::_('Single Product Widget Image'),
                           '4' => lang::_('No Images'));?>
    <label for="<?php echo $this->widget->get_field_id('image_view')?>"><?php lang::_e('Images to show')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('image_view'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('image_view'). '"',
            'value' => $this->data['image_view'],
            'options' => $options,
        ));
    ?><br />
    <?php $options = array('0' => lang::_('No'), '1' => lang::_('Yes'));?>
    <label for="<?php echo $this->widget->get_field_id('show_title')?>"><?php lang::_e('Show Product Title')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('show_title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('show_title'). '"',
            'value' => $this->data['show_title'],
            'options' => $options,
        ));
    ?><br />
    <?php $options = array('0' => lang::_('No'), '1' => lang::_('Yes'));?>
    <label for="<?php echo $this->widget->get_field_id('show_description')?>"><?php lang::_e('Show Description')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('show_description'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('show_description'). '"',
            'value' => $this->data['show_description'],
            'options' => $options,
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
            'value' => $this->data['show_add_to_cart'],
            'options' => $options,
        ));
    ?><br />
    <?php $options = array('0' => lang::_('No'), '1' => lang::_('Yes'));?>
    <label for="<?php echo $this->widget->get_field_id('show_qty')?>"><?php lang::_e('Show QTY input field')?>:</label>
    <?php
        echo html::selectbox($this->widget->get_field_name('show_qty'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('show_qty'). '"',
            'value' => $this->data['show_qty'],
            'options' => $options,
        ));
    ?><br />
</p>