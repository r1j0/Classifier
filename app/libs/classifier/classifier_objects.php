<?php

interface ClassifierObjects {


	public function getInstance();
	
	
	public function getId();
	
	
	public function setId($id);


	public function getType();


	public function setType($type);


	public function getValue();


	public function setValue($value);


	public function getHamCount();


	public function setHamCount($hamCount);


	public function getSpamCount();


	public function setSpamCount($spamCount);


	public function getSpamicity();


	public function setSpamicity($spamicity);

}