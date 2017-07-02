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
     * @param integer $id 音乐id
     */
    function index_get($id = null){
        $prisoner = array('singer_id','composer_id','lyricist_id','album_id');
        //如果传入参数，则返回该id的music信息
        if (isset($id)) {
            $info = $this->db->query("SELECT * FROM music WHERE id = $id");

            //判断音乐是否存在
            if (! $info->num_rows()) {
                $this->response(array(['error' => 'this music is not find']), 404);
            }
            else{
                $music = $info->result_array();
                $music['singer'] = $this->db->query("SELECT id,name FROM singer WHERE id = $id")->result_array();
                $music['composer'] = $this->db->query("SELECT id,name FROM musician WHERE id = $id")->result_array();
                $music['lyricist'] = $this->db->query("SELECT id,name FROM lyricist WHERE id = $id")->result_array();
                $this->unset_key($music,$prisoner);
                $this->response($music);
            }

        }
        //如果没有传入参数，则返回music列表
        else{
            $music = $this->db->query("SELECT * FROM music")->result_array;

            //先判断music表中是否存在数据
            if(!$music->num_rows()){
                $this->response(array('error'=>'there is no music now'),404);
            }
            else {
                foreach ($music as $key => $value) {
                    $value['singer'] = $this->db->query("SELECT id,name FROM musician WHERE id = {$value['id']}")->result_array();
                    $value['composer'] = $this->db->query("SELECT id,name FROM musician WHERE id = {$value['id']}")->result_array();
                    $value['lyricist'] = $this->db->query("SELECT id,name FROM musician WHERE id = {$value['id']}")->result_array();
                    $this->unset_key($value, $prisoner);
                }
                $this->response($music);
            }

        }
    }

    /**  上传音乐文件*/
    function index_post(){

    }

    /**
     * 上传音乐信息
     */
    function upinfo_post(){

    }

    /**
     * 删除音乐
     * @param integer $id 音乐id
     * @access administrator
     */
    function index_delete($id){
        //验证管理员权限
        $this->verify_auth();

        //判断音乐是否存在
        if (!$this->db->query('SELECT * FROM music WHERE id = '.$id)->num_rows)
            $this->response(array('error'=>'the music does\'t exist'),404);

        //删除该歌曲
        $this->db->query("DELETE FROM music WHERE id = {$id}") or $this->response(array('error'=>'fail to delete'),500);

        //返回成功信息
        $this->response(array('success'=>'Music has been deleted'),204);
    }

    /**
     * 修改音乐
     * @param integer $id 音乐id
     * @access administrator
     */
    function index_put($id){
        //验证管理员权限
        $this->verify_auth();
        $data = $this->_put_args;

        //判断该音乐是否存在
        if (!$this->db->query('SELECT * FROM music WHERE id = '.$id)->num_rows)
            $this->response(array('error'=>'the music does\'t exist'),404);

        //将数据更新到数据库
        try {
            foreach ($data as $key => $value) {
                switch ($key) {
                    //如果键名属于musician，则需要先判断该艺术家是否在存在与数据表之中
                    case 'singer':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET singer_id = SELECT id FROM musician WHERE name = $value");
                        break;
                    case 'composer':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET composer_id = SELECT id FROM musician WHERE name = $value");
                        break;
                    case 'lyricist':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET lyricist_id = SELECT id FROM musician WHERE name = $value");
                        break;
                    case 'album':
                        $this->db->query("UPDATE music SET album_id = SELECT id FROM album WHERE name = $value");
                        break;
                    default:
                        if ($value)
                            $this->db->query("UPDATE music SET {$key} = {$value}");
                        break;
                }
            }
        }catch (Exception $exception){
            $this->response(array('error'=>'fail to update'),500);
        }

        //返回歌曲信息
        $this->index_get($id);

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
        $token = $this->jwt->decode($headers['Access-Token'],'hjmusic_key');
        $auth = $token->auth;
        if ($auth != 'administrator') $this->response(array('error'=>'Unauthorized'),403);
    }


    /**
     * 判断艺术家是否存在与数据中
     * @param string $musician 艺术家的名字
     */
    private function verify_musician($musician){
        $res = $this->db->query("SELECT * FROM musician WHERE name = {$musician}")->num_rows;
        if ($res == 0)
            $this->response(array('error'=>'this musician is not exist'),404);
    }
}