<?php

namespace app\agent\model\agent_book;

use think\Model;
use traits\model\SoftDelete;

class Chapter extends Model
{

    use SoftDelete;



    // 表名
    protected $name = 'agent_book_chapter';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    public function agentbook()
    {
        return $this->belongsTo('app\agent\model\agent_book\Book', 'book_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
