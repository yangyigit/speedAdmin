# 按钮权限设置
>可以为按钮是否显示、是否可以有权限显示提供便捷构建

### 使用介绍

>本操作分为控制器层和视图层两个部分

#### 控制器层:

在需要显示按钮的方法下,通过'url','event','name','style'。组成的二维数组来控制，btnShow()方法会自动过滤没有权限的按钮，代码如下:

```
    //需要显示的按钮
    $btn = [
        'top'=>[
            [
                'url' => '/auth/User/add',
                'event' => 'add',
                'name' => '<i class="layui-icon layui-icon-add-1"></i>添加用户',
                'style' => '',
            ],
        ],
        'table'=>[
            [
                'url' => '/auth/User/look',
                'event' => 'detail',
                'name' => '查看',
                'style' => 'layui-btn-primary',
            ],
            [
                'url' => '/auth/User/edit',
                'event' => 'edit',
                'name' => '编辑',
                'style' => '',
            ],

            [
                'url' => '/auth/User/allot',
                'event' => 'allot',
                'name' => '分配用户组',
                'style' => 'layui-btn-normal',
            ],
            [
                'url' => '/auth/User/del',
                'event' => 'del',
                'name' => '删除',
                'style' => 'layui-btn-danger',
            ]
        ]
    ];
    //判断显示按钮是否有权限

    $data['btn'] = btnShow($btn, $this->session);
```
- url:按钮调整路径，要和规则路径一一对应，注意要以斜线(/)开头
- event:按钮触发事件名称
- name:按钮显示名称
- style：按钮样式

#### 视图层:

通过volist把按钮渲染到相应的位置，注意：name的渲染要使用raw将其中的中的HTMl内容展示出来。代码如下:

```
<script type="text/html" id="test-table-operate-barDemo">
      {volist name="btn.table" id="voo"}
            <a class="layui-btn {$voo.style} layui-btn-xs" lay-event="{$voo.event}">{$voo.name|raw}</a>
      {/volist}
</script>

 <script type="text/html" id="toolbar">
      {volist name="btn.top" id="voo"}
            <button class="layui-btn layui-btn-sm {$voo.style}" lay-event="{$voo.event}">{$voo.name|raw}</button>
      {/volist}
</script>
```
