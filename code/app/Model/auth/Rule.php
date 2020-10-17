<?php


namespace App\Model\auth;

use App\Model\Model;
use Hyperf\DbConnection\Db;

class Rule extends Model
{
    /**
     * 更新规则按钮
     */
    public function refreshRule()
    {
        //应用地址
        $admin_dir_path = __DIR__."/../../Controller/Admin";
        //控制器路径 = 命名空间
        $controller_path = 'App\Controller\Admin\\';
        //获取一级控制器
        $dirRes = opendir($admin_dir_path);

        while (($dir = readdir($dirRes)) !== false) {
            //过滤特殊文件
            if (!in_array($dir, array('.', '..', '.svn'))) {
                if (is_dir($admin_dir_path . '/' . $dir)) {
                    //获取二级文件
                    $dir_list[] = $dir;
                } else {
                    //获取最外层控制器
                    $controller = basename($dir, '.php');
                    $path = $controller_path . $controller;
                    $document_res = (new \ReflectionClass($path))->getDocComment();
                    $document = $this->dealDocument($document_res);
                    //含规范注释的为待处理控制器
                    if (!empty($document)) {
                        $plan_list[] = [
                            'method_namespace' => $path,
                            'controller_path' => '/' . $controller . '/',
                            'controller_name' => $controller,
                            'controller_doc' => $document
                        ];
                    }
                }
            }
        }

        //获取二级控制器
        if (!empty($dir_list)) {
            foreach ($dir_list as $d_v) {
                $dirRes = opendir($admin_dir_path . '/' . $d_v);
                while (($dir = readdir($dirRes)) !== false) {
                    //过滤特殊文件
                    if (!in_array($dir, array('.', '..', '.svn'))) {
                        //获取第二层控制器
                        $controller = basename($dir, '.php');
                        $path = $controller_path . $d_v . '\\' . $controller;
                        $document_res = (new \ReflectionClass($path))->getDocComment();
                        $document = $this->dealDocument($document_res);
                        //含规范注释的为待处理控制器
                        if (!empty($document)) {
                            $plan_list[] = [
                                'method_namespace' => $path,
                                'controller_path' => '/' . $d_v . '/' . $controller . '/',
                                'controller_name' => $controller,
                                'controller_doc' => $document
                            ];
                        }
                    }
                }
            }
        }

        //类注注释不能重复
        foreach ($plan_list as $p_v) {
            $controller_doc_list[] = $p_v['controller_doc'];
        }
        $repeat_list = array_diff_assoc($controller_doc_list, array_unique($controller_doc_list));
        if (!empty($repeat_list)) {
            $info = '注释' . implode(',', $repeat_list) . '重复，请核实相应代码';
            return ['code' => 1, 'info' => $info, 'data' => []];
        }

        //生成父级表
        $id = 0;
        foreach ($plan_list as $k => $p_v) {
            $parents_data[] = [
                'id' => ++$id,
                'name' => $p_v['controller_path'],
                'title' => $p_v['controller_doc']
            ];
        }

        //遍历获取对应控制器的方法
        foreach ($plan_list as $p_v) {
            if (!empty($p_v['controller_doc'])) {
                $className = $p_v['method_namespace'];
                //获取类里面的方法
                $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $m_v) {
                    $m_v = (array)$m_v;
                    $document_res = (new \ReflectionClass($className))->getMethod($m_v['name'])->getDocComment();
                    $document = $this->dealDocument($document_res);
                    //含规范注释的为待处理方法
                    if (!empty($document)) {
                        $detail[] = [
                            'controller_name' => $p_v['controller_name'],
                            'controller_path' => $p_v['controller_path'],
                            'controller_doc' => $p_v['controller_doc'],
                            'method_name' => $m_v['name'],
                            'method_namespace' => $p_v['method_namespace'],
                            'method_path' => str_replace('Controller', '', $p_v['controller_path']). $m_v['name'],
                            'method_doc' => $document,
                        ];
                        //现在的方法列表
                        $data_rule_list[] = str_replace('Controller', '', $p_v['controller_path']). $m_v['name'];
                    }
                }
            }
        }

        //子级匹配父级
        foreach ($parents_data as $p_v) {
            foreach ($detail as &$d_v) {
                if ($p_v['name'] == $d_v['controller_path']) {
                    $d_v['parent_id'] = $p_v['id'];
                }
            }
        }
        unset($d_v);

