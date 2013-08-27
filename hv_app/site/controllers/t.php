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

    public function medoo(){
        $this->load->library('medoo');
        $this->load->config('medoom',TRUE);
        $medoo_m = new medoo();
        $medoo_conf=$this->config->item('medoom');
        $medoo_m->config($medoo_conf);
        //print_r($medoo_conf);
        $r=$medoo_m->select('ph_user','username',array('userid'=>'100000'));
        header('Content-Type:text/html;charset=utf8');
        print_r($r);
        
    }

    public function cap(){
        $this->load->library('simplecaptcha');

        $captcha = new SimpleCaptcha();
        //$captcha->wordsFile =  'words/en.dic';
        $captcha->session_var = 'secretword';
        $captcha->imageFormat = 'png';
        $captcha->lineWidth = 3;
        $captcha->scale = 3; $captcha->blur = true;
        $captcha->resourcesPath = "./assets/resources";
        //echo $captcha->resourcesPath.$captcha->wordsFile;
        $captcha->CreateImage();
    }
}
        