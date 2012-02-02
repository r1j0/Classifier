<?php
interface Classifier {


	/**
	 * Allows to set a different ClassifierStore implementation.
	 * For example, instead of using a database, a file based
	 * storage service could be implemented and used.
	 *
	 * @param ClassifierStore $Store
	 */
	public function setStore(ClassifierStore $Store);


	/**
	 * Allows to set a different ClassifierTokenizer implementation.
	 * Usefull in case you like to parse data in a different way than
	 * the default implementation.
	 *
	 * @param ClassifierTokenizier $Tokenizer
	 */
	public function setTokenizer(ClassifierTokenizer $Tokenizer);


	/**
	 * Setting a different spam threshold allows to define a more
	 * appropriate setting for your application.
	 *
	 * @param float $threshold
	 */
	public function setSpamThreshold($threshold);


	/**
	 * Calculates a rating from the given ClassifierDocument
	 * based on the learned data.
	 *
	 * @param ClassifierDocument $Document
	 */
	public function check(ClassifierDocument $Document);


	/**
	 * Classifies the given ClassifierDocument by calculating a word specific
	 * spamicity value.
	 *
	 * The variable $hamTotal is the total number of ham documents that will be learned and
	 * variable $spamTotal is the total number of spam documents that will be learned.
	 *
	 * Variable $category is one of the constants Classifier:HAM or Classifier::SPAM which the
	 * to be learned ClassifierDocument will be classified as.
	 *
	 * @param ClassifierDocument $Document
	 * @param int $hamTotal
	 * @param int $spamTotal
	 * @param int $category
	 */
	public function learn(ClassifierDocument $Document, $hamTotal, $spamTotal, $category);


	/**
	 * ClassifierDocument that have been wrongfully classified as spam
	 * will be reclassified as ham.
	 *
	 * @param ClassifierDocument $Document
	 * @param int $hamTotal
	 * @param int $spamTotal
	 * @param int $category
	 */
	public function falsePositive(ClassifierDocument $Document, $hamTotal, $spamTotal);


	/**
	 * Returns true if the check method calculated
	 * a rating that is below the spam threshold.
	 *
	 * @return boolean
	 */
	public function isHam();


	/**
	 * Returns true if the check method calculated
	 * a rating that is equal or above the spam threshold.
	 *
	 * @return boolean
	 */
	public function isSpam();


	/**
	 * Returns the calculated rating value.
	 *
	 * @return float
	 */
	public function getRating();
}
?>