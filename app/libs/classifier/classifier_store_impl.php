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
				 $objects[] = $this->_createObject($entry['Classifier']['id'], $entry['Classifier']['type'], $entry['Classifier']['value'], $entry['Classifier']['ham_count'], $entry['Classifier']['spam_count'], $entry['Classifier']['spamicity']);
			}
			
			$foundValues = Set::extract($entries, '{n}.Classifier.value');
			$notFoundValues = array_diff($values, $foundValues);
			
			foreach ($notFoundValues as $value) {
				$objects[] = $this->_createObject(null, $type, $value, 0, 0, ClassifierImpl::INITIAL_SPAMICITY_THRESHOLD);
			}
		}
		
		return $objects;
	}
	
	
	public function update($classifierObjects) {
		foreach ($classifierObjects as $Object) {
			$data = array();
			$data['Classifier']['id'] = $Object->getId();
			$data['Classifier']['type'] = $Object->getType();
			$data['Classifier']['value'] = $Object->getValue();
			$data['Classifier']['ham_count'] = $Object->getHamCount();
			$data['Classifier']['spam_count'] = $Object->getSpamCount();
			$data['Classifier']['spamicity'] = $Object->getSpamicity();
			
			$this->_Model->create();
			$this->_Model->save($data, false, array('id', 'type', 'value', 'ham_count', 'spam_count', 'spamicity'));
		}
	}
	
	
	private function _createObject($id, $type, $value, $hamCount, $spamCount, $spamicity) {
		$Objects = $this->_Objects->getInstance();
		$Objects->setId($id);
		$Objects->setType($type);
		$Objects->setValue($value);
		$Objects->setHamCount($hamCount);
		$Objects->setSpamCount($spamCount);
		$Objects->setSpamicity($spamicity);

		return $Objects;
	}
	
}