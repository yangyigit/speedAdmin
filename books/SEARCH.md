# 后台搜索设置

### 使用介绍

#### 视图层：

搜索一个字段，需要指定表名和字段名，例如搜索 amdmin 表的 username 字段，代码如下：
```
<input name="search_admin#username" autocomplete="off" class="layui-input" type="text">
```
如果需要改变搜索类型，可以有三种：

- 等于（默认），可以不写，代码如下：
```
<input type="hidden" name="type_admin#username" value="eq">
```

- 搜索，代码如下：
```
<input type="hidden" name="type_admin#username" value="like">
```

- 时间范围，代码如下：
```
<input type="hidden" name="type_admin#username" value="time">
```

#### 控制器层：
代码如下：
```
$requestData = $this->request -> all();
$map = createSearchWhere($requestData);
```

然后将$map放入 where($map) 即可