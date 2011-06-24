<?php
App::import('Lib', array('classifier/Classifier', 'classifier/ClassifierTokenizer', 'classifier/ClassifierStore', 'classifier/ClassifierDocument', 'classifier/ClassifierObjects'));
App::import('Lib', array('classifier/ClassifierTokenizerImpl', 'classifier/ClassifierStoreImpl', 'classifier/ClassifierDocumentImpl', 'classifier/ClassifierObjectsImpl'));

class ClassifierImpl implements Classifier {

	const HAM = 0;
	const SPAM = 1;
	const SPAM_THRESHOLD = 0.55;
	const INITIAL_THRESHOLD = 0.4;
	const INITIAL_HAM_THRESHOLD = 0.1;
	const INITIAL_SPAM_THRESHOLD = 0.99;

	private $_Store;
	private $_Tokenizer;
	private $_Objects;
	private $_rating = 0;
	private $_spamThreshold;


	public function __construct(ClassifierTokenizer $Tokinzer = null, ClassifierStore $Store = null, ClassifierObjects $Objects = null) {
		$this->_Tokenizer = $Tokinzer ? $Tokenizer : new ClassifierTokenizerImpl();
		$this->_Objects = $Objects ? $Objects : new ClassifierObjectsImpl();
		$this->_Store = $Store ? $Store : new ClassifierStoreImpl($this->_Objects);
		$this->_spamThreshold = self::SPAM_THRESHOLD;
	}


	public function setStore(ClassifierStore $Store) {
		$this->_Store = $Store;
	}


	public function setTokenizer(ClassifierTokenizer $Tokenizer) {
		$this->_Tokenizer = $Tokenizer;
	}


	public function setSpamThreshold($threshold) {
		$this->_spamThreshold = $threshold;
	}


	public function check(ClassifierDocument $Document) {
		$tokens = $this->_Tokenizer->tokenize($Document);
		$Objects = $this->_Store->get($tokens);

		$multiplierResult = 1;
		$additionResult = 1;
		$i = 0;

		foreach ($Objects as $ClassifierObject) {
			if ($i == 0) {
				$multiplierResult = $ClassifierObject->getSpamicity();
				$additionResult = bcsub(1, $ClassifierObject->getSpamicity(), 10);
				$i++;
				continue;
			}


			$multiplierResult = bcmul($multiplierResult, $ClassifierObject->getSpamicity(), 10);
			$additionResult = bcmul($additionResult, (1 - $ClassifierObject->getSpamicity()), 10);
		}

		$denominator = bcadd($multiplierResult, $additionResult, 10);

		if ($multiplierResult == 0 || $denominator == 0) {
			$this->_rating = 0.0;
		} else {
			$this->_rating = bcdiv($multiplierResult, $denominator, 3);
		}
	}


	public function learn(ClassifierDocument $Document, $category) {
		$tokens = $this->_Tokenizer->tokenize($Document);
		$Objects = $this->_Store->get($tokens);

		$hamTotal = ($category == self::HAM) ? $this->_Store->hamTotal() + 1: $this->_Store->hamTotal();
		$spamTotal = ($category == self::SPAM) ? $this->_Store->spamTotal() + 1: $this->_Store->spamTotal();
		
		$updatedObjects = array();

		foreach ($Objects as $ClassifierObject) {
			$type = $ClassifierObject->getType();
			$value = $ClassifierObject->getValue();
			$hamCount = ($category == self::HAM) ? $ClassifierObject->getHamCount() + 1 : $ClassifierObject->getHamCount();
			$spamCount = ($category == self::SPAM) ? $ClassifierObject->getSpamCount() + 1 : $ClassifierObject->getSpamCount();
				
			if ($ClassifierObject->getHamCount() == 0 && $ClassifierObject->getSpamCount() == 0) {
				$spamicity = self::INITIAL_THRESHOLD;
			} else if ($ClassifierObject->getHamCount() > 0 && $ClassifierObject->getSpamCount() == 0) {
				$spamicity = self::INITIAL_HAM_THRESHOLD;
			} else if ($ClassifierObject->getHamCount() == 0 && $ClassifierObject->getSpamCount() > 0) {
				$spamicity = self::INITIAL_SPAM_THRESHOLD;
			} else {
				$hamPropability = bcdiv($hamCount, $hamTotal, 10);
				$spamPropability = bcdiv($spamCount, $spamTotal, 10);
				$spamicity = bcdiv($spamPropability, bcadd($spamPropability, $hamPropability, 10), 3);
			}
				
			$updatedObjects[] = $this->_createObject($type, $value, $hamCount, $spamCount, $spamicity);
		}

		return $this->_Store->update($updatedObjects);
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