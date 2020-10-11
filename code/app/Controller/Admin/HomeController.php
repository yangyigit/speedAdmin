<?php


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

/**
 * Class HomeController
 * @package App\Controller
 * @AutoController()
 */
class HomeController extends BaseController
{
    public function console(RenderInterface $render)
    {
        return $render->render('home/console');
    }
}