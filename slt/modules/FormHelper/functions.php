<?php

function form_to($controller_name, $enctype = false){
	echo module('FormHelper') -> get_form_to($controller_name, $enctype);
}

function trust_form(){
	return module('FormHelper') -> trust_form();
}