## 用户中心微服务

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
├── api                 //核心服务
│   ├── components      //组件
│   ├── config          //配置
│   ├── forms           //表单模型
│   ├── models          //数据模型
│   ├── modules         //模块
│   ├── rbac            //rbac权限
│   ├── resources       //资源
│   ├── searches        //搜索模型
│   ├── services        //服务
│   ├── traits          //特性
│   └── web             //服务入口
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
#### api文档效果图
<img src="https://www.ryan1068.cn/wp-content/uploads/2020/06/QQ%E6%88%AA%E5%9B%BE20200612171246.png"  />
