<?php
class Email {

    function send($title, $message, $to)
    {
        date_default_timezone_set("PRC");
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'hjsundog@gmail.com',
            'smtp_pass' => 'fuckfuck',
            'mailtype'  => 'html', 
            'charset'   => 'utf-8'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");


        $this->email->from('hjsundog@gmail.com', 'HJsundog');
        $this->email->to($to); 

        $this->email->subject($title);
        $this->email->message($message);  

        $this->email->send();

        echo $this->email->print_debugger();
    }
}