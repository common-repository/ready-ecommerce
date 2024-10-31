<?php
class countriesModel extends model {
	public function save($d = array()) {
		$res = false;
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		unset($d['id']);
		if($id)
			$res = frame::_()->getTable('countries')->update($d, array('id' => $id));
		else
			$res = frame::_()->getTable('countries')->insert($d);
		if(!$res)
			$this->pushError( frame::_()->getTable('countries')->getErrors() );
		elseif(!$id)
			$id = $res;
		if($res)
			return $id;
		return $res;
	}
	public function remove($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			if(frame::_()->getTable('countries')->delete($id)) {
				return true;
			} else
				$this->pushError( frame::_()->getTable('countries')->getErrors() );
		} else
			$this->pushError (lang::_('Invalid ID'));
		return false;
	}
	public function restore($d = array()) {
		// Delete all countries at first
		frame::_()->getTable('countries')->delete();
		// Insert default countries from installer
		installer::insertCountries();
		return true;
	}
}
?>
