<?php

class ClassifierTokenizerImpl implements ClassifierTokenizer {


	public function tokenize(ClassifierDocument $Document) {
		$tokens = array();
		
		$plainText = strtolower(strip_tags($Document->getText()));
		$tokens['text'] = explode(" ", $plainText);
		
		return $tokens;
	}

}