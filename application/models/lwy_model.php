<?php 

class Lwy_model extends CI_Model{

	public  function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{
		echo "model is load";
	}

	public function get_userinfo($email)
	{
		$q = "select * from user where email = '{$email}'";
		if($this->db->query($q))
		{
			$query = $this->db->query($q);
			return $query->row_array();
		}

	}
	public function save($email,$username,$password)
	{
		$q = "insert into user (`username`,`email`,`pwd`) values('{$username}','{$email}','{$password}')";
		$query = $this->db->query($q);
	}
}

























 ?>