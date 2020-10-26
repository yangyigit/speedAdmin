<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">#explain_web#</div>
                <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                    <div class="layui-form-item">
                        #search#
                        <div class="layui-inline">
                            <button class="layui-btn layuiadmin-btn-useradmin" lay-submit="" lay-filter="LAY-user-front-search">
                                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="test-table-page" lay-filter="test-table-page"></table>
                    <script type="text/html" id="test-table-operate-barDemo">
                        {volist name="btn.table" id="voo"}
                            <a class="layui-btn {$voo.style} layui-btn-xs" lay-event="{$voo.event}">{$voo.name|raw}</a>
                        {/volist}
                    </script>
                    <script type="text/html" id="toolbar">
                        {volist name="btn.top" id="voo"}
                            <button class="layui-btn layui-btn-sm {$voo.style}" lay-event="{$voo.event}">
                                {$voo.name|raw}
                            </button>
                        {/volist}
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'form'], function () {
        let admin = layui.admin
            , form = layui.form
            ,layer = layui.layer
            , table = layui.table;

        //监听搜索
        form.on('submit(LAY-user-front-search)', function(data){
            let field = data.field;

            //执行重载
            table.reload('test-table-page', {
                where: field,
                page: {curr: 1}
            });
        });

        table.render({
            elem: '#test-table-page'
            , url: 'showList'
            , toolbar:"#toolbar"
            , limit: 10
            , limits: [10,20,40,80,160,500,1000,5000,10000]
            , defaultToolbar: ['filter', 'print', 'exports']
            , cols: [[
            #fields#
                {width: 150, align: 'center', fixed: 'right', toolbar: '#test-table-operate-barDemo'}
            ]],
            page: true
        });

        //监听工具栏
        table.on('toolbar(test-table-page)', function (obj) {
            if (obj.event === 'add') {
                layer.open({
                    type: 2,
                    title: '添加#explain_web#',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['100%', '100%'],
                    content: "add",
                    cancel: function(){
                        table.reload('test-table-page');
                    }
                });
            }
        })

        //监听工具条
        table.on('tool(test-table-page)', function (obj) {
            let data = obj.data;
            if (obj.event === 'edit') {
                layer.open({
                    type: 2,
                    title: '编辑#explain_web#',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['100%', '100%'],
                    content: "edit?#primary_key#=" + data.#primary_key#,
                    cancel: function(){
                        table.reload('test-table-page');
                    }
                });
            }else if (obj.event === 'del') {
                layer.confirm('确定删除？', function (index) {
                    $.ajax({
                        'url': '{:Url("del")}',
                        'data': {
                            '#primary_key#': data.#primary_key#
                        },
                        'success': function (data) {
                            if (data.code === 0) {
                                layer.alert(data.msg,{
                                    closeBtn: 0
                                },function (index) {
                                    table.reload('test-table-page');
                                    layer.close(index);
                                });
                            } else {
                                layer.alert(data.msg,{
                                    skin: 'layui-layer-error' //样式类名
                                    ,closeBtn: 0
                                });
                            }
                        },
                        'dataType': 'json',//返回的格式
                        'type': 'post',//发送的格式
                    });
                    obj.del();
                    layer.close(index);
                });
            }
        });
    });
</script>