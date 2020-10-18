<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\auth\Group;
use App\Model\Index;
use App\Model\auth\User;
use App\Request\RuleRequest;
use yangyi\hyperf\Auth;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

/**
 * Class IndexController
 * @package App\Controller
 * @AutoController()
 */
class IndexController extends BaseController
{

    /**
     * @Inject()
     * @var Auth
     */
    protected $auth;

    /**
     * @Inject()
     * @var User
     */
    protected $userModel;

    /**
     * @Inject()
     * @var Index
     */
    protected $formatMenu;

    /**
     * @Inject()
     * @var Group
     */
    protected $groupModel;

    public function index(RenderInterface $render)
    {
        $getRuleList = $this->groupModel->getRuleList(true);
        $getRuleList = $getRuleList['data'];
        if ($this->session -> get('admin_isAdmin')){
            $data['menu'] = $this->formatMenu->formatMenu($getRuleList)->cancelSign();
        }else{
            //筛选有权限的内容
            foreach ($getRuleList as $k1 => $v1) {
                foreach ($getRuleList[$k1]['children'] as $k2=>$v2) {
                    if (isset($v2['children'])) {
                        foreach ($getRuleList[$k1]['children'][$k2]['children'] as $k3 => $v3) {
                            if (!$this->auth->check([],$v3['name'], $this->userModel -> getUserId())) {
                                unset($getRuleList[$k1]['children'][$k2]['children'][$k3]);
                            }
                        }
                    } else {
                        if (!$this->auth->check([],$v2['name'], $this->userModel -> getUserId())) {
                            unset($getRuleList[$k1]['children'][$k2]);
                        }
                    }
                }
            }
            $data['menu'] = $this->formatMenu->formatMenu($getRuleList)->cancelSign();

        }

        $data['admin_fullName'] = $this->session->get('admin_fullName');

        return $render->render('index/index',$data);
    }

    public function test(){
        $resUser = Db::table('admin')->get();
        foreach ($resUser as $k=>$item) {
            $resUser[$k]['status'] = adminStatusType($item['status']);
            $resUser[$k]['is_admin'] = '管理员';
        }
       var_dump($resUser);
    }

}
