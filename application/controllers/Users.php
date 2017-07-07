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
    public function collections_get($id = '',$pn = '')
    {
        if(empty($id)){
            $this->response(array('error' => '用户ID不能为空'), 400);
        }
        //分页
        $this->db->where('collection.user_id', $id);
        $this->db->from('collection');
        $all_num = $this->db->count_all_results();          //总数
        $this->db->reset_query();
        $page_num = 20;                                           //每页显示20条
        $page_count =ceil($all_num/$page_num);
        if ($page_count == 0){
            $page_count =1;
        }
        if($pn > $page_count){
            $pn = $page_count;
        }
        $pn         =empty($pn)?1:$pn;                    //当前页数
        $pn         =(int)$pn;                              //安全强制转换
        $limit_st     =($pn-1)*$page_num;                     //起始数
        $this->db->select('music.id,music.`name`,music.cover_url,c.`name` AS singer,a.`name` AS composer,b.`name` AS lyricist,music.lyric_url,music.album_id,music.src_url,music.published_at');
        $this->db->from('collection');
        $this->db->join('music', 'collection.music_id = music.id');
        $this->db->join('musician AS a','music.composer_id = a.id');
        $this->db->join('musician AS b','music.composer_id = b.id');
        $this->db->join('musician AS c','music.composer_id = c.id');
        $this->db->where('collection.user_id', $id);
        $this->db->limit($page_num,$limit_st);
        $sql = $this->db->get()->result_array();
        $sql['page_count']  = $page_count;
        $sql['current_page'] = $pn;
        $this->response($sql, 200);
//        var_dump($sql);
    }
    public function collections_post($id = '')
    {
        $user_id = 1;//暂时先写死，再改成从token获取id的形式
        if(empty($id)){
            $this->response(array('error' => '歌曲ID不能为空'), 400);
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


