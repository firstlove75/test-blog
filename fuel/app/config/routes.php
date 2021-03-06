<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
//      Config router for user controller
        'user' => 'user/login',
        'user_logout' => 'user/logout',
        'user_register' => 'user/register',
        'user_edit' => 'user/edit',
		'user/pass' => 'user/change_password',
//      Config router for post controller
        'post' => 'post/create',
        'post_edit' => 'post/edit',
        'post_delete' => 'post/delete',
        'posts' => 'post/show',
//      Config router for comment controller
        'comment' => 'comment/add',
        'comment_edit' => 'comment/edit',
        'comments' => 'comment/show',
        'comment_delete' => 'comment/remove'
);