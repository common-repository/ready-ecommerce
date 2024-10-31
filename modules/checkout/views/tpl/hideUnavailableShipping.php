<?php /*This will just go right after shipping modules selection to hide unavailable shipping methods for selected in order country/state (if such exists)*/ ?>
<script type="text/javascript">
// <!--
jQuery(function(){
	toeHideUnavailableShipping(<?php echo utils::jsonEncode($this->unavailableShipingIds)?>);
});
// -->
</script>