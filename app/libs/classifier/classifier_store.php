<?php

interface ClassifierStore {


	public function get($tokens);


	public function update($classifierObjects);


	public function hamTotal();


	public function spamTotal();

}