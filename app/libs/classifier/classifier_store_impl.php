<?php

class ClassifierStoreImpl implements ClassifierStore {
	
	private $_Model;
	
	
	public function __construct() {
		$this->_Model = ClassRegistry::init('Classifier');
	}
	
	
	public function get($tokens) {
		$objects = array();
		
		foreach($tokens as $type => $values) {
			$entries = $this->_Model->find('all', array(
				'conditions' => array(
					'type' => $type,
					'value' => $values
				),
			));
			
			foreach ($entries as $entry) {
				 $objects[] = $this->_getObject($entry['Classifier']['type'], $entry['Classifier']['value'], $entry['Classifier']['ham_count'], $entry['Classifier']['spam_count'], $entry['Classifier']['spamicity']);
			}
			
			$foundValues = Set::extract($entries, '{n}.Classifier.value');
			$notFoundValues = array_diff($values, $foundValues);
			
			foreach ($notFoundValues as $value) {
				$objects[] = $this->_getObject($type, $value, 0, 0, ClassifierImpl::INITIAL_THRESHOLD);
			}
		}
		
		return $objects;
	}
	
	
	public function update(ClassifierObjects $Objects) {
		
	}
	
	
	public function hamTotal() {
		return rand(1000, 5000);
	}
	
	
	public function spamTotal() {
		return rand(1000, 5000);
	}
	
	
	private function _getObject($type, $value, $hamCount, $spamCount, $spamicity) {
		$Object = new ClassifierObjectsImpl();
		$Object->setType($type);
		$Object->setValue($value);
		$Object->setHamCount($hamCount);
		$Object->setSpamCount($spamCount);
		$Object->setSpamicity($spamicity);
		
		return $Object;
	}
	
}