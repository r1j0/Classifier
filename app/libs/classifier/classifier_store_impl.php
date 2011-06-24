<?php

class ClassifierStoreImpl implements ClassifierStore {
	
	private $_Objects;
	private $_Model;
	
	public function __construct(ClassifierObjects $Objects) {
		$this->_Objects = $Objects ? $Objects : new ClassifierObjectsImpl();
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
		$Objects = $this->_Objects->getInstance();
		$Objects->setType($type);
		$Objects->setValue($value);
		$Objects->setHamCount($hamCount);
		$Objects->setSpamCount($spamCount);
		$Objects->setSpamicity($spamicity);

		return $Objects;
	}
	
}