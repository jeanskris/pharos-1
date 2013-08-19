<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class T extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function s() {
    	$this->load->library('JsonRpcClient');
        $client = new JsonRpcClient();

        $url=site_url('t/server');
		$client->request($url);
		$op=$this->uri->segment(3);
		$p=array($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
		$response=call_user_func_array(array($client,$op),$p);
		echo $response; 
    }

    public function server(){

    	$this->load->library('test');
    	$this->load->library('jsonRPCServer');
    	$t = new test();
    	$server = new jsonRPCServer();
    	$server->handle($t);
    	
    }


}
        