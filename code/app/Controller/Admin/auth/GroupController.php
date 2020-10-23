<?php


namespace App\Controller\Admin\auth;

use App\Controller\BaseController;
use App\Model\auth\Group;
use App\Request\GroupRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;
use Hyperf\DbConnection\Db;

/**
 * ##权限管理-用户组##
 * Class Group
 * @package app\admin\controller\auth
 */
class GroupController extends BaseController
{

    /**
     * @Inject()
     * @var GroupRequest
     */
    protected $groupRequest;


    /**
     * @Inject()
     * @var Group
     */
    protected $groupModel;

    /**
     * ##^用户组列表##
     */
    public function showList(RenderInterface $render)
    {

        if (isAjax($this->request)) {
            $requestData = $this->request -> all();
            $map = createSearchWhere($requestData);

            $auth_group_count = Db::table('auth_group')
                ->where($map)
                ->count();

            $auth_group_res = Db::table('auth_group')
                -> select(
                    'auth_group.id',
                    'auth_group.title',
                    'auth_group.describe',
                    'auth_group.status',
                    Db::raw('COUNT(auth_group_access.group_id) AS `number`')
                )
                -> leftJoin('auth_group_access', 'auth_group.id', '=', 'auth_group_access.group_id')
                -> where($map)
                -> groupBy('auth_group.id')
                -> offset(getOffset($requestData))
                -> limit($requestData['limit'])
                -> get()
                -> toArray();

            foreach ($auth_group_res as &$v) {
                switch ($v['status']) {
                    case 1:
                        $v['status'] = '正常';
                        break;
                    case 0:
                        $v['status'] = '禁用';
                        break;
                    default:
                        $v['status'] = '正常';
                }
            }

            $data['code'] = 0;
            $data['count'] = $auth_group_count;
            $data['data'] = $auth_group_res;

            return $this->response->json($data);
        } else {
            //需要显示的按钮
            $btn = [
                'top'=>[
                    [
                        'url' => '/auth/Group/add',
                        'event' => 'add',
                        'name' => '<i class="layui-icon layui-icon-add-1"></i>添加用户组',
                        'style' => ''
                    ],
                ],
                'table'=>[
                    [
                        'url' => '/auth/Group/look',
                        'event' => 'look',
                        'name' => '查看用户',
                        'style' => 'layui-btn-primary',
                    ],
                    [
                        'url' => '/auth/Group/edit',
                        'event' => 'edit',
                        'name' => '编辑',
                        'style' => '',
                    ],

                    [
                        'url' => '/auth/Group/allot',
                        'event' => 'allot',
                        'name' => '授权',
                        'style' => 'layui-btn-normal',
                    ],
                    [
                        'url' => '/auth/Group/del',
                        'event' => 'del',
                        'name' => '删除',
                        'style' => 'layui-btn-danger',
                    ]
                ]
            ];
            //判断显示按钮是否有权限

            $data['btn'] = btnShow($btn, $this->session);

            return $render->render('auth/group/showList',$data);

        }
    }


