<?php

class ClassifierDocumentImpl implements ClassifierDocument {

	private $_text;
	private $_author;
	private $_url;


	public function getText() {
		return $this->_text;
	}


	public function setText($text) {
		$this->_text = $text;
	}


	public function getAuthor() {
		return $this->_author;
	}


	public function setAuthor($author) {
		$this->_author = $author;
	}


	public function getUrl() {
		return $this->_url;
	}


	public function setUrl($url) {
		$this->_url = $url;
	}
}