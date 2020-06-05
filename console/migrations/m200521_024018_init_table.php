<?php

use yii\db\Migration;

/**
 * Class m200521_024018_init_table
 */
class m200521_024018_init_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute($this->getSql());

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_024018_init_role cannot be reverted.\n";

        return false;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return <<<SQL
USE `call_center`;

CREATE TABLE `cc_ucenter_admin_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(15) NOT NULL DEFAULT '' COMMENT '账号',
  `nickname` varchar(15) NOT NULL DEFAULT '' COMMENT '姓名',
  `tel` varchar(11) NOT NULL DEFAULT '' COMMENT '电话',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(100) NOT NULL DEFAULT '' COMMENT '密码',
  `current_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前的角色id',
  `auth_key` varchar(100) NOT NULL DEFAULT '' COMMENT '认证key',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '账号状态:0启用，1停用',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型:0自定义增加，1业财增加',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`),
  KEY `tel` (`tel`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `is_del` (`is_del`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理用户表';

CREATE TABLE `cc_ucenter_admin_user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是当前使用角色（一个用户只有能一个当前使用的角色）：0否，1是',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `role_id` (`role_id`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户对应角色表';

CREATE TABLE `cc_ucenter_auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `cc_ucenter_auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `cc_ucenter_auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `cc_ucenter_auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `cc_ucenter_org` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '组织名称',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组织结构表';

CREATE TABLE `cc_ucenter_org_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '组织id',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组织对应用户表';

CREATE TABLE `cc_ucenter_org_area` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '组织id',
  `area_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '4S店id',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组织对应店表';

CREATE TABLE `cc_ucenter_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '角色名称',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '角色类型：0系统角色，1自定义角色',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `role_key` varchar(50) NOT NULL DEFAULT '' COMMENT '标识',
  `sort` int(10) unsigned NOT NULL DEFAULT '999' COMMENT '排序',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色表';

CREATE TABLE `cc_ucenter_role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `column_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限菜单id',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `cloumn_id` (`column_id`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限表';

CREATE TABLE `cc_ucenter_yecai_admin_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(15) NOT NULL DEFAULT '' COMMENT '账号',
  `nickname` varchar(15) NOT NULL DEFAULT '' COMMENT '姓名',
  `tel` varchar(11) NOT NULL DEFAULT '' COMMENT '电话',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(100) NOT NULL DEFAULT '' COMMENT '密码',
  `auth_key` varchar(100) NOT NULL DEFAULT '' COMMENT '认证key',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '账号状态:0启用，1停用',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理用户表';

CREATE TABLE `cc_ucenter_column` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单地址',
  `permission` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单权限标识',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示：0显示，1不显示',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单详情',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0正常，1删除',
  `created_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员',
  `updated_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新人员',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `sort` (`sort`,`created_at`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单权限表';

/*Data for the table `cc_ucenter_column` */

insert  into `cc_ucenter_column`(`id`,`pid`,`name`,`url`,`permission`,`sort`,`visible`,`remark`,`icon`,`is_del`,`created_at`,`updated_at`,`created_by`,`updated_by`) values 

(1,0,'首页','index','system:index',1,0,'首页','',1,0,0,0,0),

(2,0,'工作台','work','system:work',2,0,'管理员工作台','',0,0,0,0,0),

(3,0,'呼叫中心','call','system:call',4,0,'呼叫中心','',0,0,0,0,0),

(4,0,'业务管理','business','system:business',3,0,'业务管理','',0,0,0,0,0),

(5,0,'系统配置','setting','system:setting',5,0,'系统配置','',0,0,0,0,0),

(6,2,'DCC外呼员工工作台模式','invite','system:work:inviter',1,0,'','',0,0,0,0,0),

(7,2,'DCC组长工作台模式','group','system:work:leader',2,0,'','',0,0,0,0,0),

(8,3,'任务管理','call/task','system:call:task',1,0,'','',0,0,0,0,0),

(9,3,'知识库管理','call/know','system:call:know',2,0,'','',0,0,0,0,0),

(10,3,'坐席管理','call/seat','system:call:seat',3,0,'','',0,0,0,0,0),

(11,3,'垂媒管理','call/media-manage','system:call:media-manage',4,0,'','',0,0,0,0,0),

(12,3,'通话记录','call/call-record','system:call:call-record',5,0,'','',0,0,0,0,0),

(13,3,'参数配置','call/param','system:call:param-setting',6,0,'','',0,0,0,0,0),

(14,8,'开口任务','call/task/talk','system:call:talk-task',1,0,'','',0,0,0,0,0),

(15,10,'坐席组管理','call/seat/seat-manage','system:call:seat-manage',1,0,'','',0,0,0,0,0),

(16,10,'坐席设置','call/seat/seat-setting','system:call:seat-setting',2,0,'','',0,0,0,0,0),

(17,13,'DCC跟进规则设置','call/param/dcc','system:call:dcc',1,0,'','',0,0,0,0,0),

(18,13,'战败原因设置','call/param/defeat','system:call:defeat',2,0,'','',0,0,0,0,0),

(19,13,'线索/客户来源设置','call/param/source','system:call:source',3,0,'','',0,0,0,0,0),

(20,13,'线索设置','call/param/clue','system:call:clue',4,0,'','',0,0,0,0,0),

(21,13,'可选功能','call/param/optional','system:call:optional',5,0,'','',0,0,0,0,0),

(22,4,'线索管理','business/clue','system:business:clue',1,0,'','',0,0,0,0,0),

(23,4,'潜客管理','business/potential','system:business:potential',2,0,'','',0,0,0,0,0),

(24,4,'邀约管理','business/invite','system:business:invite',3,0,'','',0,0,0,0,0),

(25,4,'订单管理','business/order','system:business:order',4,0,'','',0,0,0,0,0),

(26,4,'战败记录','business/defeat','system:business:defeat',5,0,'','',0,0,0,0,0),

(27,5,'集团人员','setting/group-people','system:setting:account',1,0,'','',0,0,0,0,0),

(28,5,'角色权限','setting/role-power','system:setting:role',2,0,'','',0,0,0,0,0),

(29,5,'集团门店','setting/group-shop','system:setting:area',3,0,'','',0,0,0,0,0),

(30,5,'组织架构','setting/org-structure','system:setting:org',4,0,'','',0,0,0,0,0),

(31,13,'无效原因设置','call/param/invalid','system:call:invalid',6,0,'','',0,0,0,0,0),

(32,2,'DCC主管/经理工作台模式','manage','system:work:supervisor',3,0,'','',0,0,0,0,0),

(33,9,'攻防话术','call/know/attack','system:call:attack',1,0,'','',0,0,0,0,0),

(34,9,'活动管理','call/know/activity','system:call:activity',2,0,'','',0,0,0,0,0),

(35,9,'常用话术','call/know/normal','system:call:normal',3,0,'','',0,0,0,0,0);
SQL;

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_024018_init_role cannot be reverted.\n";

        return false;
    }
    */
}
