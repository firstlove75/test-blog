<?php
namespace Model;

class Post extends \Orm\Model {
    protected static $_table_name = 'post';
    protected static $_properties = array(
        'id',
        'title',
        'outline',
        'content',
        'author_id',
        'created_gmt',
        'modified_gmt'
    );
    
    public function create_post($data) {
        $entry = Post::find('all', array(
           'where' => array(
               array('title', '=', $data['title']),
               array('content', '=', $data['content'])
           )
        ));
        $post = Post::forge($data);
        if(count($entry) == 0) {
            $post->save();
            $result['status'] = 200;
            $result['text'] = '';
            $result['data'] = NULL;
        }
        else {
            $result['status'] = 10301;
            $result['text'] = 'Post Existed';
            $result['data'] = NULL;
        }
        return $result;
    }
}
