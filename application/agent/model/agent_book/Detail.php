<?php

namespace app\agent\model\agent_book;

use think\Model;
use traits\model\SoftDelete;

class Detail extends Model
{

    use SoftDelete;



    // 表名
    protected $name = 'agent_book_detail';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function agentbook()
    {
        return $this->belongsTo('app\agent\model\agent_book\Book', 'book_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function agentbookchapter()
    {
        return $this->belongsTo('app\agent\model\agent_book\Chapter', 'chapter_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
