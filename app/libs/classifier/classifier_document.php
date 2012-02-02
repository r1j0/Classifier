<?php
interface ClassifierDocument {


	public static function getInstance();


	public function getText();


	public function setText($text);


	public function getAuthor();


	public function setAuthor($author);


	public function getUrl();


	public function setUrl($url);
}
?>