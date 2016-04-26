<?php
/**
 * 模块配置文件
 * wangbaoqing@imooly.com
 * 2015-05-15
 */
return array(
	//主题模板 - Beyond
	'DEFAULT_THEME' => 'Beyond',

    //管理员角色
    'ROLE' => array(
        1 => array('id'=>1, 'rolename'=>'区分局管理员', 'rolerank'=>3),
        2 => array('id'=>2, 'rolename'=>'派出所管理员', 'rolerank'=>2),
    ),
	
	//加载扩展配置文件 引用方式C('x.x')
	'LOAD_EXT_CONFIG' => array(
		//支付配置
		// 'PAY'   => 'pay.config',
	),
);