    /**
     * ##添加##
     */
    public function add(RenderInterface $render){

        if ($this->request -> isMethod('post')){

            $data = $this->request->all();
            $validated  = $this->groupRequest->validated();
            $data = array_merge($data,$validated);

            //检测是否存在，存在就提示失败
            $count_auth_group =  Db::table('auth_group')
                -> where([
                    'title' => $data['title']
                ])
                -> count();

            if ($count_auth_group > 0){
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.add_error')]);
            }

            //插入新数据
            $insert = Db::table('auth_group')
                -> insert($data);

            if ($insert) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.add_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.add_error')]);
            }

        }else{
            return $render->render('auth/group/add');
        }
    }


    /**
     * ##修改##/
     */
    public function edit(RenderInterface $render){

        if ($this->request -> isMethod('post')){

            $data = $this->request->all();
            $validated  = $this->groupRequest->validated();
            $data = array_merge($data,$validated);

            //检测是否存在，存在就提示失败
            $count_auth_group = Db::table('auth_group')
                -> where([
                    ['title','=',$data['title']],
                    ['id','<>',$data['id']]
                ])
                -> count();

            if ($count_auth_group > 0){
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.edit_error')]);
            }


            //修改
            $res_user = Db::table('auth_group')
                -> where([
                    'id' => $data['id']
                ])
                -> update([
                    'title' => $data['title'],
                    'describe' => $data['describe'],
                    'status' => $data['status']
                ]);

            if ($res_user) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.edit_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.edit_error')]);
            }
        }else{

            $id = $this->request->input('id');

            $res = Db::table('auth_group')
                -> where([
                    ['id', '=', $id]
                ])
                -> first();

            return $render->render('auth/group/edit', ['info' => $res]);
        }
    }


    /**
     * ##删除##
     */
    public function del(){

        $id = $this->request->input('id');


        //检查该用户组下有没有用户，如果有就提示禁止删除
        $count_auth_group_access = Db::table('auth_group_access')
            -> where('group_id',$id)
            -> count();
        if ($count_auth_group_access > 0){
            return $this->response->json(['code' => 1, 'msg' => trans('common.alert.del_error')]);
        }

        //删除用户组
        $delete = Db::table('auth_group')
            ->where('id', $id)
            ->delete();
        if ($delete) {
            return $this->response->json(['code' => 0, 'msg' => trans('common.alert.del_success')]);
        } else {
            return $this->response->json(['code' => 1, 'msg' => trans('common.alert.del_error')]);
        }
    }


    /**
     * ##查看用户##
     */
    public function look(RenderInterface $render){

        $id = $this->request->input('id');

        //获取用户信息
        $res_auth_group_access = Db::table('auth_group_access')
            -> select(
                'admin.id',
                'admin.username',
                'admin.fullname',
                'admin.status',
                'auth_group_access.group_id'
            )
            -> join('admin','auth_group_access.uid','=','admin.id')
            -> where(
                ['auth_group_access.group_id','=',$id]
            )
            -> get()
            -> toArray();

        //展示信息处理
        foreach ($res_auth_group_access as &$v){
            switch ($v['status']) {
                case 0:
                    $v['status'] = '禁用';
                    break;
                case 1:
                    $v['status'] = '激活';
                    break;
                default:
                    $v['status'] = '未知';
            }
        }

        return $render->render('auth/group/look', ['info' => $res_auth_group_access]);

    }


    /**
     * ##解除用户在用户组##
     */
    public function removeUser(){

        $id = $this->request->input('id');
        $group_id = $this->request->input('group_id');


        $delete = Db::table('auth_group_access')
            ->where(            [
                ['uid','=',$id],
                ['group_id','=',$group_id]
            ])
            ->delete();


        if ($delete) {
            return $this->response->json(['code' => 0, 'msg' => trans('common.alert.del_success')]);
        } else {
            return $this->response->json(['code' => 1, 'msg' => trans('common.alert.del_error')]);
        }
    }


    /**
     * ##授权##
     */
    public function allot(RenderInterface $render){

        if ($this->request -> isMethod('post')){
            $data = $this->request->all();

            //授权
            $res_allot = $this->groupModel -> allot($data['id'], $data['rule']);
            if ($res_allot['code'] == 0){
                return $this->response->json(['code' => 0, 'msg' => trans('common.group_alert.allot_success')]);
            }else{
                return $this->response->json(['code' => 1, 'msg' => trans('common.group_alert.allot_error')]);
            }
        }else{

            $id = $this->request->input('id');
            //获取规则列表
            $info = $this->groupModel -> getRuleGroup($id);

            return $render->render('auth/group/allot', ['info' => json_encode($info['data'], JSON_UNESCAPED_UNICODE ), 'id' => $id]);
        }

    }

}
