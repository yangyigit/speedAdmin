<?php

declare (strict_types=1);
namespace App\Model\auth;


use Hyperf\DbConnection\Db;

class Group
{
    /**
     * 获取规则列表
     * @param bool $menu 是否是目录
     * @return array
     */
    public function getRuleList($menu = false){
        if ($menu == true){
            $where[] = ['auth_rule.title', 'like', '^%'];
        }else{
            $where = [];
        }

        $res_auth_rule = Db::table('auth_rule')
            -> join('auth_rule_parent', 'auth_rule_parent.id', '=', 'auth_rule.p_id')
            -> select(
                'auth_rule.id',
                'auth_rule.name',
                'auth_rule.title',
                'auth_rule.type',
                'auth_rule.status',
                'auth_rule.condition',
                'auth_rule_parent.title as parent_title'
            )
            -> where($where)
            -> get();

        $role_list = [];
        foreach ($res_auth_rule as $k_auth_rule => $v_auth_rule){
            $tmp = [];
            if(strpos($v_auth_rule['parent_title'], '-') == false){
                //有一级权限
                $has = false;
                foreach ($role_list as &$v_role_list){
                    if($v_auth_rule['parent_title'] == $v_role_list['title']){
                        $has = true;
                        $v_role_list['children'][] = [
                            'id' => $v_auth_rule['id'],
                            'title' => $v_auth_rule['title'],
                            'name' => $v_auth_rule['name'],
                            'type' => $v_auth_rule['type'],
                            'status' => $v_auth_rule['status'],
                            'condition' => $v_auth_rule['condition'],
                        ];
                    }
                }
                if ($has == false){
                    $tmp['id'] = 'x'.$k_auth_rule;
                    $tmp['title'] = $v_auth_rule['parent_title'];
                    $tmp['children'][] = [
                        'id' => $v_auth_rule['id'],
                        'title' => $v_auth_rule['title'],
                        'name' => $v_auth_rule['name'],
                        'type' => $v_auth_rule['type'],
                        'status' => $v_auth_rule['status'],
                        'condition' => $v_auth_rule['condition'],
                    ];
                    $role_list[] = $tmp;
                }
            }else{
                //有两级权限
                $parent_name = explode('-', $v_auth_rule['parent_title']);
                $has_one = false;
                $has_two = false;
                foreach ($role_list as &$v_role_list){
                    if($parent_name[0] == $v_role_list['title']){
                        $has_one = true;
                        foreach ($v_role_list['children'] as &$v_role_list_children){
                            if($parent_name[1] == $v_role_list_children['title']){
                                $has_two = true;
                                $v_role_list_children['children'][] = [
                                    'id' => $v_auth_rule['id'],
                                    'title' => $v_auth_rule['title'],
                                    'name' => $v_auth_rule['name'],
                                    'type' => $v_auth_rule['type'],
                                    'status' => $v_auth_rule['status'],
                                    'condition' => $v_auth_rule['condition'],
                                ];
                            }
                        }
                        if(!$has_two){
                            $v_role_list['children'][] = [
                                'id' => 'xx'.$k_auth_rule,
                                'title' => $parent_name[1],
                                'name' => $v_auth_rule['name'],
                                'children' => [
                                    [
                                        'id' => $v_auth_rule['id'],
                                        'title' => $v_auth_rule['title'],
                                        'name' => $v_auth_rule['name'],
                                        'type' => $v_auth_rule['type'],
                                        'status' => $v_auth_rule['status'],
                                        'condition' => $v_auth_rule['condition'],
                                    ]
                                ]
                            ];
                        }
                    }
                }

                if(!$has_one && !$has_two){
                    $tmp['id'] = 'x'.$k_auth_rule;
                    $tmp['title'] = $parent_name[0];
                    $tmp['children'][] = [
                        'id' => 'xx'.$k_auth_rule,
                        'name' => $v_auth_rule['name'],
                        'title' => $parent_name[1],
                        'children' => [
                            [
                                'id' => $v_auth_rule['id'],
                                'title' => $v_auth_rule['title'],
                                'name' => $v_auth_rule['name'],
                                'type' => $v_auth_rule['type'],
                                'status' => $v_auth_rule['status'],
                                'condition' => $v_auth_rule['condition'],
                            ]
                        ]
                    ];
                    $role_list[] = $tmp;
                }
            }
        }

        return ['code' => 0, 'msg' => '获取成功', 'data' => $role_list];
    }


    /**
     * 获取用户组相关的规则
     * @param $id 用户组id
     * @return array
     */
    public function getRuleGroup($id){
        $rule_list = $this -> getRuleList();
        $rule_list = $rule_list['data'];

        $res_auth_group = Db::table('auth_group')
            -> select('rules')
            -> where([
                ['id','=',$id]
            ])
            -> first();

        $rules = explode(',', $res_auth_group['rules']);

        foreach ($rule_list as &$v_rule_list){
            foreach ($v_rule_list['children'] as &$vv_rule_list){
                if(isset($vv_rule_list['children'])){
                    //二级分组
                    foreach ($vv_rule_list['children'] as &$vvv_rule_list){
                        if (in_array($vvv_rule_list['id'], $rules)){
                            $vvv_rule_list['checked'] = true;
                        }
                    }
                }else{
                    //一级分组
                    if (in_array($vv_rule_list['id'], $rules)){
                        $vv_rule_list['checked'] = true;
                    }
                }
            }
        }

        return ['code' => 0, 'msg' => '获取成功', 'data' => $rule_list];
    }


    /**
     * 分配权限
     * @param $id 用户组id
     * @param $data 需要分配的权限json
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function allot($id, $data){
        $rule_list = json_decode($data, true);

        $ids = [];
        foreach ($rule_list as $v_rule_list){
            foreach ($v_rule_list['children'] as $vv_rule_list){
                if(isset($vv_rule_list['children'])){
                    //二级分组
                    foreach ($vv_rule_list['children'] as &$vvv_rule_list){
                        $ids[] = $vvv_rule_list['id'];
                    }
                }else{
                    $ids[] = $vv_rule_list['id'];
                }
            }
        }

        $update_data = [
            'rules' => implode(',', $ids)
        ];
        $res_auth_group = Db::table('auth_group')->where('id', $id)->update($update_data);
        if ($res_auth_group){
            return ['code' => 0, 'msg' => '修改成功'];
        }else{
            return ['code' => 1, 'msg' => '修改失败'];
        }
    }




}