<?php

/**
 * ECSHOP v2.6.2 升级程序
 * ============================================================================
 * 版权所有 (C) 2005-2008 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author:     ECSHOP R&D TEAM  http://www.ecshop.com
 * @version:    v2.x
 * ---------------------------------------------
 * $Author: testyang $
 * $Date: 2008-11-12 15:38:08 +0800 (星期三, 12 十一月 2008) $
 * $Id: v2.6.1.php 15171 2008-11-12 07:38:08Z testyang $
 */

class up_v2_6_2
{
    var $sql_files = array(
                            'structure' => 'structure.sql',
                            'data' => array(
                                            'zh_cn_gbk' => 'data_zh_cn_gbk.sql',
                                            'zh_cn_utf-8' => 'data_zh_cn_utf-8.sql',
                                            'zh_tw_utf-8' => 'data_zh_tw_utf-8.sql'
                            )
        );

    var $auto_match = true;

    function __construct(){}
    function up_v2_6_2(){}

    function update_database_optionally()
    {
        global $ecs, $db, $prefix;

        include_once(ROOT_PATH . 'includes/inc_constant.php');
    }

    function update_files()
    {
        global $ecs, $db, $prefix;
        //查询sort_order字段是否存在
        $sort_order_col = $db->getRow("DESCRIBE " . $ecs->table('goods') . " `sort_order`");
        if (!$sort_order_col)
        {
            $db->query("ALTER TABLE " . $ecs->table('goods') . " ADD `sort_order` MEDIUMINT( 4 ) NOT NULL DEFAULT '0'");
        }
        $db->query("ALTER TABLE " . $ecs->table('goods') . " ADD INDEX (`sort_order`)");
    }

    /**
     * 私有函数，转换时间的操作都放在这个函数里面
     *
     * @return  void
     */
    function convert_datetime()
    {

    }
}

?>
