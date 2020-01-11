<?php

namespace app\api\controller;

use app\agent\model\agent_book\Detail;
use app\common\controller\Api;
use app\common\model\AgentConfig as AgentConfigModel;
use app\agent\model\agent_book\Book as BookModel;
use app\agent\model\agent_book\Chapter as ChapterModel;
use app\agent\model\agent_book\Detail as DetailModel;

class Wechat extends Api
{
  protected $noNeedLogin = ['*'];
  protected $noNeedRight = '*';

  public function _initialize()
  {
    parent::_initialize();
  }

  public function book()
  {
    if ($this->request->isPost()) {
      $params = $this->request->request();
//      $params['tag'] $params['book_id'] $params['agent_id']
      // 取代理信息
      $agentConfigModel = new AgentConfigModel();
      $siteName = $agentConfigModel->where(['agent_id' => $params['agent_id'],'name' => 'name'])->value('value');
      // 取图书信息
      $bookModel = new BookModel();
      $bookInfo = $bookModel->where(['id' => $params['book_id']])->find();
      // 取章节
      $chapterModel = new ChapterModel();
      $chapterInfo = $chapterModel->where(['book_id' => $params['book_id']])->select();
      $data['siteName'] = $siteName;
      if ($bookInfo) {
        $data['bookInfo'] = $bookInfo;
      } else {
        $data['bookInfo'] = [];
      }
      if ($chapterInfo) {
        $data['chapterInfo'] = $chapterInfo;
      } else {
        $data['chapterInfo'] = [];
      }
      $this->success('success', $data);
    } else {
      $this->error('error');
    }
  }

  public function chapter ()
  {
    if ($this->request->isPost()) {
      $params = $this->request->request();
      $cid = $params['cid'];
      // 分类下面的卡片
      $detailModel = new DetailModel();
      $info = $detailModel->where(['chapter_id' => $cid])->select();
      if ($info) {
        $this->success('success', $info);
      } else {
        $this->error('error');
      }
    }
  }
}
