<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Contract\SessionInterface;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Di\Annotation\Inject;

/**
 * @property int $id 
 * @property int $is_admin 
 * @property string $username 
 * @property string $fullname 
 * @property string $password 
 * @property int $status 
 * @property string $last_login_time 
 * @property string $create_time 
 * @property string $update_time 
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'is_admin' => 'integer', 'status' => 'integer'];

    /**
     * @Inject()
     * @var SessionInterface
     */
    private $session;
    /**
     * 记录用户状态
     * @param $resUser 用户信息
     * @param $session session对象
     */
    public function writeStatus($resUser)
    {
        $this->session->set('admin_userId', $resUser->id);
        $this->session->set('admin_userName', $resUser->username);
        $this->session->set('admin_fullName', $resUser->fullname);
        $this->session->set('admin_isAdmin', $resUser->is_admin);
    }
}