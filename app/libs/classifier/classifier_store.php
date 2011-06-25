<?php

interface ClassifierStore {


	/**
	 * Accepts an array of tokens and returns an array of ClassifierObjects.
	 *
	 * It is important to return all tokens. Otherwise unknown tokens can
	 * not contribute in a meaningfull way.
	 *
	 * @param array $tokens
	 * @return ClassifierObjects
	 */
	public function get($tokens);


	/**
	 * The array of ClassifierObjects should only contain a unique list of
	 * ClassifierObjects.
	 *
	 * @param array ClassifierObjects $classifierObjects
	 */
	public function update($classifierObjects);

}