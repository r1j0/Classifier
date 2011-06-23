<?php
App::import('Lib', array('classifier/Classifier', 'classifier/ClassifierTokenizer', 'classifier/ClassifierStore', 'classifier/ClassifierDocument', 'classifier/ClassifierObjects'));
App::import('Lib', array('classifier/ClassifierTokenizerImpl', 'classifier/ClassifierStoreImpl', 'classifier/ClassifierDocumentImpl', 'classifier/ClassifierObjectsImpl'));

class ClassifierImpl implements Classifier {

	const HAM = 0;
	const SPAM = 1;
	const SPAM_THRESHOLD = 0.55;
	const INITIAL_THRESHOLD = 0.4;

	private $_Store;
	private $_Tokenizer;
	private $_rating = 0;
	private $_spamThreshold;


	public function __construct(ClassifierTokenizer $Tokinzer = null, ClassifierStore $Store = null) {
		$this->_Tokenizer = $Tokinzer ? $Tokenizer : new ClassifierTokenizerImpl();
		$this->_Store = $Store ? $Store : new ClassifierStoreImpl();
		$this->_spamThreshold = self::SPAM_THRESHOLD;
	}


	public function setStore(ClassifierStore $Store) {
		$this->_Store = $Store;
	}


	public function setTokenizer(ClassifierTokenizer $Tokenizer) {
		$this->_Tokenizer = $Tokenizer;
	}


	public function check(ClassifierDocument $Document) {
		$tokens = $this->_Tokenizer->tokenize($Document);
		$Objects = $this->_Store->get($tokens);

		$hamTotal = $this->_Store->hamTotal();
		$spamTotal = $this->_Store->spamTotal();
		$multiplierResult = 1;
		$additionResult = 1;
		$i = 0;

		foreach ($Objects as $ClassifierObjects) {
			if ($i == 0) {
				$multiplierResult = $ClassifierObjects->getSpamicity();
				$additionResult = bcsub(1, $ClassifierObjects->getSpamicity(), 10);
				$i++;
				continue;
			}
				
				
			$multiplierResult = bcmul($multiplierResult, $ClassifierObjects->getSpamicity(), 10);
			$additionResult = bcmul($additionResult, (1 - $ClassifierObjects->getSpamicity()), 10);
		}

		$denominator = bcadd($multiplierResult, $additionResult, 10);
		
		if ($multiplierResult == 0 || $denominator == 0) {
			$this->_rating = 0.0;
		} else {
			$this->_rating = bcdiv($multiplierResult, $denominator, 3);
		}
	}


	public function learn(ClassifierDocument $Document, $category) {

	}


	public function falsePositive(ClassifierDocument $Document) {

	}


	public function isHam() {
		return $this->_rating < $this->_spamThreshold;
	}


	public function isSpam() {
		return $this->_rating >= $this->_spamThreshold;
	}


	public function getRating() {
		return $this->_rating;
	}

}