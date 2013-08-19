<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test{

	public function sayHello(){
		return '1+2=3';
	}

	public function nima($y,$m,$d){
		return "Year:$y Month:$m Day $d";
	}

	public function getMessage()
	{
		return 'error';
	}
}