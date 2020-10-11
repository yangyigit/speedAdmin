<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Model\auth\User;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class GlobalMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @Inject()
     * @var User
     */
    protected $userModel;

    public function __construct(ContainerInterface $container, HttpResponse $response)
    {
        $this->container = $container;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //检查用户是否登录
        $resCheckLoginUser = $this -> checkLoginUser($request);
        if (!$resCheckLoginUser){
            return $this->response->redirect('admin/user/login');
        }

        return $handler->handle($request);
    }

    /**
     * 检查用户是否登录
     * @param $request
     * @return bool
     */
    private function checkLoginUser($request){
        //去除不需要检测的url
        $params = $request -> getServerParams();
        if(in_array($params['path_info'], config('app.no_login_url'))){
            return true;
        }

        $userId = $this->userModel->getUserId();

        if (empty($userId)) {
            return false;
        }else{
            return true;
        }
    }
}