<?php


namespace App\Tools;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\SessionInterface;

class SessionTool
{

    public $sessionData = [];
    /**
     * @Inject()
     * @var SessionInterface
     */
    private $session;


    public function set($resUser){

    }
}