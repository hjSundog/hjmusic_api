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
        //如果没有传入参数，则判断是否有分页要求
        elseif (isset($_GET['offset']) && isset($_GET['limit'])){
            $offset = $_GET['offset'];  $this->lawyer($offset);
            $limit = $_GET['limit'];    $this->lawyer($limit);

            $music = $this->db->query("
            SELECT 
            music.*,
            a.name AS singer_name,
            b.name AS composer_name,
            c.name AS lyricist_name
            FROM music
            INNER JOIN musician AS a ON music.singer_id = a.id
            INNER JOIN musician AS b ON music.composer_id = b.id
            INNER JOIN musician AS c ON music.lyricist_id = c.id
            LIMIT {$offset},{$limit};");

            if (!$music->num_rows())    $this->response(array('error'=>'查询的范围超出'),403);

            $info = $music->result_array();
            foreach ($info as $key => $value) {
                $info[$key]['singer'] = array('id' => $info[$key]['singer_id'], 'name' => $info[$key]['singer_name']);
                $info[$key]['composer'] = array('id' => $info[$key]['composer_id'], 'name' => $info[$key]['composer_name']);
                $info[$key]['lyricist'] = array('id' => $info[$key]['lyricist_id'], 'name' => $info[$key]['lyricist_name']);
//                $info[$key]['album'] = array('id' => $info[$key]['album_id'], 'name' => $info[$key]['album_name']);
                $this->unset_key($info[$key], $prisoner);
            }

            $previous = $_SERVER['HTTP_HOST'].'/music'.'?offset='.($offset-$limit>0 ? $offset-$limit : 1).'&limit='.$limit;
            $next = $_SERVER['HTTP_HOST'].'/music'.'?offset='.($offset+$limit).'&limit='.$limit;

            $final_info['data'] = $info;
            $final_info['paging'] = array('previous'=>$previous,'next'=>$next);

            $this->response($final_info);
        }
        //没有传入分页参数，返回所有music信息
        else{
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
     * 上传音乐信息
     */
    function index_post(){

        //验证用户权限
        $this->verify_auth();

        //加载配置文件
        $this->config->load('Upload.php');
        $temp_dir = $this->config->item('upload_path');
        $music_dir = $this->config->item('music_path');

        //验证文件令牌、判断文件是否存在
        $token = $this->post('token');
        if (empty($token))
            $this->response(array('error'=>'token is missing'),400);

        if (!$this->verify_file($temp_dir,$token))
            $this->response(array('error'=>'not found the file'),404);

        //验证是否接收到json数据
        $data = $this->post('data');
        if (empty($data))
            $this->response(array('error'=>'json data is missing'),400);
        $data = json_decode($data);

        //验证json数据的完整性
        $this->verify_json($data);

        //验证数据中的艺术家是否存在于musician表中
        $this->verify_musician($data->singer_id);
        $this->verify_musician($data->composer_id);
        $this->verify_musician($data->lyricist_id);

        ignore_user_abort(true);

        //向music表插入数据
        $field = "";
        $f_value = "";
        foreach ($data as $key=>$value){
            $field .= $key.",";
            $f_value .= "'".$value."',";
        }
        $field = substr($field,0,strlen($field)-1);
        $f_value = substr($f_value,0,strlen($f_value)-1);
        $this->db->query("INSERT INTO music ({$field}) VALUE ({$f_value})");

        //获取插入音乐的id
        $id = $this->last_insert_id();

        //将文件从temp文件夹移动到music文件夹，重命名为主键id
        $type = explode('.',$token)[1];
        rename($temp_dir.$token,$music_dir.$id.'.'.$type) or $this->response(array('error'=>'can\'t move file'),406);

        $this->response(array('success'=>'The FileInfo upload complete'),200);
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

//        $data = $this->_put_args;
        //使用前验证是否接收到json数据
        try {
            $data = json_decode($this->put('json'));
        }
        catch (Exception $exception){
            $this->response(array('error'=>'json data is missing'),400);
        }

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
                        $this->db->query("UPDATE music SET singer_id = {$id} WHERE id = {$id}");
                        break;
                    case 'composer':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET composer_id = {$id} WHERE id = {$id}");
                        break;
                    case 'lyricist':
                        $this->verify_musician($value);
                        $this->db->query("UPDATE music SET lyricist_id = {$id} WHERE id = {$id}");
                        break;
                    case 'album':
                        $this->db->query("UPDATE music SET album_id = {$id} WHERE id = {$id}");
                        break;
                    default:
                        if ($value)
                            $this->db->query("UPDATE music SET {$key} = '$value' WHERE id = {$id}");
                        break;
                }
            }
        }catch (Exception $exception){
            $this->response(array('error'=>'fail to update'),500);
        }

        //返回修改后的歌曲信息
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

        if (empty($headers['Access-Token']))
            $this->response(array('error'=>'Access-Token is missing'));

        try {
            $token = $this->jwt->decode($headers['Access-Token'], 'hjmusic_key');
        }catch (Exception $exception){
            $this->response(array('error'=>'Authorization failed:'.$exception->getMessage()),403);
            return;
        }
        $auth = $token->auth;
        if ($auth != 'administrator') $this->response(array('error'=>'Unauthorized'),403);
    }


    /**
     * 判断艺术家是否存在与数据中
     * @param $musician mixed 艺术家的id
     */
    private function verify_musician($musician){
        try {
            $this->db->query("SELECT * FROM musician WHERE id = {$musician->id}");
        }
        catch (Exception $exception){
                $this->response(array('error' => 'this musician is not exist'), 404);
        }
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
            $info = $music->result_array()[0];

            $info['singer'] = array('id'=>$info['singer_id'],'name'=>$info['singer_name']);
            $info['composer'] = array('id'=>$info['composer_id'],'name'=>$info['composer_name']);
            $info['lyricist'] = array('id'=>$info['lyricist_id'],'name'=>$info['lyricist_name']);
//            $info[0]['album'] = array('id'=>$info[0]['album_id'],'name'=>$info[0]['album_name']);
            $this->unset_key($info, $prisoner);
            $this->response($info);
        }
    }


    /**
     * 数据合法性判断，判断是否为num类型
     * @param $id mixed 通过url传入的值
     */
    private function lawyer($id){
        is_numeric($id) && $id>=0 or $this->response(array('error'=>'the music id must be number or greater than zero'),400);
    }


    /**
     * 判断令牌对应的文件是否存在于临时文件目录中
     * @param $dir string 临时文件目录
     * @param $token string 文件令牌
     * @return true 文件存在，返回true,此时令牌名就是文件名
     * @return false 文件不存在，返回false
     */
    private function verify_file($dir,$token){
        if (is_dir($dir)){
            if ($dh = opendir($dir)){
                while ($file = readdir($dh) !== false){
                    if ($file == $token){
                        return true;
                    }
                }
            }
        }
        return false;
    }


    /**
     * 验证前端传来的json数据的完整性
     * @param $data object 解码的json数据
     */
    private function verify_json($data){
        $require = array('id','name','cover_url','singer_id','composer_id','lyricist_id','src_url','published_at');
        foreach ($require as $value){
            if (!isset($data->{$value}))
                $this->response(array('error'=>$value.' is require'),403);
        }
    }


    /**
     * 获取最后一次插入的数据的主键id
     * @return mixed $id 最后一次插入的数据的id
     */
    private function last_insert_id(){
        $res = $this->db->query("SELECT LAST_INSERT_ID() AS id;")->result_array();
        return $res[0]['id'];
    }
}