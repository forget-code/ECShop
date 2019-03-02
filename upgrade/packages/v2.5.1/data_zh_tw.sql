-- 增加拍卖权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 7, 'auction');
-- 增加团购权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 7, 'group_by');
-- 增加拍卖权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 7, 'favourable');
-- 增加拍卖权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 7, 'whole_sale');
-- 增加拍卖权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 1, 'goods_auto');
-- 增加团购权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 2, 'article_auto');
-- 增加计划任务权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 5, 'cron');
-- 增加推荐设置权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 5, 'affiliate');
-- 增加分成管理权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 5, 'affiliate_ck');
-- 增加关注管理权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 8, 'attention_list');
-- 增加邮件订阅管理权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 8, 'email_list');
-- 增加杂志管理权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 8, 'magazine_list');
-- 增加邮件队列管理权限
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 8, 'view_sendlist');
INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`) VALUES (NULL, 1, 'virualcard');
-- 把专题管理放到促销管理栏目下
UPDATE `ecs_admin_action` SET `parent_id` = 7 WHERE `action_code` = 'topic_manage';
-- 给shop_config表的上传附件大小增加一个选项
UPDATE `ecs_shop_config` SET `store_range` = 'default,0,64,128,256,512,1024,2048,4096' WHERE `code` = 'upload_size_limit';
-- 增加商品显示方式的设置
INSERT INTO `ecs_shop_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`) VALUES (322, 3, 'show_order_type', 'select', '0,1,2', '', '0', '1');
-- 增加帮助菜单是否打开的设置
INSERT INTO `ecs_shop_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`) VALUES (333, 3, 'help_open', 'select', '0,1', '', '1', '1');
