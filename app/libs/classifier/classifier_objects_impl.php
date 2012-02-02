<?php
class ClassifierObjectsImpl implements ClassifierObjects {

	private $_id;
	private $_type;
	private $_value;
	private $_hamCount;
	private $_spamCount;
	private $_spamicity;


	public static function getInstance() {
		return new ClassifierObjectsImpl();
	}


	public function getId() {
		return $this->_id;
	}


	public function setId($id) {
		$this->_id = $id;
	}


	public function getType() {
		return $this->_type;
	}


	public function setType($type) {
		$this->_type = $type;
	}


	public function getValue() {
		return $this->_value;
	}


	public function setValue($value) {
		$this->_value = $value;
	}


	public function getHamCount() {
		return $this->_hamCount;
	}


	public function setHamCount($hamCount) {
		$this->_hamCount = $hamCount;
	}


	public function getSpamCount() {
		return $this->_spamCount;
	}


	public function setSpamCount($spamCount) {
		$this->_spamCount = $spamCount;
	}


	public function getSpamicity() {
		return $this->_spamicity;
	}


	public function setSpamicity($spamicity) {
		$this->_spamicity = $spamicity;
	}
}
?>