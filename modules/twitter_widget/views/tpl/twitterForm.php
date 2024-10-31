<label for="<?php echo $this->widget->get_field_id('title')?>"><?php lang::_e('Twitter Title')?>:</label>
<?php 
    echo html::text($this->widget->get_field_name('title'), array(
        'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
        'value' => $this->data['title']));
?><br />
<label for="<?php echo $this->widget->get_field_id('username')?>"><?php lang::_e('Twitter Username')?>:</label>
<?php 
    echo html::text($this->widget->get_field_name('username'), array(
        'attrs' => 'id="'. $this->widget->get_field_id('username'). '"', 
        'value' => $this->data['username']));
?><br />
<label for="<?php echo $this->widget->get_field_id('count')?>"><?php lang::_e('Tweets Count')?>:</label>
<?php 
    echo html::text($this->widget->get_field_name('count'), array(
        'attrs' => 'id="'. $this->widget->get_field_id('count'). '"', 
        'value' => $this->data['count']));
?><br />