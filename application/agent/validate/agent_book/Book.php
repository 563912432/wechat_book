<?php

namespace app\agent\validate\agent_book;

use think\Validate;

class Book extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
      'tpl_id' => 'require',
      'name'   => 'require',
      'href'   => 'require|unique:agent_book'
    ];
    /**
     * 提示消息
     */
    protected $message = [
      'tpl_id' => '请选择关联模板',
      'name'   => '请填写书名',
      'href'   => '首页地址已存在'
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['tpl_id', 'name', 'href'],
        'edit' => ['tpl_id', 'name'],
    ];

}
