<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class T extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
    	echo "<pre>";
    	print_r($this->config);
    	echo '</pre>';
    	echo 'this is test Controller';

    }

    public function sendemail(){
    	$this->load->library('email');
    	$this->config->load('email',TRUE);
    	$conf=$this->config->item('email');
		$this->email->initialize($conf);

		$this->email->from('qing_cang@126.com', 'tkorays');
		$this->email->to('767838908@qq.com');
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();

		echo $this->email->print_debugger();
    }
}
        