<?php
class pagesModel extends model {
	public function recreatePages() {
		installer::createPages();
		return true;
	}
}
?>
