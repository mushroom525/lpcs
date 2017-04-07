### 接口信息
#### 接口名：orderlist()
#### 接口描述：订单列表信息

### 接口格式

#### 调用

```
接收方式        GET/POST
```

```
接口地址：.../lpcs/home/order/orderlist
```

###### Json数据格式
```
data
openid              微信用户id  

callback            回调
```

#### 回调
###### Json数据格式

```
成功
callback(
code         000
data         {
                    openid      微信用户id
                    contetn     内容
             }
msg          发表成功
)
```

```
失败
callback(
code          111
data          ""
msg           发表失败
)
```

###### Code值含义

```
```