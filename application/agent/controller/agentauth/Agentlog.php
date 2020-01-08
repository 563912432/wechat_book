<?php

namespace app\agent\controller\agentAuth;

use app\agent\model\AgentAuthGroup;
use app\common\controller\BackendAgent;

/**
 * 管理员日志
 *
 * @icon fa fa-users
 * @remark 管理员可以查看自己所拥有的权限的管理员日志
 */
class Agentlog extends BackendAgent
{

    /**
     * @var \app\agent\model\AgentLog
     */
    protected $model = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AgentLog');

        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds($this->auth->isSuperAdmin() ? true : false);

        $groupName = AgentAuthGroup::where('id', 'in', $this->childrenGroupIds)
                ->column('id,name');

        $this->view->assign('groupdata', $groupName);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            $myWhere['agent_id'] = session('agent.id');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where($where)
                    ->where('agent_id', 'in', $this->childrenAdminIds)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->where($where)
                    ->where('agent_id', 'in', $this->childrenAdminIds)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    /**
     * 添加
     * @internal
     */
    public function add()
    {
        $this->error();
    }

    /**
     * 编辑
     * @internal
     */
    public function edit($ids = NULL)
    {
        $this->error();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('agent_id', 'in', function($query) use($childrenGroupIds) {
                        $query->name('agent_auth_group_access')->field('uid');
                    })->select();
            if ($adminList)
            {
                $deleteIds = [];
                foreach ($adminList as $k => $v)
                {
                    $deleteIds[] = $v->id;
                }
                if ($deleteIds)
                {
                    $this->model->destroy($deleteIds);
                    $this->success();
                }
            }
        }
        $this->error();
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }

}
