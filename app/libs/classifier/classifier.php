<?php

interface Classifier {


	public function setStore(ClassifierStore $Store);


	public function setTokenizer(ClassifierTokenizer $Tokenizer);


	public function check(ClassifierDocument $Document);


	public function learn(ClassifierDocument $Document, $category);


	public function falsePositive(ClassifierDocument $Document);


	public function isHam();


	public function isSpam();
	
	
	public function getRating();

}