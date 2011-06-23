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
				 $objects[] = $this->_createObject($entry['Classifier']['type'], $entry['Classifier']['value'], $entry['Classifier']['ham_count'], $entry['Classifier']['spam_count'], $entry['Classifier']['spamicity']);
			}
			
			$foundValues = Set::extract($entries, '{n}.Classifier.value');
			$notFoundValues = array_diff($values, $foundValues);
			
			foreach ($notFoundValues as $value) {
				$objects[] = $this->_createObject($type, $value, 0, 0, ClassifierImpl::INITIAL_THRESHOLD);
			}
		}
		
		return $objects;
	}
	
	
	public function update($classifierObjects) {
		
	}
	
	
	public function hamTotal() {
		return 2000;
	}
	
	
	public function spamTotal() {
		return 2000;
	}
	
	
	private function _createObject($type, $value, $hamCount, $spamCount, $spamicity) {
		$Object = new ClassifierObjectsImpl();
		$Object->setType($type);
		$Object->setValue($value);
		$Object->setHamCount($hamCount);
		$Object->setSpamCount($spamCount);
		$Object->setSpamicity($spamicity);
		
		return $Object;
	}
	
}