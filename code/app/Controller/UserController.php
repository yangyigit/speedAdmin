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

use App\Request\UserRequest;
use EasySwoole\VerifyCode\Conf;
use EasySwoole\VerifyCode\VerifyCode;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

/**
 * Class IndexController
 * @package App\Controller
 * @AutoController()
 */
class UserController extends AbstractController
{

    public function login(RenderInterface $render, UserRequest $userRequest)
    {
        if($this->request->isMethod('post')){
            var_dump($validated = $userRequest->validated());
        }else{
            return $render->render('user/login');
        }
    }

    public function verifyCode(){
        $conf = new Conf();
        // 开启或关闭混淆曲线
        $conf->setUseCurve();
        // 开启或关闭混淆噪点
        $conf->setUseNoise();

        $code = mt_rand(1111,9999);
        $this->session->set('verify_code', $code);
        $vcode = new VerifyCode($code);
        $codeStr = $vcode->DrawCode($code)->getImageByte();
        return $codeStr;
    }

}
