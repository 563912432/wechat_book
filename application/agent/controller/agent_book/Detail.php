<?php

namespace app\agent\controller\agent_book;

use app\agent\model\agent_book\Book as BookModel;
use app\agent\model\agent_book\Chapter as ChapterModel;
use app\common\controller\BackendAgent;
use Exception;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Session;

/**
 * 图书详情管理
 *
 * @icon fa fa-circle-o
 */
class Detail extends BackendAgent
{

    /**
     * Detail模型对象
     * @var \app\agent\model\agent_book\Detail
     */
    protected $model = null;
    protected $chapterList = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\agent\model\agent_book\Detail;

        $bookModel = new BookModel();
        $bookList = $bookModel->where(['agent_id' => Session::get('agent.id')])->field('id, name')->select();
        $this->assign('bookList', $bookList);

        $chapterModel = new ChapterModel();
        // 必须将结果集转换为数组
        $chapterList = collection($chapterModel->where(['we_agent_book_chapter.agent_id' => session('agent.id')])->order('weigh', 'desc')->with(['agentbook'])->select())->toArray();
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
            $myWhere['we_agent_book_detail.agent_id'] = session('agent.id');
            $total = $this->model
                    ->with(['agentbook','agentbookchapter'])
                    ->where($where)
                    ->where($myWhere)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['agentbook','agentbookchapter'])
                    ->where($where)
                    ->where($myWhere)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as &$row) {
              $row['url'] = 'http://www.ceshi.com/'.$row['agentbook']['id'].'/'.$row['id'];
            }
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
        $params['agent_id'] = session('agent.id');
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
     * 生成二维码
     * */
    public function qrcode()
    {
      // 类库使用
      $qrCode = new \Endroid\QrCode\QrCode();
      // 设置生成二维码生成的各项参数
      //设置版本号，QR码符号共有40种规格的矩阵，从21x21（版本1），到177x177（版本40），每一版本符号比前一版本 每边增加4个模块。
      $setVersion = $qrCode -> setVersion(5);
      //容错级别 2的容错率:30% 容错级别：0：15%，1：7%，2：30%，3：25%
      $setErrorCorrection = $qrCode -> setErrorCorrection(2);
      $setModuleSize = $qrCode -> setModuleSize(2);//设置QR码模块大小
      $setImageType = $qrCode -> setImageType('png');//设置二维码保存类型
//      $logo = 'uploads/accountPictrue/logo1.jpg';//logo图片
//      $setLogo = $qrCode -> setLogo($logo);//二维码中间的图片
//      $setLogoSize = $qrCode -> setLogoSize(360);//设置logo大小
      $value = 'https://www.dongtianjr.com'; //二维码内容
      $setText = $qrCode -> setText($value);//设置文字以隐藏QR码。
      $setSize = $qrCode -> setSize(1024);//二维码生成后的大小
      $setPadding = $qrCode -> setPadding(16);//设置二维码的边框宽度，默认16
      $setDrawQuietZone = $qrCode -> setDrawQuietZone(true);//设置模块间距
      $setDrawBorder = $qrCode -> setDrawBorder(true);//给二维码加边框。。。
//      $text = 'XX销售，XX公司！一二';
//      $setLabel = $qrCode -> setLabel($text);//在生成的图片下面加上文字
//      $setLabelFontSize = $qrCode -> setLabelFontSize(39);//生成的文字大小、
//      $lablePath = 'uploads/qr/qr.TTF';
//      $setLabelFontPath = $qrCode -> setLabelFontPath($lablePath);//设置标签字体

      $color_foreground = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];
      $setForegroundColor = $qrCode -> setForegroundColor($color_foreground);//生成的二维码的颜色
      $color_background = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];
      $setBackgroundColor = $qrCode -> setBackgroundColor($color_background);//生成的图片背景颜色

      if (!is_dir(config('CODE_UPLOAD_ROOT_PATH'))) {
//        echo config('CODE_UPLOAD_ROOT_PATH');exit;
        if (!mkdir(config('CODE_UPLOAD_ROOT_PATH'))) {
          $this->error('文件上传根目录创建失败！请检查父目录写入权限');
        }
      }
      $filepath = config('CODE_UPLOAD_ROOT_PATH');
      $flieName = $filepath . md5($value). '.png';//二维码的名字
      $qrCode -> save($flieName);//生成二维码
    }
}
