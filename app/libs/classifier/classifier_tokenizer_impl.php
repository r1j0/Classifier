<?php
class ClassifierTokenizerImpl implements ClassifierTokenizer {


	public function tokenize(ClassifierDocument $Document) {
		$tokens = array();

		$plainText = strip_tags($Document->getText());
		$plainText = preg_replace(array('/[,!:."§$%&\/\(\)=?*#]/u', '/\s\s+/u'), ' ', $plainText);
		$array = explode(' ', mb_strtolower($plainText, 'UTF-8'));
		$array = array_filter($array, 'trim');
		$tokens['text'] = array_values($array);

		return $tokens;
	}
}
?>