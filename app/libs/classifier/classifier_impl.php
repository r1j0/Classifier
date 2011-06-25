<?php
App::import('Lib', array('classifier/Classifier', 'classifier/ClassifierTokenizer', 'classifier/ClassifierStore', 'classifier/ClassifierDocument', 'classifier/ClassifierObjects'));
App::import('Lib', array('classifier/ClassifierTokenizerImpl', 'classifier/ClassifierStoreImpl', 'classifier/ClassifierDocumentImpl', 'classifier/ClassifierObjectsImpl'));

class ClassifierImpl implements Classifier {

	const HAM = 0;
	const SPAM = 1;
	const SPAM_THRESHOLD = 0.55;
	const MINIMUM_COUNT = 5;
	const INITIAL_SPAMICITY_THRESHOLD = 0.4;
	const INITIAL_HAM_SPAMICITY_THRESHOLD = 0.1;
	const INITIAL_SPAM_SPAMICITY_THRESHOLD = 0.99;

	private $_Store;
	private $_Tokenizer;
	private $_Objects;
	private $_rating = 0;
	private $_spamThreshold;


	/**
	 * Default Classifier implementation.
	 *
	 * @param ClassifierTokenizer $Tokenizer
	 * @param ClassifierStore $Store
	 * @param ClassifierObjects $Objects
	 */
	public function __construct(ClassifierTokenizer $Tokenizer = null, ClassifierStore $Store = null, ClassifierObjects $Objects = null) {
		$this->_Tokenizer = $Tokenizer ? $Tokenizer : new ClassifierTokenizerImpl();
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


	public function learn(ClassifierDocument $Document, $hamTotal, $spamTotal, $category) {
		$tokens = $this->_Tokenizer->tokenize($Document);
		$Objects = $this->_Store->get($tokens);

		$hamTotal = ($category == self::HAM) ? $hamTotal + 1: $hamTotal;
		$spamTotal = ($category == self::SPAM) ? $spamTotal + 1: $spamTotal;

		$values = array();

		foreach ($Objects as $ClassifierObject) {
			$type = $ClassifierObject->getType();
			$value = $ClassifierObject->getValue();

			if (isset($values[$type][$value])) {
				$values[$type][$value]['ham_count'] += ($category == self::HAM) ? 1 : 0;
				$values[$type][$value]['spam_count'] += ($category == self::SPAM) ? 1 : 0;
				$values[$type][$value]['spamicity'] = $this->_calculateSpamicity($values[$type][$value]['ham_count'], $values[$type][$value]['spam_count'], $hamTotal, $spamTotal);
				continue;
			}

			$id = $ClassifierObject->getId();
			$hamCount = ($category == self::HAM) ? $ClassifierObject->getHamCount() + 1 : $ClassifierObject->getHamCount();
			$spamCount = ($category == self::SPAM) ? $ClassifierObject->getSpamCount() + 1 : $ClassifierObject->getSpamCount();
			$spamicity = $this->_calculateSpamicity($hamCount, $spamCount, $hamTotal, $spamTotal);

			$values[$type][$value] = array('id' => $id, 'type' => $type, 'value' => $value, 'ham_count' => $hamCount, 'spam_count' => $spamCount, 'spamicity' => $spamicity);
		}

		$updatedObjects = $this->_createObjects($values);

		return $this->_Store->update($updatedObjects);
	}


	public function falsePositive(ClassifierDocument $Document, $hamTotal, $spamTotal) {

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


	private function _createObjects($filteredValues) {
		$updatedObjects = array();

		foreach($filteredValues as $values) {
			foreach($values as $value) {
				$updatedObjects[] = $this->_createObject($value['id'], $value['type'], $value['value'], $value['ham_count'], $value['spam_count'], $value['spamicity']);
			}
		}

		return $updatedObjects;
	}


	private function _calculateSpamicity($hamCount, $spamCount, $hamTotal, $spamTotal) {
		$spamicity = self::INITIAL_SPAMICITY_THRESHOLD;

		if ($hamCount == 0 && $spamCount == 0) {
			$spamicity = self::INITIAL_SPAMICITY_THRESHOLD;
		} else if ($hamCount < self::MINIMUM_COUNT && $spamCount == 0) {
			$spamicity = self::INITIAL_HAM_SPAMICITY_THRESHOLD;
		} else if ($hamCount == 0 && $spamCount < self::MINIMUM_COUNT) {
			$spamicity = self::INITIAL_SPAM_SPAMICITY_THRESHOLD;
		} else if ($spamTotal == 0) {
			$spamicity = $category == self::HAM ? self::INITIAL_HAM_SPAMICITY_THRESHOLD : self::INITIAL_SPAM_SPAMICITY_THRESHOLD;
		} else {
			$hamPropability = bcdiv($hamCount, $hamTotal, 10);
			$spamPropability = bcdiv($spamCount, $spamTotal, 10);
			$spamicity = bcdiv($spamPropability, bcadd($spamPropability, $hamPropability, 10), 3);
		}

		return $spamicity;
	}
}