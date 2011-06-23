<?php

class ClassifierTokenizerImpl implements ClassifierTokenizer {


	public function tokenize(ClassifierDocument $Document) {
		$tokens = array();
		
		$plainText = strtolower(strip_tags($Document->getText()));
		$plainText = preg_match_all('[\w+]', $plainText, $matches);
		$array = array_filter($matches[0], 'trim');
		$array = array_filter($matches[0], 'strlen');
		$tokens['text'] = $array;
		
		return $tokens;
	}

}