        //获取原先的规则表数据
        $tab_rule_res = Db::table('auth_rule')
            -> orderBy('id', 'desc')
            -> get();

        if (empty($tab_rule_res)) {
            $max_id = 0;
        } else {
            $max_id = $tab_rule_res['0']['id'];
        }

        $tab_rule_list = [];
        $delete_rule_id_list = [];
        //对比现在的方法列表，获取已经取消的方法id
        foreach ($tab_rule_res as $t_v) {
            $tab_rule_list[] = $t_v['name'];
            if (!in_array($t_v['name'], $data_rule_list)) {
                $delete_rule_id_list[] = $t_v['id'];
            }
        }

        //获取没变的方法
        $repeat_rule_name_list = array_intersect($tab_rule_list, $data_rule_list);

        //获取规则表数据
        foreach ($detail as $d_v) {
            if (in_array($d_v['method_path'], $repeat_rule_name_list)) {
                foreach ($tab_rule_res as $t_v) {
                    //已经取消的方法忽略不做处理
                    if ($d_v['method_path'] == $t_v['name']) {
                        //没有改变的方法
                        $new_rule_data[] = [
                            'id' => $t_v['id'],
                            'name' => $t_v['name'],
                            'title' => $d_v['method_doc'],
                            'type' => $t_v['type'],
                            'status' => $t_v['status'],
                            'condition' => $t_v['condition'],
                            'p_id' => $d_v['parent_id']
                        ];
                    }
                }
            } else {
                //新增的方法
                $new_rule_data[] = [
                    'id' => ++$max_id,
                    'name' => $d_v['method_path'],
                    'title' => $d_v['method_doc'],
                    'type' => 1,
                    'status' => 1,
                    'condition' => '',
                    'p_id' => $d_v['parent_id']
                ];
            }
        }

        $delete_group_id_list = [];
        $new_group_data = [];
        //如果有方法删除了，用户组表数据需要修改
        if (!empty($delete_rule_id_list)) {
            $auth_group_res = Db::table('auth_group')-> get();
            foreach ($auth_group_res as $a_v) {
                $rules = explode(',', $a_v['rules']);
                $flag = false;
                foreach ($rules as $r_v) {
                    if (in_array($r_v, $delete_rule_id_list)) {
                        $flag = true;
                    }
                }
                if ($flag) {
                    $new_rules_arr = array_diff($rules, $delete_rule_id_list);
                    //有变动的数据id，需要全部删除
                    $delete_group_id_list[] = $a_v['id'];
                    //新的用户组数据
                    $new_group_data[] = [
                        'id' => $a_v['id'],
                        'title' => $a_v['title'],
                        'describe' => $a_v['describe'],
                        'status' => $a_v['status'],
                        'rules' => implode(',', $new_rules_arr)
                    ];
                }
            }
        }
        if (count($delete_group_id_list) !== count($new_group_data)) {
            return ['code' => 1, 'info' => '用户组表变更数据获取有误', 'data' => []];
        }

        // 启动事务
        Db::beginTransaction();
        try {
            //清空父级表
            Db::table('auth_rule_parent')->delete();
            //父级表插入新数据
            Db::table('auth_rule_parent')->insert($parents_data);
            //清空规则表
            Db::table('auth_rule')->delete();
            //规则表插入新数据
            Db::table('auth_rule')->insert($new_rule_data);
            if (!empty($delete_rule_id_list) && !empty($delete_group_id_list) && !empty($new_group_data)) {
                //删除用户组变动的数据
                Db::table('auth_group')->whereIn('id', $delete_group_id_list)->delete();
                //变动数据新增
                Db::table('auth_group')->insert($new_group_data);
            }
            // 提交事务
            Db::commit();
            return ['code' => 0, 'info' => '更新成功', 'data' => []];
        } catch (\Throwable $ex) {
            // 回滚事务
            Db::rollback();
            return ['code' => 1, 'info' => '请求失败，原因：' . $ex->getMessage(), 'data' => []];
        }
    }

    /**
     * 注释处理
     * @param $document
     * @return string
     */
    private function dealDocument($document)
    {
        $flag = '##';
        //##第一次出现位置
        $start = mb_strpos($document, $flag);
        //##最后一次出现位置
        $end = mb_strrpos($document, $flag);
        //需要截取的长度
        $length = $end - $start;
        if ($start && $end && $start !== $end) {
            $return_data = mb_substr($document, $start + 2, $length - 2);
        } else {
            $return_data = '';
        }
        return $return_data;
    }
}
