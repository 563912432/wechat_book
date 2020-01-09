<?php

namespace app\agent\controller\agent_book;

use app\admin\model\BookTpl as BookTplModel;
use app\admin\model\BookTpl;
use app\agent\model\agent_book\Book as BookModel;
use app\common\controller\BackendAgent;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Session;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Book extends BackendAgent
{

    /**
     * Book模型对象
     * @var \app\agent\model\agent_book\Book
     */
    protected $model = null;
    protected $agent_id = '';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\agent\model\agent_book\Book;
        $this->agent_id = Session::get('agent.id');
        // 关联模板
        $tplModel = new BookTplModel();
        $bookTplList = $tplModel->field('id, name')->select();
        $this->assign('bookTplList', $bookTplList);
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/agent/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     */
    public function index()
    {
      //设置过滤方法
      $this->request->filter(['strip_tags']);
      if ($this->request->isAjax()) {
        //如果发送的来源是Selectpage，则转发到Selectpage
        if ($this->request->request('keyField')) {
          return $this->selectpage();
        }
        $myWhere = [];
        /*根据权限产生不同的where条件*/
        /*$con = "";
        if($con) {
          $mywhere["tapply.id"] = 1;
        }*/
        $myWhere['agent_id'] = session('agent.id');
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        $total = $this->model
          ->where($where)
          ->where($myWhere)
          ->with(['tpl'])
          ->order($sort, $order)
          ->count();

        $list = $this->model
          ->where($where)
          ->where($myWhere)
          ->with(['tpl'])
          ->order($sort, $order)
          ->limit($offset, $limit)
          ->select();

        $list = collection($list)->toArray();
        $result = array("total" => $total, "rows" => $list);
        return json($result);
      }
      return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
      if ($this->request->isPost()) {
        $params = $this->request->post("row/a");
        $params['agent_id'] = $this->agent_id;
        if ($params) {
          $params = $this->preExcludeFields($params);

          if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
          }
          $result = false;
          Db::startTrans();
          try {
            //是否采用模型验证
            if ($this->modelValidate) {
              $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
              $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
              $this->model->validateFailException(true)->validate($validate);
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
          } catch (ValidateException $e) {
            Db::rollback();
            $this->error($e->getMessage());
          } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
          } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
          }
          if ($result !== false) {
            $this->success();
          } else {
            $this->error(__('No rows were inserted'));
          }
        }
        $this->error(__('Parameter %s can not be empty', ''));
      }
      return $this->view->fetch();
    }

    /*
     * 模板图片预览
     * */
    public function detail()
    {
      // 所有模板的图片预览
      $tplModel = new BookTpl();
      $list = $tplModel->select();
      foreach ($list as &$value) {
        $value['thumb'] = explode(',', $value['thumb']);
      }
      $this->assign('list', $list);
      return $this->view->fetch();
    }

    public function large($image)
    {
      $this->assign('image', $image);
      return $this->view->fetch();
    }
}
