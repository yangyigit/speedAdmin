<style>
    .layui-form-label{
        padding: 8px 2px 8px 0;
        width: 120px;
    }
    .layui-input-block{
        margin-left: 130px;
    }
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">新增#explain_web#</div>
        <div class="layui-card-body" style="padding: 15px;">
            <form class="layui-form" action="" lay-filter="component-form-group">
                #fields#

                <div class="layui-form-item layui-layout-admin">
                    <div class="layui-input-block">
                        <div class="layui-footer" style="left: 0;">
                            <button class="layui-btn" lay-submit="" lay-filter="component-form-demo1">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index' ,'form', 'laydate'], function () {
        let $ = layui.$,
            admin = layui.admin,
            element = layui.element,
            layer = layui.layer,
            laydate = layui.laydate,
            form = layui.form;

        form.render(null, 'component-form-group');

        /* 监听提交 */
        form.on('submit(component-form-demo1)', function (data) {
            $.ajax({
                'url':'{:Url("add")}',
                'data':JSON.stringify(data.field),
                'success':function(data){
                    if (data.code === 0) {
                        layer.alert(data.msg,{
                            closeBtn: 0
                        },function (index) {
                            location.reload();
                            layer.close(index);
                        });
                    } else {
                        layer.alert(data.msg,{
                            skin: 'layui-layer-error' //样式类名
                            ,closeBtn: 0
                        });
                    }
                },
                'dataType':'json',//返回的格式
                'type':'post',//发送的格式
            });
            return false;
        });
    });
</script>