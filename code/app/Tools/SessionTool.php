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


    public function set($k,$v){
        $this->session->set($k,$v);
        $this->sessionData = $this->session->all();
        return;
    }

    public function get($k){
        return $this->sessionData[$k];
    }

    public function getAll(){
        return $this->sessionData;
    }

    public function clear(){
        unset($this->sessionData);
    }
}