<?php

namespace app\agent\controller\agent_book;

use app\agent\model\agent_book\Book as BookModel;
use app\agent\model\agent_book\Chapter as ChapterModel;
use app\common\controller\BackendAgent;
use fast\Tree;
use think\Session;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Error extends BackendAgent
{

    /**
     * Error模型对象
     * @var \app\agent\model\agent_book\Error
     */
    protected $model = null;
    protected $chapterList = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\agent\model\agent_book\Error;

        $bookModel = new BookModel();
        $bookList = $bookModel->where(['agent_id' => Session::get('agent.id')])->field('id, name')->select();
        $this->assign('bookList', $bookList);

        $chapterModel = new ChapterModel();
        // 必须将结果集转换为数组
        $chapterList = collection($chapterModel->order('weigh', 'desc')->with(['agentbook'])->select())->toArray();
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
        $this->view->assign('chapterList', $ruledata);

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
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['agentbook','agentbookchapter'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['agentbook','agentbookchapter'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {


            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
