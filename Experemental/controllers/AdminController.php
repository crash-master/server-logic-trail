<?php

class AdminController{
	public function admin_panel(){
		return current_signin();
	}
}