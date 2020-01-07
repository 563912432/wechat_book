<?php

namespace app\agent\controller\agent_book;

use app\common\controller\BackendAgent;
use app\agent\model\agent_book\Book as BookModel;
use fast\Tree;
use think\Session;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Chapter extends BackendAgent
{

    /**
     * Chapter模型对象
     * @var \app\agent\model\agent_book\Chapter
     */
    protected $model = null;
    protected $chapterList = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\agent\model\agent_book\Chapter;
        $bookModel = new BookModel();
        $bookList = $bookModel->where(['agent_id' => Session::get('agent.id')])->field('id, name')->select();
        $this->assign('bookList', $bookList);

        // 必须将结果集转换为数组
        $chapterList = collection($this->model->order('weigh', 'desc')->with(['agentbook'])->select())->toArray();
        foreach ($chapterList as $k => &$v)
        {
          $v['title'] = __($v['title']);
          $v['remark'] = __($v['remark']);
        }
        unset($v);
        Tree::instance()->init($chapterList);
        $this->chapterList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $ruledata = [0 => __('None')];
        foreach ($this->chapterList as $k => &$v)
        {
          $ruledata[$v['id']] = $v['title'];
        }
        $this->view->assign('ruledata', $ruledata);

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        if ($this->request->isAjax())
        {
          $list = $this->chapterList;
          $total = count($this->chapterList);

          $result = array("total" => $total, "rows" => $list);

          return json($result);
        }
        return $this->view->fetch();
    }
}
