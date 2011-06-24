<?php

interface ClassifierStore {


	public function get($tokens);


	public function update($classifierObjects);

}