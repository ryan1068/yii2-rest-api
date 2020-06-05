##呼叫中心项目

1. 向运维申请gitlab的权限
2. 执行依赖包安装
```
composer install
```
3. 初始化配置
```
php init --env=Development --overwrite=all
```
4. 目录结构
```
├── api                //接口项目
│   ├── behaviors      //行为类 
│   ├── components     //组件
│   ├── config         //项目配置
│   ├── controllers    //控制器
│   ├── forms          //表单模型
│   ├── models         //数据表模型
│   ├── modules        //模块
│   ├── resources      //资源类
│   │   └── search     //搜索模型
│   └── web            //访问入口
├── autocompletion.php //IDE代码自动补全
├── common
│   ├── behaviors      //公共行为类
│   ├── components     //公共组件
│   ├── config         //公共配置
│   ├── messages       //国际化
│   └── models         //公共模型
├── composer.json
├── composer.lock
├── console            //cli命令行
│   ├── config  
│   ├── controllers
│   └── models
├── environments       //环境配置
│   ├── dev            //开发环境
│   ├── index.php
│   └── prod           //生产环境
│   └── test           //测试环境
├── vendor             //第三方包
├── init
├── README.md
├── requirements.php
└── yii
```

