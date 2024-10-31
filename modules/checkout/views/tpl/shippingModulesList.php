<?php
    $htmlMethod = (count($this->modules) > 1) ? 'radiobutton' : 'hidden';
?>
<?php 
    foreach($this->modules as $m) { 
    $htmlParams = array('value' => $m['id']);
    if($htmlMethod == 'radiobutton' && isset($this->order[$this->modType]) && ($this->order[$this->modType] == $m['id'] || $this->order[$this->modType]['id'] == $m['id']))
        $htmlParams['checked'] = true;
?>
    <label class="ModListButtons">
        <?php echo html::$htmlMethod($this->modType, $htmlParams)?><?php echo $m['label']?><br />
    </label>
<?php }?>
<div class="toeErrorForField toe_<?php echo $this->modType?>"></div>