<?php


namespace App\Controller\Admin\auth;

use App\Controller\BaseController;
use App\Request\RuleRequest;
use Hyperf\DbConnection\Db;
use Hyperf\View\RenderInterface;

/**
 * ##权限管理-规则管理##
 * Class RuleController
 * @package App\Controller\Admin\auth
 */
class RuleController extends BaseController
{
    /**
     * ##^列表展示##
     * @param RenderInterface $render
     * @return mixed
     */
    public function showList(RenderInterface $render)
    {
        if ($this->request->getHeader("X-Requested-With")) {
            $requestData = $this->request -> all();
            $map = createSearchWhere($requestData);

            $data_count = Db::table('auth_rule')
                ->where($map)
                ->count();

            $data_res = Db::table('auth_rule')
                -> join('auth_rule_parent', 'auth_rule.p_id', '=', 'auth_rule_parent.id')
                -> select('auth_rule.*', 'auth_rule_parent.title as p_title')
                -> where($map)
                -> offset(getOffset($requestData))
                -> limit($requestData['limit'])
                -> get();

            foreach ($data_res as &$v) {
                $ear = substr($v['title'], 0, 1);
                if ('^'== $ear) {
                    $v['title'] = substr($v['title'], 1, strlen($v['title']));
                    $v['level'] = '展示页';
                } else {
                    $v['level'] = '功能';
                }
                switch ($v['status']) {
                    case 1:
                        $v['status'] = '正常';
                        break;
                    case 0:
                        $v['status'] = '禁用';
                        break;
                    default:
                        $v['status'] = '未知';
                }
            }

            $data['code'] = 0;
            $data['count'] = $data_count;
            $data['data'] = $data_res;

            return $this->response->json($data);
        } else {

            //需要显示的按钮
            $btn = [
                'top'=>[
                    [
                        'url' => '/auth/Rule/ruleRefresh',
                        'event' => 'refresh',
                        'name' => '<i class="layui-icon layui-icon-refresh-1"></i>更新',
                        'style' => ''
                    ],
                ],
                'table'=>[
                    [
                        'url' => '/auth/Rule/edit',
                        'event' => 'edit',
                        'name' => '编辑',
                        'style' => '',
                    ]
                ]
            ];
            //判断显示按钮是否有权限

            $data['btn'] = btnShow($btn, $this->session);

            return $render->render('auth/rule/showList', $data);
        }
    }

    /**
     * ##规则编辑##
     */
    public function edit(RenderInterface $render)
    {
        if ($this->request->isMethod('post')) {
            $data = $this->request->all();
/*            //检测是否存在，存在就提示失败
            $check = Db::table('auth_rule')
                -> where([
                    'id' => $data['id']
                ])
                -> first();

            if ($check['status'] == $data['status'] && $check['condition'] == $data['condition']) {
                return $this->response->json(['code' => 0, 'msg' => '内容无变动']);
            }

            $update_rule = Db::table('auth_rule')
                -> where([
                    'id' => $data['id']
                ])
                -> update([
                    'status' => $data['status'],
                    'condition' => $data['condition']
                ]);
            if ($update_rule) {
                return $this->response->json(['code' => 0, 'msg' => '修改成功']);
            } else {
                return $this->response->json(['code' => 1, 'msg' => '修改失败']);
            }*/
        } else {
            $id = $this->request->input('id');

            $res_rule = Db::table('auth_rule')
                -> where([
                    'id' => $id
                ])
                -> first();
            return $render->render('auth/rule/edit',['info' => $res_rule]);

        }
    }


    /**
     * ##规则刷新##
     */
    public function ruleRefresh()
    {
        $ruleModel = new RuleModel();
        $refresh = $ruleModel->refreshRule();
        return ResponseHelper::json($response, ['code' => $refresh['code'], 'msg' => $refresh['info']]);
    }
}
