<?php


namespace App\Controller\Admin\auth;

use App\Controller\BaseController;
use App\Model\auth\User;
use App\Request\AdminRequest;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;

/**
 * ##权限管理-用户##
 * Class UserController
 * @package App\Controller\Admin\auth
 */
class UserController extends BaseController
{
    /**
     * @Inject()
     * @var User
     */
    protected $userModel;

    /**
     * @Inject()
     * @var AdminRequest
     */
    protected $adminRequest;



    /**
     * ##^用户列表##
     * @return int
     */
    public function showList(RenderInterface $render)
    {

        if (isAjax($this->request)) {
            $requestData = $this->request -> all();
            $map = createSearchWhere($requestData);


            $countUser = Db::table('admin')
                ->where($map)
                ->count();

            $resUser = Db::table('admin')
                ->where($map)
                ->offset(getOffset($requestData))
                ->limit($requestData['limit'])
                ->get()
                ->toArray();

            foreach ($resUser as $k => $v) {

                $resUser[$k]['group'] = implode(' | ', $this->userModel->getGroupName($v['id']));
                $resUser[$k]['status'] = adminStatusType($v['status']);
            }

            $data['code'] = 0;
            $data['count'] = $countUser;
            $data['data'] = $resUser;

            return $this->response->json($data);
        } else {
            //需要显示的按钮
            $btn = [
                'top'=>[
                    [
                        'url' => '/auth/User/add',
                        'event' => 'add',
                        'name' => '<i class="layui-icon layui-icon-add-1"></i>添加用户',
                        'style' => '',
                    ],
                ],
                'table'=>[
                    [
                        'url' => '/auth/User/look',
                        'event' => 'detail',
                        'name' => '查看',
                        'style' => 'layui-btn-primary',
                    ],
                    [
                        'url' => '/auth/User/edit',
                        'event' => 'edit',
                        'name' => '编辑',
                        'style' => '',
                    ],

                    [
                        'url' => '/auth/User/allot',
                        'event' => 'allot',
                        'name' => '分配用户组',
                        'style' => 'layui-btn-normal',
                    ],
                    [
                        'url' => '/auth/User/del',
                        'event' => 'del',
                        'name' => '删除',
                        'style' => 'layui-btn-danger',
                    ]
                ]
            ];
            //判断显示按钮是否有权限

            $data['btn'] = btnShow($btn, $this->session);

            return $render->render('auth/user/showList',$data);
        }
    }

    /**
     * ##添加##
     * @return int
     */
    public function add(RenderInterface $render){
        if ($this->request->isMethod('post')){
            $data = $this->request->all();
            $validated  = $this->adminRequest->validated();
            $data = array_merge($data,$validated);
            $data['password'] = md5($data['password']);
            $data['create_time'] = date('Y-m-d H:i:s');

            $resUser = Db::table('admin')
                ->insert($data);

            if ($resUser) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.add_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.add_error')]);
            }
        }else{
            return $render->render('auth/user/add');
        }
    }

    /**
     * ##查看##
     * @return mixed
     */
    public function look(RenderInterface $render)
    {
        $id = $this->request->input('id');

        $resUser = $this->userModel->look($id);

        return $render->render('auth/user/look', ['info' => $resUser]);
    }


    /**
     * ##编辑##
     * @return mixed
     */
    public function edit(RenderInterface $render)
    {
        if ($this->request -> isMethod('post')){
            $data = $this->request->all();
            $validated  = $this->adminRequest->validated();
            $data = array_merge($data,$validated);

            $data['update_time'] = date('Y-m-d H:i:s');
            if ($data['password'] != '****') {
                $data['password'] = md5($data['password']);
            }else{
                unset($data['password']);
            }

            $resUser = Db::table('admin')
                ->where('id',$data['id'])
                ->update($data);

            if ($resUser) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.edit_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.edit_error')]);
            }
        }else{
            $id = $this->request->input('id');

            $resUser = $this->userModel -> look($id);

        return $render->render('auth/user/edit', ['info' => $resUser]);
        }
    }

    /**
     * ##删除##
     */
    public function del()
    {
        $id = $this->request->input('id');

        Db::beginTransaction();
        //删除用户信息
        try {
            $resUser = Db::table('admin')
                ->where('id', '=', $id)
                ->delete();

            //删除用户对应角色的信息
            Db::table('auth_group_access')
                ->where('uid', '=', $id)
                ->delete();
        }catch (\Exception $e) {
            Db::rollBack();

            return $this->response->json(['code' => 1, 'msg' => trans('common.alert.del_error')]);
        }

        if ($resUser){
            Db::commit();

            return $this->response->json(['code' => 0, 'msg' => trans('common.alert.del_success')]);
        }else{
            Db::rollBack();

            return $this->response->json(['code' => 1, 'msg' => trans('common.alert.del_error')]);
        }
    }


    /**
     * ##分配##
     * @param ServerRequest $request
     * @return mixed
     */
    public function allot(RenderInterface $render)
    {
        if ($this->request -> isMethod('post')){
            $data = $this->request->all();

            if (empty($data['uid'])){
                return $this->response->json(['code' => 1, 'msg' => trans('common.user_alert.user_choose_error')]);
            }

            $roleData = array();
            if (isset($data['id'])){
                foreach ($data['id'] as $k => $v) {
                    $roleData[] = $v;
                }
            }

            //分配角色
            $resUserRole = Db::table('auth_group_access')
                ->where('uid', '=', $data['uid'])
                ->delete();

            if (empty($roleData)){
                $resUser = true;
            }else{
                $dataGather = [];
                foreach ($roleData as $k=>$v) {
                    $tmp['uid'] = $data['uid'];
                    $tmp['group_id'] = $v;
                    $dataGather[] = $tmp;
                }

                $resUser = Db::table('auth_group_access')
                    ->insert($dataGather);
            }

            if ($resUserRole && $resUser) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.user_alert.allot_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.user_alert.allot_error')]);
            }
        }else{

            $id = $this->request->input('id');


            $resGroup = $this->userModel->getGroup($id);

            return $render->render('auth/user/allot', ['resgroup' => $resGroup, 'uid' => $id]);

        }
    }
}
