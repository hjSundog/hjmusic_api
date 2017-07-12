<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
class Upload extends REST_Controller
{

    /**
     * 上传音乐文件
     */
    function music_post(){
        //验证是否传来文件
        if (empty($_FILES))
            $this->response(array('error'=>'the file can\'t be empty'),400);

        //权限验证
        $this->verify_auth();

        //加载配置文件
        $this->config->load('Upload.php');

        //将文件重命名为加密的当前时间戳
        $token = hash_hmac('sha256',time(),'hjmusic_key');

        //配置上传参数
        $file_name = $this->config->item('file_name');
        $config['upload_path']      = $this->config->item('upload_path');
        $config['allowed_types']    = $this->config->item('allowed_types');
        $config['max_size']         = $this->config->item('max_size');
        $config['file_name']        = $token;

        //将文件上传到临时文件夹 uploads/temp
        $this->load->library('Upload.php',$config);
        if (!$this->upload->do_upload($file_name)){
            //上传失败，返回一个被<p>标签包裹的错误信息
            $this->response(array('error' => $this->upload->display_errors()));
        }
        else{
            //文件上传成功返回带后缀的文件令牌
            $this->response(array('token'=>$this->upload->data('file_name')),200);
        }
    }


    /***********************
     * ↓↓↓工具方法↓↓↓*
     ***********************/


    /**
     * 验证管理员权限
     */
    private function verify_auth(){
        $this->load->library('jwt');
        $headers = $this->input->request_headers();

        if (empty($headers['Access-Token']))
            $this->response(array('error'=>'Access-Token is missing'));

        try {
            $token = $this->jwt->decode($headers['Access-Token'], 'hjmusic_key');
        }catch (Exception $exception){
            $this->response(array('error'=>'Authorization failed'.$exception->getMessage()),403);
            return;
        }
        $auth = $token->auth;
        if ($auth != 'administrator') $this->response(array('error'=>'Unauthorized'),403);
    }
}