<?php

interface Classifier {


	public function setStore(ClassifierStore $Store);


	public function setTokenizer(ClassifierTokenizer $Tokenizer);
	
	
	public function setSpamThreshold($threshold);


	public function check(ClassifierDocument $Document);


	public function learn(ClassifierDocument $Document, $hamTotal, $spamTotal, $category);


	public function falsePositive(ClassifierDocument $Document, $hamTotal, $spamTotal);


	public function isHam();


	public function isSpam();
	
	
	public function getRating();

}