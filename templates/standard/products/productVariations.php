<div class="toeVariationSelect">
<h2>Select variation</h2>
<select id="selct">
<option id="0">Select variation</option>
<?php foreach ($this->variations as $var) : ?>
<option id="<?=$var[0]['post_id'];?>"><?=$var[0]['var_name'];?></option>
<?php endforeach;?>
</select>
</div>
<div class="toeVariationProduct"></div>