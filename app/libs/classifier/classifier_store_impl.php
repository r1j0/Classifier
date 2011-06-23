<?php

class ClassifierStoreImpl implements ClassifierStore {
	
	
	public function get($tokens) {
		$objects = array();
		
		foreach($tokens as $type) {
			foreach($type as $value) {
				 $Object = new ClassifierObjectsImpl();
				 $Object->setType('word');
				 $Object->setValue($value);
				 $Object->setHamCount(rand(0, 1000));
				 $Object->setSpamCount(rand(0, 1000));
				 $Object->setSpamicity(rand(1, 1000) / 1000);
				 
				 $objects[] = $Object;
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
	
}