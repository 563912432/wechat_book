<?php

namespace app\agent\validate\agent_book;

use think\Validate;

class Detail extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
      'title' => 'require'
    ];
    /**
     * 提示消息
     */
    protected $message = [
      'title' => '请填写标题'
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['title'],
        'edit' => ['title'],
    ];

}
