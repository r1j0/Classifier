<?php

class ClassifierTokenizerImpl implements ClassifierTokenizer {


	public function tokenize(ClassifierDocument $Document) {
		$tokens = array();
		
		$plainText = strtolower(strip_tags($Document->getText()));
		$tokens['value'] = explode(" ", $plainText);
		
		return $tokens;
	}

}