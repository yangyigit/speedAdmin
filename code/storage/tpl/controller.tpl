<?php
declare(strict_types = 1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Request\#request#;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * ###explain_class###
 * author: #author#
 * Class #class#.
 * @AutoController()
 */
class #class#Controller extends BaseController
{

    /**
     * @Inject()
     * @var #request#
     */
    protected $verifyRequest;


    /**
     * ##^列表展示##
     */
    public function showList(RenderInterface $render)
    {
        if (isAjax($this->request)) {
            $requestData = $this->request -> all();

            $where= createSearchWhere($requestData);

            $count= Db::table('#table#')
                ->where($where)
                ->count();

            $res = Db::table('#table#')
                ->offset(getOffset($requestData))
                ->where($where)
                ->get()
                ->toArray();

            $data['code'] = 0;
            $data['count'] = $count;
            $data['data'] = $res;

            return $this->response->json($data);
        } else {
            //需要显示的按钮
            $btn = [
                'top'=>[
                [
                    'url' => '/admin/#class#/add',
                    'event' => 'add',
                    'name' => '<i class="layui-icon layui-icon-add-1"></i>添加',
                    'style' => ''
                ],
                ],
                'table'=>[
                    [
                        'url' => '/admin/#class#/edit',
                        'event' => 'edit',
                        'name' => '编辑',
                        'style' => '',
                    ],
                    [
                        'url' => '/admin/#class#/del',
                        'event' => 'del',
                        'name' => '删除',
                        'style' => 'layui-btn-danger',
                    ]
                ]
            ];
            //判断显示按钮是否有权限
            $data['btn'] = btnShow($btn, $this->session);

            return $render->render('#url_name#/showList',$data);
        }
    }

    /**
     * ##添加##
     */
    public function add(RenderInterface $render)
    {
        if ($this->request->isMethod('post')) {
            $data = $this->request->all();
            $validated  = $this->verifyRequest->validated();
            $data = array_merge($data,$validated);

            $res = Db::table('#table#')
                ->insert($data);

            if ($res) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.add_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.add_error')]);
            }
        } else {
                return $render->render('#url_name#/add');
        }
    }

    /**
     * ##编辑##
     */
    public function edit(RenderInterface $render)
    {
        if ($this->request -> isMethod('post')) {
            $data = $this->request->all();
            $validated  = $this->verifyRequest->validated();
            $data = array_merge($data,$validated);

            $res = Db::table('#table#')
                ->where('#primary_key#', $data['#primary_key#'])
                ->update($data);

            if ($res) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.edit_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.edit_error')]);
            }
        } else {
            $#primary_key# = $this->request->input('#primary_key#');

            $res = Db::table('#table#')
                ->where('#primary_key#', $#primary_key#)
                ->first();

                return $render->render('#url_name#/edit', ['info' => $res]);
        }
    }

    /**
     * ##删除##
     */
    public function del()
    {
        $#primary_key# = $this->request->input('#primary_key#');

        $res = Db::table('#table#')
            ->where('#primary_key#', $#primary_key#)
            ->delete();

        if ($res) {
            return $this->response->json(['code' => 0, 'msg' => trans('common.alert.del_success')]);
        } else {
            return $this->response->json(['code' => 1, 'msg' => trans('common.alert.del_error')]);
        }
    }
}
