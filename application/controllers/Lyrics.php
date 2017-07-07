<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package CodeIgniter
 * @subpackage  Rest Server
 * @category    Controller
 * @author  Adam Whitney
 * @link    http://outergalactic.org/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';
class Lyrics extends REST_Controller
{
    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:*');
    }

    /**
     * 查看歌词信息
     * @param mixed $id 歌词id
     */
    function index_get($id = null){

        //需要重构的数据
        $prisoner = array('lyric_id','music_id','singer_id','composer_id','lyricist_id','album_id','uploader_id');

        //如果传入参数，则返回该id的lyric信息
        if (isset($id)) {
            //判断id是否为num类型
            $this->lawyer($id);

            //获取指定id的歌曲信息
            $this->aim_lyric($id,$prisoner);

        }
		//如果没有传入参数，则判断是否有分页要求
        elseif (isset($_GET['offset']) && isset($_GET['limit'])){
            $offset = $_GET['offset']; 
            $this->lawyer($offset);
            $limit = $_GET['limit']; 
            $this->lawyer($limit);
            $this->aim_lyrics($offset,$limit);

  		}

  		//没有传入分页参数，返回所有歌词信息
  		else{
	    	//获取歌词的信息
	    	
	    	$res = $this->db->query("SELECT
				*
				FROM lyric

	    		");
	    	if(! $res->num_rows()){
	    		$this->response(array(['error' => '超出查询的范围']), 403);
	    	}else{
		    	$lyric = $res->result_array();

		    	foreach ($lyric as $key => $value) {

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
			        WHERE music.id = {$lyric[$key]['music_id']};

			        ")
			        ->result_array()[0];

			        $uploader = $this->db->query("SELECT
						*
						FROM user
						WHERE id = {$lyric[$key]['uploader_id']};
			        	")
			        ->result_array()[0];

			        $album = $this->db->query("SELECT
						*
						FROM album
						WHERE id = {$music['album_id']};
			        	")
			        ->result_array()[0];
			        $info[$key] = array(

			        		'id' => $lyric[$key]['id'],
			        		'uploaded_at' => $lyric[$key]['uploaded_at'],
			        		'lyric' => $lyric[$key]['lyric'],
			        		'music' => array(
			        				'id' => $music['id'],
			        				'name' => $music['name'],
			        				'coverr_url' => $music['cover_url'],
			        				'singer' => array(
			        						'id' => $music['singer_id'],
			        						'name' => $music['singer_name']
			        					),
			        				'composer' => array(
			        						'id' =>  $music['composer_id'],
			        						'name' => $music['composer_name']
			        					),
			        				'lyricist' => array(
			        						'id' => $music['lyricist_id'],
			        						'name' => $music['lyricist_name']
			        					),
			        				'lyric_url' => $music['lyric_url'],
			        				'album' => array(
			        						'id' => $album['id'],
			        						'name' => $album['name'],
			        						'cover_url' => $album['cover_url'],
			        						'songs_num' => $album['songs_num']
			        					),
			        				'src' => $music['src_url'],
			        				'published_at' => $music['published_at']
			        			),
			        		'uploader' => array(
			        				'id' => $uploader['id'],
			        				'username' => $uploader['username'],
			        				'realname' => $uploader['realname'],
			        				'email' => $uploader['email'],
			        				'auth' => $uploader['auth']
			        			)

			        	);
		    	}

		    	$this->response($info);

	    	}

  		}
	}

   /**
     * 上传歌词信息
     */
    function index_post(){


        //验证文件令牌、判断文件是否存在
        $token = $this->post('token');
        if (empty($token))
            $this->response(array('error'=>'token is missing'),400);


        //验证是否接收到json数据
        $data = $this->post('data');
        if (empty($data))
            $this->response(array('error'=>'json data is missing'),400);
        $data = json_decode($data);

        //验证json数据的完整性
        $this->verify_json($data);

        //验证数据中的音乐是否存在于music表中
        $this->verify_music($data->music_id);
      
        //不会检测到用户是否已断开连接，直到尝试向客户机发送信息为止
        ignore_user_abort(true);

        //向lyric表插入数据
        $field = "";
        $f_value = "";
        foreach ($data as $key=>$value){
            $field .= $key.",";
            $f_value .= "'".$value."',";
        }
        $field = substr($field,0,strlen($field)-1);
        $f_value = substr($f_value,0,strlen($f_value)-1);
        $this->db->query("INSERT INTO lyric ({$field}) VALUE ({$f_value})");

        //获取插入歌词的id
        $id = $this->last_insert_id();

		$this->response(array('success'=>'The FileInfo upload complete'),200);
  
        $this->aim_lyric($id,$prisoner);

    }

   /**
     * 删除歌词
     * @param mixed $id 歌词id
     * @access administrator
     */
    function index_delete($id){
		//判断id是否为num类型
        $this->lawyer($id);

        //验证管理员权限
        //$this->verify_auth();

        //判断歌词是否存在
        if (!$this->db->query('SELECT * FROM lyric WHERE id = '.$id)->num_rows())
            $this->response(array('error'=>'the lyric does\'t exist'),404);

        //删除该歌词，如果数据库操作出错则返回500错误
        $this->db->query("DELETE FROM lyric WHERE id = {$id}") or $this->response(array('error'=>'fail to delete'),500);

        //返回成功信息
        $this->response(null,204);


    }



    /***********************
     * ↓↓↓工具方法↓↓↓*
     ***********************/

    /**
     * 数据合法性判断，判断是否为num类型
     * @param $id mixed 通过url传入的值
     */
    private function lawyer($id){
        is_numeric($id) && $id>=0 or $this->response(array('error'=>'the music id must be number or greater than zero'),400);
    }


    /**
     * 返回指定id的歌词信息
     * @param $id mixed 音乐id
     * @param $prisoner array 需要重构的数据（数组key值）
     */
    private function aim_lyric($id,$prisoner){

    	//获取该歌词的信息
    	
    	$res = $this->db->query("SELECT
			*
			FROM lyric
			WHERE id = {$id};

    		");
    	if(! $res->num_rows()){
    		$this->response(array(['error' => 'this lyric is not find']), 404);
    	}else{
	    	$lyric = $res->result_array()[0];


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
	        WHERE music.id = {$lyric['music_id']};

	        ")
	        ->result_array()[0];



	        $uploader = $this->db->query("SELECT
				*
				FROM user
				WHERE id = {$lyric['uploader_id']};
	        	")
	        ->result_array()[0];

	        $album = $this->db->query("SELECT
				*
				FROM album
				WHERE id = {$music['album_id']};
	        	")
	        ->result_array()[0];

	        $info = array(

	        		'id' => $lyric['id'],
	        		'uploaded_at' => $lyric['uploaded_at'],
	        		'lyric' => $lyric['lyric'],
	        		'music' => array(
	        				'id' => $music['id'],
	        				'name' => $music['name'],
	        				'coverr_url' => $music['cover_url'],
	        				'singer' => array(
	        						'id' => $music['singer_id'],
	        						'name' => $music['singer_name']
	        					),
	        				'composer' => array(
	        						'id' =>  $music['composer_id'],
	        						'name' => $music['composer_name']
	        					),
	        				'lyricist' => array(
	        						'id' => $music['lyricist_id'],
	        						'name' => $music['lyricist_name']
	        					),
	        				'lyric_url' => $music['lyric_url'],
	        				'album' => array(
	        						'id' => $album['id'],
	        						'name' => $album['name'],
	        						'cover_url' => $album['cover_url'],
	        						'songs_num' => $album['songs_num']
	        					),
	        				'src' => $music['src_url'],
	        				'published_at' => $music['published_at']
	        			),
	        		'uploader' => array(
	        				'id' => $uploader['id'],
	        				'username' => $uploader['username'],
	        				'realname' => $uploader['realname'],
	        				'email' => $uploader['email'],
	        				'auth' => $uploader['auth']
	        			)

	        	);
			$this->response($info);
			


    	}
    	





    }


    /**
     * 返回所有歌词信息 包含分页
     * @param offset
     * @param limit
     */
    private function aim_lyrics($offset,$limit){
 
    	//获取该歌词的信息
    	
    	$res = $this->db->query("SELECT
			*
			FROM lyric
			LIMIT {$offset},{$limit};

    		");
    	if(! $res->num_rows()){
    		$this->response(array(['error' => '超出查询的范围']), 403);
    	}else{
	    	$lyric = $res->result_array();

	    	foreach ($lyric as $key => $value) {

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
		        WHERE music.id = {$lyric[$key]['music_id']};

		        ")
		        ->result_array()[0];

		        $uploader = $this->db->query("SELECT
					*
					FROM user
					WHERE id = {$lyric[$key]['uploader_id']};
		        	")
		        ->result_array()[0];

		        $album = $this->db->query("SELECT
					*
					FROM album
					WHERE id = {$music['album_id']};
		        	")
		        ->result_array()[0];
		        $info[$key] = array(

		        		'id' => $lyric[$key]['id'],
		        		'uploaded_at' => $lyric[$key]['uploaded_at'],
		        		'lyric' => $lyric[$key]['lyric'],
		        		'music' => array(
		        				'id' => $music['id'],
		        				'name' => $music['name'],
		        				'coverr_url' => $music['cover_url'],
		        				'singer' => array(
		        						'id' => $music['singer_id'],
		        						'name' => $music['singer_name']
		        					),
		        				'composer' => array(
		        						'id' =>  $music['composer_id'],
		        						'name' => $music['composer_name']
		        					),
		        				'lyricist' => array(
		        						'id' => $music['lyricist_id'],
		        						'name' => $music['lyricist_name']
		        					),
		        				'lyric_url' => $music['lyric_url'],
		        				'album' => array(
		        						'id' => $album['id'],
		        						'name' => $album['name'],
		        						'cover_url' => $album['cover_url'],
		        						'songs_num' => $album['songs_num']
		        					),
		        				'src' => $music['src_url'],
		        				'published_at' => $music['published_at']
		        			),
		        		'uploader' => array(
		        				'id' => $uploader['id'],
		        				'username' => $uploader['username'],
		        				'realname' => $uploader['realname'],
		        				'email' => $uploader['email'],
		        				'auth' => $uploader['auth']
		        			)

		        	);
	    	}

            $previous = $_SERVER['HTTP_HOST'].'/lyrics'.'?offset='.($offset-$limit>0 ? $offset-$limit : 1).'&limit='.$limit;
            $next = $_SERVER['HTTP_HOST'].'/lyrics'.'?offset='.($offset+$limit).'&limit='.$limit;

            $final_info['data'] = $info;
            $final_info['paging'] = array('previous'=>$previous,'next'=>$next);

	    	$this->response($final_info);

    	}
    }



    /**
     * 判断歌曲是否存在与数据中
     * @param $music_id 音乐id
     */
    private function verify_music($music_id){
        try {
            $this->db->query("SELECT * FROM music WHERE id = {$music_id}");
        }
        catch (Exception $exception){
                $this->response(array('error' => 'this music is not exist'), 404);
        }
    }


    /**
     * 验证前端传来的json数据的完整性
     * @param $data object 解码的json数据
     */
    private function verify_json($data){
        $require = array('music_id','lyric');
        foreach ($require as $value){
            if (!isset($data->{$value}))
                $this->response(array('error'=>$value.' is require'),403);
        }
    }






}
