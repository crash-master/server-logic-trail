<?php

use Kernel\Events;

Events::on('load_kernel', function(){
	echo "LOAD KERNEL<br>";
});

Events::on('app_finished', function(){
	die("APP FINISHED");
});

Events::on('register_component', function($component){
	echo $component['name'] . '<br>';
});