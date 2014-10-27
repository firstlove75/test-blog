<?php

use Fuel\Core\Session;
use Fuel\Core\Controller_Rest;
use Auth\Auth;
use Fuel\Core\Input;
use Fuel\Core\Security;
use Model\Comment;

class Controller_Comment extends Controller_Rest 
{

    protected $format = 'json';

    public function before() 
    {
        parent::before();
        if (Auth::check() && !empty(Session::get('token'))) 
        {
            echo '';
        } 
        else 
        {
            return $this->response(array(
                'message' => array(
                    'code' => 10301,
                    'text' => 'Please login'
                ),
                'data' => null
            ));
        }
    }

    public function post_add() 
    {
        if (Auth::check() && !empty(Session::get('token'))) 
        {
            // get id of author
            $arr_auth   = Auth::instance()->get_user_id();
            $author_id  = $arr_auth[1];
            // get inputs
            $post_id    = (int) Input::post('post_id');
            $content    = Security::strip_tags(Security::xss_clean(Input::post('content')));
            $time       = time();
            $comment    = new Comment();

            if ($post_id > 0 && !empty($post_id) && !empty($content)) 
            {
                $flag   = $comment->is_existed_post($post_id);
                // Check post existed, if post existed then add new comment
                if ($flag == 1) 
                {
                    $data   = array(
                        'content' => $content,
                        'author_id' => $author_id,
                        'post_id' => $post_id,
                        'created_gmt' => $time,
                        'modified_gmt' => 0
                    );
                    $result = $comment->add_comment($data);
                    return $this->response(array(
                        'message' => array(
                            'code' => $result['status'],
                            'text' => $result['text']
                        ),
                        'data' => null
                    ));
                } 
                else 
                {
                    return $this->response(array(
                        'message' => array(
                            'code' => 10300,
                            'text' => 'Database Exception'
                        ),
                        'data' => null
                    ));
                }
            } 
            else 
            {
                return $this->response(array(
                    'message' => array(
                        'code' => 401,
                        'text' => 'Invalid Input'
                    ),
                    'data' => null
                ));
            }
        }
    }

    public function delete_remove() 
    {
        if (Auth::check() && !empty(Session::get('token'))) 
        {
            $comm_id = (int)Input::delete('id');
            if (empty($comm_id) || $comm_id <= 0) 
            {
                return $this->response(array(
                    'message' => array(
                        'code' => 404,
                        'text' => 'Not Found'
                    ),
                    'data' => null
                ));
            } 
            else 
            {
                $comment    = new Comment();
                $result     = $comment->delete_comment($comm_id);
                return $this->response(array(
                    'message' => array(
                        'code' => $result['status'],
                        'text' => $result['text']
                    ),
                    'data' => $result['data']
                ));
            }
        }
    }

    public function get_show() 
    {
        if (Auth::check() && !empty(Session::get('token'))) 
        {
            // Check input is valid or invalid
            $post_id    = (int)Input::get('post_id');
            if (empty($post_id) || $post_id <= 0) 
            {
                return $this->response(array(
                    'message' => array(
                        'code' => 401,
                        'text' => 'Invalid Input'
                    ),
                    'data' => null
                ));
            }
            $comment    = new Comment();
            $result     = $comment->get_comments_of_specified_post($post_id);
            if (count($result) != 0) 
            {
                $data   = array();
                $i      = 0;
                // create data[] array
                foreach ($result as $item) 
                {
                    $data[$i]['id']             = $item['id'];
                    $data[$i]['content']        = $item['content'];
                    $data[$i]['author_id']      = $item['author_id'];
                    $data[$i]['post_id']        = $item['post_id'];
                    $data[$i]['created_gmt']    = gmdate('Y-m-d H:i:s', $item['created_gmt']);
                    $data[$i]['modified_gmt']   = gmdate('Y-m-d H:i:s', $item['modified_gmt']);
                    $i++;
                }
                return $this->response(array(
                    'message' => array(
                        'code' => 200,
                        'text' => ''
                    ),
                    'data' => $data
                ));
            } 
            else 
            {
                return $this->response(array(
                    'message' => array(
                        'code' => 404,
                        'text' => 'Not Found'
                    ),
                    'data' => null
                ));
            }
        }
    }

    public function put_edit() 
    {
        if (Auth::check() && !empty(Session::get('token'))) 
        {
            // Check input is valid or invalid
            $comm_id    =  (int)Input::put('id');
            if (empty($comm_id) || $comm_id <= 0) 
            {
                return $this->response(array(
                    'message' => array(
                        'code' => 401,
                        'text' => 'Invalid Input'
                    ),
                    'data' => null
                ));
            }

            // Get post
            $comment        = new Comment();
            $result         = $comment->get_comment($comm_id);
            // Get input
            $content        = htmlspecialchars_decode(Security::strip_tags(Security::xss_clean(Input::put('content'))), ENT_QUOTES);
            $modify_time    = time();

            if (Input::put('id') && !empty($content) && $content != $result['content']) 
            {
                $data = array(
                    'id' => $result['id'],
                    'content' => (empty($content)) ? $result['content'] : htmlspecialchars_decode($content, ENT_QUOTES),
                    'author_id' => $result['author_id'],
                    'post_id' => $result['post_id'],
                    'created_gmt' => $result['created_gmt'],
                    'modified_gmt' => $modify_time
                );
                $comment->update_comment($comm_id, $data);
                $data['created_gmt']    = gmdate('Y-m-d H:i:s', $result['created_gmt']);
                $data['modified_gmt']   = gmdate('Y-m-d H:i:s', $result['modified_gmt']);
                return $this->response(array(
                    'message' => array(
                        'code' => 10302,
                        'text' => 'Updated Successfully'
                    ),
                    'data' => $data
                ));
            } 
            else 
            {
                if (count($result) != 0) 
                {
                    $data = array(
                        'id' => $result['id'],
                        'content' => $result['content'],
                        'author_id' => $result['author_id'],
                        'post_id' => $result['post_id'],
                        'created_gmt' => gmdate('Y-m-d H:i:s', $result['created_gmt']),
                        'modified_gmt' => gmdate('Y-m-d H:i:s', $result['modified_gmt'])
                    );
                    return $this->response(array(
                        'message' => array(
                            'code' => 200,
                            'text' => ''
                        ),
                        'data' => $data
                    ));
                } 
                else 
                {
                    return $this->response(array(
                        'message' => array(
                            'code' => 404,
                            'text' => 'Not Found'
                        ),
                        'data' => null
                    ));
                }
            }
        }
    }
}
