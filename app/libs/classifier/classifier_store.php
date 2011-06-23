<?php

interface ClassifierStore {


	public function get($tokens);


	public function update(ClassifierObjects $Objects);


	public function hamTotal();


	public function spamTotal();

}