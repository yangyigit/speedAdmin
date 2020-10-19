<?php


namespace App\Controller\Admin\auth;

use App\Controller\BaseController;
use App\Model\auth\User;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
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
}
