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
use App\Tools\Auth;
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
    protected $indexModel;

    /**
     * @Inject()
     * @var Group
     */
    protected $groupModel;

    public function index(RenderInterface $render)
    {
        /*$getRuleList = $this->groupModel->getRuleList(true);
        var_dump($getRuleList);*/
        return $render->render('index/index');
    }

    public function test(){
        var_dump(config('databases.default.prefix','kong'));
    }

}
