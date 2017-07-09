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
class users extends REST_Controller
{
    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
    }
    /**
     * 注册
     */
    public function index_post()
    {
//        $data = [
//            'user_email' => $this->input->post('user_email'),
//            'password' => password_hash($this->input->post('password'),PASSWORD_DEFAULT)
//        ];
        $data = json_decode(trim(file_get_contents('php://input')), true);
        $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
        $query = $this->db->get_where('user', array('user_email' => $data['user_email']))->row_array();
        if($query){
            $this->response(['error'=>'邮箱被占用！'], 400);
        }else{
            $this->db->insert('user', $data);
            $user = $this->db->get_where('user', array('user_id' => $this->db->insert_id()))->row_array();
            $user['token'] = $this->jwt->encode(['exp'=>time()+604800,'auth'=>$user['auth'],'user_id'=>$user['user_id']],$this->config->item('encryption_key'));
            unset($user['password']);
            $this->response($user, 200);
        }
    }
    public function signin_post()
    {
//        $data = [
//            'user_email' => $this->input->post('user_email'),
//            'password' => $this->input->post('password')
//        ];
        $data = json_decode(trim(file_get_contents('php://input')), true);
        $user = $this->db->get_where('user', array('user_email' => $data['user_email']))->row_array();
        if($user){
            if (password_verify($data['password'], $user['password'])) {
                $user['token'] = $this->jwt->encode(['exp'=>time()+604800,'auth'=>$user['auth'],'user_id'=>$user['user_id']],$this->config->item('encryption_key'));
                //$test = $this->jwt->decode($user['token'],'hjbook_key');
                //$this->response($test, 200);
                unset($user['password']);
                $this->response($user, 200);
            }else{
                $this->response(['error'=>'密码错误！'], 400);
            }
        }else{
            $this->response(['error'=>'邮箱未注册！'], 400);
        }
    }
    public function records_get($user_id)
    {
        $record = $this->db->get_where('record', array('user_id' => $user_id))->result_array();
        if($record)
            $this->response($record, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any record!'), 404);
    }
    function index_get($id = '')
    {
        $query = $this->db->query('SELECT * FROM user');
        // Example data for testing.
        $user = $query->result_array();

        //if (!$user_id) { $user_id = $this->get('user_id'); }
        if (!$id)

            {
                //$user = $this->user_model->getuser();
                if($user){
                    foreach($user as $key=>$value)
                    {
                        unset($value['password']);
                        $user[$key] = $value;
                    }

                    $this->response($user, 200); // 200 being the HTTP response code
                }

                else
                    $this->response(array('error' => 'Couldn\'t find any user!'), 404);
            }

        //$user = $this->user_model->getuser($id);

        if ($id)
            {
            $query = $this->db->query('SELECT * FROM user WHERE user_id = '.$id);

            $user = $query->row_array();
            if($user){
                unset($user['password']);
                $user['header'] = $this->input->get_request_header('Access-Token');
                $this->response($user, 200); // 200 being the HTTP response code
            }

            else
                $this->response(array('error' => 'user could not be found'), 404);
            }
        if ($id == 0) $this->response(array('error' => 'user could not be found'), 404);
    }

//    function index_post()
//    {
//        if (func_num_args() != 0) $this->response(array('error' => 'cannot post with certain id'), 401);
//        $data = $this->_post_args;
//        try{
//            $query = $this->db->query('INSERT INTO user (user_name, password, real_name, user_email, auth) VALUES ("'.$data['user_name'].'", "'.$data['password'].'","'.$data['real_name'].'","'.$data['user_email'].'", 0)');
//            } catch (Exception $e){
//                $this->response(array('error' => $e->getMessage()), $e->getCode());
//            }
//        $new = $this->db->query('SELECT @@identity');
//        $result = $new->result();
//        //$this->response($result, 200);
//        $new_id = ($result[0]->{'@@identity'});
//        $query = $this->db->query('SELECT * FROM user WHERE user_id = '.$new_id);
//        $user = $query->result();
//            if($user)
//                $this->response($user, 200); // 200 being the HTTP response code
//
//
//        /*
//        try {
//            //$id = $this->user_model->createuser($data);
//            $id = 3; // test code
//            //throw new Exception('Invalid request data', 400); // test code
//            //throw new Exception('user already exists', 409); // test code
//        } catch (Exception $e) {
//            // Here the model can throw exceptions like the following:
//            // * For invalid input data: new Exception('Invalid request data', 400)
//            // * For a conflict when attempting to create, like a resubmit: new Exception('user already exists', 409)
//            $this->response(array('error' => $e->getMessage()), $e->getCode());
//        }
//        if ($id) {
//            $user = array('id' => $id, 'name' => $data['name']); // test code
//            //$user = $this->user_model->getuser($id);
//            $this->response($user, 201); // 201 being the HTTP response code
//        } else
//            $this->response(array('error' => 'user could not be created'), 404);
//        */
//    }

    public function index_put($id = '')
    {
        $data = $this->_put_args;
        if ($id) {
            //存在问题 之前两个都可以为空的
            //是否可以修改名字未知，暂时可以
            $query = $this->db->query('UPDATE user SET user_name = "'.$data['user_name'].'", password = "'.$data['password'].'" WHERE user_id = '.$id);
            $query = $this->db->query('SELECT * FROM user WHERE user_id = '.$id);
            $user = $query->result();
            //$user = array('id' => $data['id'], 'name' => $data['name']); // test code
            //$user = $this->user_model->getuser($id);
            $this->response($user, 200); // 200 being the HTTP response code
        } else
            $this->response(array('error' => 'user could not be found'), 404);

    }

    function index_delete($id = '')
    {
        if (!$id) { $id = $this->get('id'); }
        if (!$id)
        {
            $this->response(array('error' => 'An ID must be supplied to delete a user'), 400);
        }

        $query = $this->db->query('DELETE FROM user WHERE user_id ='.$id);


        if($query) {
            $this->response(array('message' => 'Delete OK!'), 200);
        } else
            $this->response(array('error' => 'user could not be found'), 404);
    }
    public function collections_get($id = '')
    {
        if(!preg_match("/^[1-9]\d*$/",(int)$id)){
            $this->response(array('error' => '用户ID格式不正确'), 400);
        }
        //分页
        $this->db->where('collection.user_id', $id);
        $this->db->from('collection');
        $all_num = $this->db->count_all_results();          //总数
        $this->db->reset_query();
        $offset = isset($_GET['offset']) ? (int)$_GET['offset']>0 ? $_GET['offset'] : 0 : 0;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] :50;
        $foffset = (ceil($all_num/$limit)-1)*$limit<0?0:(ceil($all_num/$limit)-1)*$limit;
        $offset = $offset>$foffset?$foffset:$offset;
        $poffset = ($offset - $limit)>=0?$offset - $limit:0;
        $noffset = ($offset + $limit)<=($all_num-1)?$offset + $limit:$offset;
        $page = [
            'first'=>$_SERVER['HTTP_HOST']."/users/$id/collections?offset=0&limit=".$limit,
            'previous'=>$_SERVER['HTTP_HOST']."/users/$id/collections?offset=".$poffset."&limit=".$limit,
            'next'=>$_SERVER['HTTP_HOST']."/users/$id/collections?offset=".$noffset."&limit=".$limit,
            'final'=>$_SERVER['HTTP_HOST']."/users/$id/collections?offset=".$foffset."&limit=".$limit,
        ];
        $this->db->select('`collection`.`id`,`collection`.`collect_at`,`music`.`id` AS music_id,`music`.`name`,`music`.`cover_url`,`c`.`name` AS `singer_name`,`a`.`name` AS `composer_name`,`b`.`name` AS `lyricist_name`,`c`.id AS `singer_id`,`b`.id AS `lyricist_id`,`a`.id AS `composer_id`,`music`.`lyric_url`,`music`.`album_id`,`music`.`src_url` AS `src`,`music`.`published_at`');
        $this->db->from('collection');
        $this->db->join('music', 'collection.music_id = music.id');
        $this->db->join('musician AS a','music.composer_id = a.id');
        $this->db->join('musician AS b','music.lyricist_id = b.id');
        $this->db->join('musician AS c','music.singer_id = c.id');
        $this->db->where('collection.user_id', $id);
        $this->db->order_by('id');
        $this->db->limit($limit,$offset);
        $sql = $this->db->get()->result_array();
        foreach ($sql as $key=>$value){
            $rs[$key] = [
                'id'=>$value['id'],
                'collect_at'=>$value['collect_at'],
                'music'=>[
                    'id'=>$value['music_id'],
                    'name'=>$value['name'],
                    'cover_url'=>$value['cover_url'],
                    'singer'=>[
                        'id'=>$value['singer_id'],
                        'name'=>$value['singer_name']
                    ],
                    'composer'=>[
                        'id'=>$value['composer_id'],
                        'name'=>$value['composer_name']
                    ],
                    'lyricist'=>[
                        'id'=>$value['lyricist_id'],
                        'name'=>$value['lyricist_name']
                    ],
                    'lyric_url'=>$value['lyric_url'],
                    'album'=>[
                        NULL
//                    'id'=>$value['album_id'],
//                    'name'=>'',
//                    'cover_url'=>'',
//                    'songs_num'=>''
                    ],
                    'src'=>$value['src'],
                    'published_at'=>$value['published_at']
                ],
            ];
        }
        if(empty($rs)){
            $rs = [NULL];
        }
        $this->response(['data'=>$rs,'paging'=>$page], 200);

    }
    private function parsing_token($jwt)
    {
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
        }
        catch(InvalidArgumentException $e)
        {
            $this->response('token解析失败，原因：'.$e->getMessage(),400);
        }
        catch(UnexpectedValueException $e)
        {
            $this->response('token解析失败，原因：'.$e->getMessage(),400);
        }
        return $token;
    }
    public function collections_post($id = '')
    {
        $access_token = $this->input->request_headers();
        if(!empty($access_token['Access-Token'])){
            $token = $this->parsing_token($access_token['Access-Token']);
//            $data = [] ;
        }else{
            $this->response('密钥获取失败，请重新登录！',400);
        }
        try{
            $user_id = $token->user_id;
        }catch (Exception $e){
            $this->response($e,400);
        }
        if(!preg_match("/^[1-9]\d*$/",(int)$id)){
            $this->response(array('error' => '歌曲ID格式不正确'), 400);
        }
//        $sql = 'SELECT music.id FROM music WHERE music.id = 1 ';
        $music_id = $this->db->select('music.id')
            ->from('music')
            ->where('music.id',$id)
            ->get()
            ->row_array();
        if(empty($music_id))
        {
            $this->response(array('error' => '歌曲不存在'), 404);
        }
        $data = [
            'user_id' =>$user_id,
            'music_id' =>$id,
            'collect_at' =>date('Y-m-d H:i:s')
        ];
        $collection_id = $this->db->select('collection.id')
            ->from('collection')
            ->where('collection.music_id',$id)
            ->where('collection.user_id',$user_id)
            ->get()
            ->row_array();
        if(empty($collection_id))
        {
            if($this->db->insert('collection', $data))
            {
                $this->response(array('success' => '收藏成功'), 204);
            }else{
                $this->response(array('error' => '收藏失败'), 400);
            }
        }else{
            $this->response(array('error' => '歌曲已收藏'), 409);
        }

    }
}


