##用户中心微服务

1. 执行依赖包安装
```
composer install
```

2. 初始化配置
```
php init --env=Development --overwrite=all
```

3. 目录结构
```
├── api
│   ├── components      //组件
│   ├── config          //配置
│   ├── forms           //表单模型
│   ├── models          //数据模型
│   ├── modules         //模块
│   ├── rbac            //rbac权限
│   ├── resources       //资源
│   ├── searches        //搜索模型
│   ├── services        //服务
│   ├── traits          //特性
│   └── web
├── autocompletion.php  //IDE代码自动补全
├── common
│   ├── behaviors       //行为类
│   ├── components      //组件
│   ├── config          //配置
│   ├── messages        //国际化
│   └── validators      //核心验证器
├── composer.json    
├── composer.lock    
├── console             //cli控制台    
│   ├── config    
│   ├── controllers     //控制器
│   ├── migrations      //数据迁移文件
│   └── yii    
├── environments        //环境配置
│   ├── dev             //开发环境
│   ├── index.php    
│   └── prod            //生产环境
├── vendor              //第三方包
├── init
├── README.md
├── requirements.php
└── yii
```
