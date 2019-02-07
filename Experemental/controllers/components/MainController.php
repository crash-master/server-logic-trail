<?php

class MainController{
	public function main_component($counter){
		$counterX2 = $counter * 2;
		return compact('counter', 'counterX2');
	}
}