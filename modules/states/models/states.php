<?php
class statesModel extends model {
	public function save($d = array()) {
		$res = false;
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		unset($d['id']);
		if($id)
			$res = frame::_()->getTable('states')->update($d, array('id' => $id));
		else
			$res = frame::_()->getTable('states')->insert($d);
		if(!$res)
			$this->pushError( frame::_()->getTable('states')->getErrors() );
		elseif(!$id)
			$id = $res;
		if($res)
			return $id;
		return $res;
	}
	public function remove($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			if(frame::_()->getTable('states')->delete($id)) {
				return true;
			} else
				$this->pushError( frame::_()->getTable('states')->getErrors() );
		} else
			$this->pushError (lang::_('Invalid ID'));
		return false;
	}
	public function restore($d = array()) {
		// Delete all states at first
		frame::_()->getTable('states')->delete();
		// Insert default states from installer
		installer::insertStates();
		return true;
	}
}
?>
