<?php

namespace app\admin\validate;

use think\Validate;

class BookTpl extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
      'no'  => 'require|unique:bookTpl',
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['no'],
        'edit' => [],
    ];

}
