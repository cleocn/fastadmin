<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;

/**
 * 基本配置
 *
 * @icon fa fa-cog
 * @remark 用于管理一些字典数据,通常以键值格式进行录入,保存的数据格式为JSON
 */
class Configvalue extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Configvalue');
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

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
        if ($this->request->isPost())
        {
            $this->code = -1;
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($this->request->has('field'))
                {
                    //JSON字段
                    $fieldarr = $valuearr = [];
                    $field = $this->request->post('field/a');
                    $value = $this->request->post('value/a');
                    foreach ($field as $k => $v)
                    {
                        if ($v != '')
                        {
                            $fieldarr[] = $field[$k];
                            $valuearr[] = $value[$k];
                        }
                    }
                    $params['content'] = array_combine($fieldarr, $valuearr);
                }
                $this->model->save($params);
                $this->code = 1;
            }

            return;
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost())
        {
            $this->code = -1;
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($this->request->has('field'))
                {
                    //JSON字段
                    $fieldarr = $valuearr = [];
                    $field = $this->request->post('field/a');
                    $value = $this->request->post('value/a');
                    foreach ($field as $k => $v)
                    {
                        if ($v != '')
                        {
                            $fieldarr[] = $field[$k];
                            $valuearr[] = $value[$k];
                        }
                    }
                    $params['content'] = array_combine($fieldarr, $valuearr);
                }
                $row->save($params);
                $this->code = 1;
            }

            return;
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        $this->code = -1;
        if ($ids)
        {
            $count = $this->model->where('id', 'in', $ids)->delete();
            if ($count)
            {
                $this->code = 1;
            }
        }

        return;
    }

    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        $this->code = -1;
        $ids = $ids ? $ids : $this->request->param("ids");
        if ($ids)
        {
            if ($this->request->has('params'))
            {
                parse_str($this->request->post("params"), $values);
                $values = array_intersect_key($values, array_flip(array('status')));
                if ($values)
                {
                    $count = $this->model->where('id', 'in', $ids)->update($values);
                    if ($count)
                    {
                        $this->code = 1;
                    }
                }
            }
            else
            {
                $this->code = 1;
            }
        }

        return;
    }

}
