<?php


namespace App\Controller;


use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

/**
 * Class HomeController
 * @package App\Controller
 * @AutoController()
 */
class HomeController
{
    public function console(RenderInterface $render)
    {
        return $render->render('home/console');
    }
}