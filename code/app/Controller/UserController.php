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
namespace App\Controller;

use App\Model\User;
use App\Request\UserRequest;
use EasySwoole\VerifyCode\Conf;
use EasySwoole\VerifyCode\VerifyCode;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

/**
 * Class IndexController
 * @package App\Controller
 * @AutoController()
 */
class UserController extends AbstractController
{
    /**
     * @Inject()
     * @var UserRequest
     */
    protected $userRequest;

    /**
     * @Inject()
     * @var User
     */
    protected $userModel;

    public function login(RenderInterface $render)
    {
        if($this->request->isMethod('post')){
            $validated = $this->userRequest->validated();
            if($validated) {
                $verify_code = $this->session->get('verify_code');
                if (empty($verify_code) || ($validated['vercode'] != $verify_code)){
                    $this->session->forget('verify_code');
                    return ['code' => 1, 'msg' => trans('common.alert.verify_code_error')];
                }

                $res_user = Db::table('admin')
                    ->where([
                        ['username', '=', $validated['username']],
                        ['password', '=', md5($validated['password'])],
                        ['status', '=', 1],
                    ])
                    ->first();

                if (!empty($res_user)) {
                    $update_user = Db::table('admin')
                        ->where('username',$validated['username'])
                        ->update(['last_login_time' => date('Y-m-d H:i:s')]);
                    if ($update_user) {
                        $this->userModel->writeStatus($res_user);
                        return ['code' => 0, 'msg' => trans('common.alert.login_success'), 'url' => '/'];
                    }else {
                        return ['code' => 1, 'msg' => trans('common.alert.login_error')];
                    }
                }

                return ['code' => 1, 'msg' => trans('common.alert.pw_name_error')];
            }
        }else{
            $userId = $this->userModel->getUserId();
            if (!empty($userId)) {
                return $this->response->redirect('/');
            }
            return $render->render('user/login');
        }
    }

    /**
     * 退出
     * @param ServerRequest $request
     * @param Response $response
     * @return Response
     */
    public function logout()
    {
        $this->session->clear();
        return $this->response->redirect('/user/login');
    }

    public function verifyCode(){
        $conf = new Conf();
        // 开启或关闭混淆曲线
        $conf->setUseCurve();
        // 开启或关闭混淆噪点
        $conf->setUseNoise();

        $code = mt_rand(1111,9999);
        $this->session->set('verify_code', $code);
        $this->sessionContainer = $this->session->get('verify_code');
        $vcode = new VerifyCode($code);
        $codeStr = $vcode->DrawCode($code)->getImageByte();
        return $codeStr;
    }

}
