## 概念
通过类注解和类方法注解的形式设置权限节点

## 使用说明

1、类注解为一级节点
```
/**
 * ##请求日志管理##
 * author: yy
 * Class RequestLog.
 */
class RequestLogController extends BaseController
```
2、类方法注解为二级节点
- 显示菜单节点配置
```
    /**
     * ##^列表展示##
     * @return mixed
     */
    public function showList(RenderInterface $render)
```

- 不显示菜单节点配置
```
    /**
     * ##添加##
     */
    public function add(RenderInterface $render)
```
3、在权限管理->规则管理中点击 “更新” 即可生成权限节点