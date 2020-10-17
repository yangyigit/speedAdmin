<?php


namespace App\Controller\Admin\auth;

use App\Controller\BaseController;
use App\Request\RuleRequest;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;

/**
 * ##权限管理-规则管理##
 * Class RuleController
 * @package App\Controller\Admin\auth
 */
class RuleController extends BaseController
{

    /**
     * @Inject()
     * @var RuleRequest
     */
    protected $ruleRequest;
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
                        $v['status'] = trans('common.status.normal');
                        break;
                    case 0:
                        $v['status'] = trans('common.status.disable');
                        break;
                    default:
                        $v['status'] = trans('common.status.unknown');
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
                        'name' => '<i class="layui-icon layui-icon-refresh-1"></i>'.trans('common.btn_name.update'),
                        'style' => ''
                    ],
                ],
                'table'=>[
                    [
                        'url' => '/auth/Rule/edit',
                        'event' => 'edit',
                        'name' => trans('common.btn_name.edit'),
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
            $validated  = $this->ruleRequest->validated();
            $data = array_merge($data,$validated);
            //检测是否存在，存在就提示失败
            $check = Db::table('auth_rule')
                -> where([
                    'id' => $data['id']
                ])
                -> first();

            if ($check['status'] == $data['status'] && $check['condition'] == $data['condition']) {
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.content_variable')]);
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
                return $this->response->json(['code' => 0, 'msg' => trans('common.alert.edit_success')]);
            } else {
                return $this->response->json(['code' => 1, 'msg' => trans('common.alert.edit_error')]);
            }
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
