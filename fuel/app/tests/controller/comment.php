<?php
use Fuel\Core\TestCase;
use Fuel\Core\Request;

/**
 * Test_Controller_Comment Class contains all test methods for methods in class Controller_Comment
 * 
 * @group Comment
 * @package Fuel\Core\TestCase
 * @var Test_Controller_Comment Object contains methods to test methods in class Controller_Comment
 */
class Test_Controller_Comment extends TestCase 
{
    private $user;
    
    public function setUp() 
    {
        parent::setUp();
        $this->user = new Test_Controller_User();
    }
    
    public function tearDown() 
    {
        parent::tearDown();
        unset($this->user);
    }
    
    /**
     * add_comment_provider
     * 
     * @return mixed Set of data to test
     */
    public function add_comment_provider() 
    {
        return array(
            array('19', 'Good post!.'),
            array('25', 'Hello.'),
            array('31', 'What\'s the hell.'),
            array('32', 'I got it!Thanks.'),
        );
    }
    
    /**
     * test_post_add Test post_add method in comment controller
     * 
     * @dataProvider add_comment_provider
     * @param int $post_id ID of post to test
     * @param string $cmn_content Content of comment to test
     */
    public function test_post_add($post_id, $comm_content) 
    {
        $token = $this->user->test_post_login('albert', '123');
        if(!empty($token))
        {
            $curl       = Request::forge('http://localhost/test-blog/comment', 'curl');
            $curl->set_method('post');
            $comm_info  = array(
                'post_id' => $post_id,
                'content' => $comm_content,
                'token' => $token
            );
            $curl->set_params($comm_info);
            $curl->execute();
            // Get API Response
            $response       = $curl->response()->body();
            // Convert json response to array
            $arr_msg        = json_decode($response, true);
            // Get API Response code
            $status_actual  = $arr_msg['message']['code'];
            $status_expected= 200;
            $this->assertEquals($status_expected, $status_actual);
        }
    }
    
    /**
     * edit_comment_provider
     * 
     * @return mixed Set of data to test
     */
    public function edit_comment_provider() 
    {
        return array(
            array(27, 'Thanks for your post. I appreciate it.'),
            array(28, 'It makes me stronger. I think so.'),
        );
    }
    
    /**
     * test_put_edit test put_edit method in comment controller
     * 
     * @dataProvider edit_comment_provider
     * @param int $comm_id ID of comment to test
     * @param string $comm_content Content of comment to test
     */
    public function test_put_edit($comm_id, $comm_content) 
    {
        $token = $this->user->test_post_login('albert', '123');
        if(!empty($token))
        {
            $curl       = Request::forge('http://localhost/test-blog/comment_edit', 'curl');
            $curl->set_method('put');
            $comm_info  = array(
                'id' => $comm_id,
                'content' => $comm_content,
                'token' => $token
            );
            $curl->set_params($comm_info);
            $curl->execute();
            // Get API Response
            $response       = $curl->response()->body();
            // Convert json response to array
            $arr_msg        = json_decode($response, true);
            // Get API Response code
            $status_actual  = $arr_msg['message']['code'];
            $status_expected= 10302;
            $this->assertEquals($status_expected, $status_actual);
        }
    }
    
    /**
     * delete_comm_provider
     * 
     * @return mixed Set of data to test
     */
    public function delete_comm_provider() 
    {
        return array(
            array(26),
            array(29)
        );
    }
    
    /**
     * test_delete_remove test delete_remove method in comment controller
     * 
     * @dataProvider delete_comm_provider
     * @param int $comm_id ID of comment to test
     */
    public function test_delete_remove($comm_id) 
    {
        $token  = $this->user->test_post_login('albert', '123');
        if(!empty($token))
        {
            $curl       = Request::forge('http://localhost/test-blog/comment_delete', 'curl');
            $curl->set_method('delete');
            $comm_info  = array(
                'id' => $comm_id,
                'token' => $token
            );
            $curl->set_params($comm_info);
            $curl->execute();
            // Get API Response
            $response       = $curl->response()->body();
            // Convert json response to array
            $arr_msg        = json_decode($response, true);
            // Get API Response code
            $status_actual  = $arr_msg['message']['code'];
            $status_expected= 200;
            $this->assertEquals($status_expected, $status_actual);
        }
    }
    
    /**
     * show_comm_provider
     * 
     * @return mixed Set of data to test
     */
    public function show_comm_provider() 
    {
        return array(
            array(22),
        );
    }
    
    /**
     * test_get_show test get_show method in comment controller
     * 
     * @dataProvider show_comm_provider
     * @param int $post_id ID of post to test
     */
    public function test_get_show($post_id) 
    {
        $token  = $this->user->test_post_login('albert', '123');
        if(!empty($token))
        {
            $curl       = Request::forge('http://localhost/test-blog/comments', 'curl');
            $curl->set_method('get');
            $comm_info  = array(
                'post_id' => $post_id,
                'token' => $token
            );
            $curl->set_params($comm_info);
            $curl->execute();
            // Get API Response
            $response       = $curl->response()->body();
            // Convert json response to array
            $arr_msg        = json_decode($response, true);
            // Get API Response code
            $status_actual  = $arr_msg['message']['code'];
            $status_expected= 200;
            $this->assertEquals($status_expected, $status_actual);
        }
    }
}

