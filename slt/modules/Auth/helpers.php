<?php

function asset_auth_path($path){
	return '/' . auth_path($path);
}

function auth_path($path){
	return \Kernel\Module::pathToModule('Auth') . $path;
}