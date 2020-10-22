<?php

declare (strict_types=1);
namespace App\Model\auth;

use Hyperf\Contract\SessionInterface;
use Hyperf\DbConnection\Db;
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
        $this->session->set('admin_userId', $resUser['id']);
        $this->session->set('admin_userName', $resUser['username']);
        $this->session->set('admin_fullName', $resUser['fullname']);
        $this->session->set('admin_isAdmin', $resUser['is_admin']);
    }

    /**
     * 获取用户id
     * @param $session session对象
     * @return mixed
     */
    public function getUserId(){
        return $this->session->get('admin_userId');
    }

    public function getGroup($uid = 0){

        $resRole = Db::table('auth_group')
            -> orderBy('id', 'asc')
            -> get()
            -> toArray();

        $userRoleData = array();

        if (!empty($uid)) {
            $resUserRole = Db::table('auth_group_access')
                -> select('group_id')
                -> where([
                    ['uid', '=', $uid]
                ])
                -> get()
                -> toArray();

            foreach ($resUserRole as $v) {
                $userRoleData[] = $v['group_id'];
            }
        }

        foreach ($resRole as $k=>$v) {
            $resRole[$k]['status'] = groupStatusType($v['status']);

            $resRole[$k]['checked'] = 0;
            if (in_array($v['id'], $userRoleData)) {
                $resRole[$k]['checked'] = 1;
            }
        }
        return $resRole;
    }

    /**
     * 获取分组名称
     * @param $uid
     * @return array
     */
    public function getGroupName($uid)
    {
        $return = array();

        if (!empty($uid)) {
            $where[] = ['uid', '=', $uid];

            $groupName = Db::table('auth_group_access')
                -> leftJoin('auth_group', 'auth_group_access.group_id', '=', 'auth_group.id')
                -> select('auth_group.title')
                -> where($where)
                -> get();

            if (!empty($groupName)) {
                foreach ($groupName as $k=>$v) {
                    $return[] = $v['title'];
                }
                return $return;
            }else{
                return $return;
            }


        }
    }

    /**
     * 获取用户信息
     * @param $id 用户id
     * @return mixed
     */
    public function look($id){

        $resUser = Db::table('admin')
            -> where([
                ['id', '=', $id]
            ])
            -> first();

        $resUser['status'] = adminStatusType($resUser['status']);
        $resUser['is_admin'] = isAdminStatusType($resUser['is_admin']);

        return $resUser;
    }

}