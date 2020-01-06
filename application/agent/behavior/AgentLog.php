<?php

namespace app\agent\behavior;

class AgentLog
{
    public function run(&$params)
    {
        if (request()->isPost()) {
            \app\agent\model\AgentLog::record();
        }
    }
}
