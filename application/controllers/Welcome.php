<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->library('email');
		$config = array();
		$config['useragent'] = "CodeIgniter";
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.googlemail.com';
		$config['smtp_port'] = 465;
		$config['smtp_user'] = 'hjsundog@gmail.com';
		$config['smtp_pass'] = 'fuckfuck';
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['newline']  = "\r\n";
        $config['wordwrap'] = TRUE;
		$this->email->initialize($config);
		$this->email->from('hjsundog@gmail.com'); 
        $this->email->to('443474713@qq.com'); 
        $this->email->subject("test from hjsundog"); 
        $this->email->message('this is just a test'); 
        $this->email->send(); 
         
        echo $this->email->print_debugger(); 
	}
}
