<?php


namespace App\Model;


class Index
{
    protected $getRuleList = '';

    /**
     * 格式化菜单
     * @param array $getRuleList
     * @return array
     */
    public function formatMenu(array $getRuleList)
    {
        foreach ($getRuleList as $k1=>$v1) {
            foreach ($getRuleList[$k1]['children'] as $k2=>$v2) {
                if(isset($v2['children'])) {
                    //去除取不到^符号的值
                    foreach ($getRuleList[$k1]['children'][$k2]['children'] as $k3 => $v3) {
                        if(mb_substr($v3['title'],0,1) !== '^'){
                            unset($getRuleList[$k1]['children'][$k2]['children'][$k3]);
                        }
                    }
                    //去除三级为空的内容
                    if(empty($getRuleList[$k1]['children'][$k2]['children'])){
                        unset($getRuleList[$k1]['children'][$k2]);
                    }
                }else{
                    //去除取不到^符号的值
                    if(mb_substr($v2['title'],0,1) !== '^'){
                        unset($getRuleList[$k1]['children'][$k2]);
                    }
                }
            }
            //去除二级为空的内容
            if(empty($getRuleList[$k1]['children'])){
                unset($getRuleList[$k1]);
            }
        }
        $this->getRuleList = $getRuleList;

        return $this;
    }

    /**
     * Notes:去掉特殊符号
     * Method: cancelSign
     * Auth: yangyi
     * @return string
     */
    public function cancelSign(){
        foreach ($this->getRuleList as $k1=>$v1) {
            if(isset($v1['children'])) {
                foreach ($this->getRuleList[$k1]['children'] as $k2 => $v2) {
                    if(!empty($v2['children'])){
                        foreach ($this->getRuleList[$k1]['children'][$k2]['children'] as $k3=>$v3){
                            $this->getRuleList[$k1]['children'][$k2]['children'][$k3]['title'] = mb_substr($v3['title'],1);
                        }
                    }else{
                        $this->getRuleList[$k1]['children'][$k2]['title'] = mb_substr($v2['title'],1);
                    }
                }
            }else{
                $this->getRuleList[$k1]['title'] = mb_substr($v1['title'],1);
            }
        }
        return $this->getRuleList;
    }
}