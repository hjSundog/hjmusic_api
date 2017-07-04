<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 　Music
 *　获取Music单个及列表，以及单个Music的上传，删除，和修改
 */

require APPPATH.'/libraries/REST_Controller.php';
class Music extends REST_Controller
{
    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:*');
    }

    function test_get(){
    }

    /**
     * 查看音乐信息
     * @param mixed $id 音乐id
     */
    function index_get($id = null){

        //需要重构的数据
        $prisoner = array('singer_id','composer_id','lyricist_id','album_id','singer_name','composer_name','lyricist_name','album_name');

        //如果传入参数，则返回该id的music信息
        if (isset($id)) {
            //判断id是否为num类型
            $this->lawyer($id);

            //获取指定id的歌曲信息
            $this->aim_music($id,$prisoner);

        }
        //如果没有传入参数，则返回music列表
        else{
            //@@@待加入  分页功能

            $music = $this->db->query("
            SELECT 
            music.*,
            a.name AS singer_name,
            b.name AS composer_name,
            c.name AS lyricist_name
            FROM music
            INNER JOIN musician AS a ON music.singer_id = a.id
            INNER JOIN musician AS b ON music.composer_id = b.id
            INNER JOIN musician AS c ON music.lyricist_id = c.id;");

            //先判断music表中是否存在数据
            if(!$music->num_rows()){
                $this->response(array('error'=>'there is no music now'),404);
            }
            else {
                $info = $music->result_array();
                foreach ($info as $key => $value) {
                    $info[$key]['singer'] = array('id'=>$info[$key]['singer_id'],'name'=>$info[$key]['singer_name']);
                    $info[$key]['composer'] = array('id'=>$info[$key]['composer_id'],'name'=>$info[$key]['composer_name']);
                    $info[$key]['lyricist'] = array('id'=>$info[$key]['lyricist_id'],'name'=>$info[$key]['lyricist_name']);
//                    $info[$key]['album'] = array('id'=>$info[$key]['album_id'],'name'=>$info[$key]['album_name']);
                    $this->unset_key($info[$key], $prisoner);
                }
                $this->response($info);
            }

        }
    }

    /**
     * 上传音乐文件
     */
    function index_post(){
        //权限验证
        $this->verify_auth();

        //加载配置文件
        $this->config->load('upload');

        //将文件重命名为加密的当前时间戳
        $token = hash_hmac('sha256',time(),'hjmusic_key');

        //配置上传参数
        $config['upload_path']      = $this->config->item('upload_path');
        $config['allowed_types']    = $this->config->item('allowed_types');
        $config['max_size']         = $this->config->item('max_size');
        $config['file_name']        = $token;

        //将文件上传到临时文件夹 uploads/temp
        $this->load->library('upload',$config);
        if (!$this->upload->do_upload('music_file')){
            //上传失败，返回一个被<p>标签包裹的错误信息
            $this->response(array('error' => $this->upload->display_errors()));
        }
        else{
            //文件上传成功返回文件令牌
            $this->response(array('token'=>$token));
        }
    }

    /**
     * 上传音乐信息
     */
    function upinfo_post(){

    }

    /**
     * 删除音乐
     * @param mixed $id 音乐id
     * @access administrator
     */
    function index_delete($id){
//        判断id是否为num类型
        $this->lawyer($id);

        //验证管理员权限
        $this->verify_auth();

        //判断音乐是否存在
        if (!$this->db->query('SELECT * FROM music WHERE id = '.$id)->num_rows())
            $this->response(array('error'=>'the music does\'t exist'),404);

        //删除该歌曲，如果数据库操作出错则返回500错误
        $this->db->query("DELETE FROM music WHERE id = {$id}") or $this->response(array('error'=>'fail to delete'),500);

        //返回成功信息
        $this->response(null,204);
    }

    /**
     * 修改音乐
     * @param mixed $id 音乐id
     * @access administrator
     */
    function index_put($id){
        //判断id是否为num类型
        $this->lawyer($id);

        //验证管理员权限
        $this->verify_auth();
        $data = $this->_put_args;

        //判断该音乐是否存在
        if (!$this->db->query('SELECT * FROM music WHERE id = '.$id)->num_rows())
            $this->response(array('error'=>'the music does\'t exist'),404);

        //将数据更新到数据库
        try {
            foreach ($data as $key => $value) {
                switch ($key) {
                    //如果键名属于musician，则需要先判断该艺术家是否在存在与数据表之中
                    case 'singer':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET singer_id = (SELECT id FROM musician WHERE name =$value) WHERE id = {$id}");
                        break;
                    case 'composer':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET composer_id = (SELECT id FROM musician WHERE name =$value) WHERE id = {$id}");
                        break;
                    case 'lyricist':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET lyricist_id = (SELECT id FROM musician WHERE name =$value) WHERE id = {$id}");
                        break;
                    case 'album':
                        $this->db->query("UPDATE music SET album_id = (SELECT id FROM album WHERE name =$value) WHERE id = {$id}");
                        break;
                    default:
                        if ($value)
                            $this->db->query("UPDATE music SET {$key} = {$value} WHERE id = {$id}");
                        break;
                }
            }
        }catch (Exception $exception){
            $this->response(array('error'=>'fail to update'),500);
        }

        //返回歌曲信息
        $this->aim_music($id,array('singer_id','composer_id','lyricist_id','album_id','singer_name','composer_name','lyricist_name','album_name'));

    }



    /***********************
     * ↓↓↓工具方法↓↓↓*
     ***********************/


    /**
     * 删除结果数组中的键值对
     * @param array $target 需要修改的数组
     * @param array $prisoner 要删除的key值
     */
    private function unset_key(array &$target,array $prisoner){
        foreach ($prisoner as $value){
            unset($target[$value]);
        }
    }


    /**
     * 验证管理员权限
     */
    private function verify_auth(){
        $this->load->library('jwt');
        $headers = $this->input->request_headers();
        try {
            $token = $this->jwt->decode($headers['Access-Token'], 'hjmusic_key');
        }catch (Exception $exception){
            $this->response(array('error'=>$exception->getMessage()),403);
            return;
        }
        $auth = $token->auth;
        if ($auth != 'administrator') $this->response(array('error'=>'Unauthorized'),403);
    }


    /**
     * 判断艺术家是否存在与数据中
     * @param $musician string 艺术家的名字
     */
    private function verify_musician($musician){
        $res = $this->db->query("SELECT * FROM musician WHERE name = {$musician}")->num_rows();
        if ($res == 0)
            $this->response(array('error'=>'this musician is not exist'),404);
    }


    /**
     * 返回指定id的歌曲信息
     * @param $id mixed 音乐id
     * @param $prisoner array 需要重构的数据（数组key值）
     */
    private function aim_music($id,$prisoner){
        //获取该音乐的信息
        $music = $this->db->query("SELECT 
        music.*,
        a.name AS singer_name,
        b.name AS composer_name,
        c.name AS lyricist_name
        FROM music
        INNER JOIN musician AS a ON music.singer_id = a.id
        INNER JOIN musician AS b ON music.composer_id = b.id
        INNER JOIN musician AS c ON music.lyricist_id = c.id
        WHERE music.id = {$id};");

        //判断音乐是否存在
        if (! $music->num_rows()) {
            $this->response(array(['error' => 'this music is not find']), 404);
        }
        else{
            $info = $music->result_array();

            $info[0]['singer'] = array('id'=>$info[0]['singer_id'],'name'=>$info[0]['singer_name']);
            $info[0]['composer'] = array('id'=>$info[0]['composer_id'],'name'=>$info[0]['composer_name']);
            $info[0]['lyricist'] = array('id'=>$info[0]['lyricist_id'],'name'=>$info[0]['lyricist_name']);
//            $info[0]['album'] = array('id'=>$info[0]['album_id'],'name'=>$info[0]['album_name']);
            $this->unset_key($info[0], $prisoner);
            $this->response($info);
        }
    }


    /**
     * 数据合法性判断，判断是否为num类型
     * @param mixed $id 通过url传入的值
     */
    private function lawyer($id){
        is_numeric($id) or $this->response(array('error'=>'the music id must be number'),400);
    }


}