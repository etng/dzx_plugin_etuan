DiscuzX Team Buy Plugin
=======================

Use this plugin to enable members in your specified group can create team buy
and others can buy it.

Installation
-----------

Just like a common discuzx plugin


Modify
-------
add the following code to /dzx2/config/config_global.php

```	$_config['plugindeveloper'] = 1;
```

TOTO
----
* Supplier Logo, Product Photo file upload

中文说明
--------

* 解压本压缩包并上传到<bbsroot>/source/plugin/目录下
* 访问  __插件__=>__插件__=>__安装新插件__ 找到插件__易团__点击旁边的__安装__链接，按照提示进行安装即可
* 点击左侧导航栏的__易团__链接进入团购设置界面，在这里设置商家用户组、支付宝信息、积分兑换比例等
* 接上,在__小区管理__里面对小区进行管理
* 在 __论坛__=>__板块管理__ 选择需要发布团购帖子的板块， __编辑__=>__帖子选项__=>__允许发布的扩展特殊主题__ 处勾选 __团购主题__ 并保存
* 在__用户=>__用户组__ 选择商家所在的用户组，__编辑__=>__论坛相关__=>__特殊主题__=>__允许发布的扩展特殊主题__ 处勾选 __团购主题__并保存
* 访问前台，选择加入过商家用户组的用户登录则会在右上角导航里面看到__我的团购__，点击即可进入进行供应商、商品、团购及发货等管理
* 访问前台，任何用户都可看到右上角导航的__购物车__，可进入我的订单、收货地址等管理。到团购主题相关的板块购买后可以进入结账流程提交订单并支付完成购物