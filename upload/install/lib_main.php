<?php

/**
 * ECSHOP 鍓嶅彴鍏?敤鍑芥暟搴
 * ============================================================================
 * 鐗堟潈鎵€鏈 2005-2008 涓婃捣鍟嗘淳缃戠粶绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_main.php 6098 2009-09-09 06:52:23Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 鏇存柊鐢ㄦ埛SESSION,COOKIE鍙婄櫥褰曟椂闂淬€佺櫥褰曟?鏁般€
 *
 * @access  public
 * @return  void
 */
function update_user_info()
{
    if (!$_SESSION['user_id'])
    {
        return false;
    }

    /* 鏌ヨ?浼氬憳淇℃伅 */
    $time = date('Y-m-d');
    $sql = 'SELECT u.user_money, u.pay_points, u.user_rank, u.rank_points, '.
            ' IFNULL(b.type_money, 0) AS user_bonus, u.last_login, u.last_ip'.
            ' FROM ' .$GLOBALS['ecs']->table('users'). ' AS u ' .
            ' LEFT JOIN ' .$GLOBALS['ecs']->table('user_bonus'). ' AS ub'.
            ' ON ub.user_id = u.user_id AND ub.used_time = 0 ' .
            ' LEFT JOIN ' .$GLOBALS['ecs']->table('bonus_type'). ' AS b'.
            " ON b.type_id = ub.bonus_type_id AND b.use_start_date <= '$time' AND b.use_end_date >= '$time' ".
            " WHERE u.user_id = '$_SESSION[user_id]'";
    if ($row = $GLOBALS['db']->getRow($sql))
    {
        /* 鏇存柊SESSION */
        $_SESSION['last_time']   = $row['last_login'];
        $_SESSION['last_ip']     = $row['last_ip'];
        $_SESSION['login_fail']  = 0;
        /*鍒ゆ柇鏄?惁鏄?壒娈婄瓑绾э紝鍙?兘鍚庡彴鎶婄壒娈婁細鍛樼粍鏇存敼鏅?€氫細鍛樼粍*/
        if($row['user_rank'] >0)
        {
            $sql="SELECT special_rank from ".$GLOBALS['ecs']->table('user_rank')."where rank_id='$row[user_rank]'";
            $special_rank = $GLOBALS['db']->getOne($sql);
            if(empty($special_rank))
            {   
                $sql="update ".$GLOBALS['ecs']->table('users')."set user_rank='0' where user_id='$_SESSION[user_id]'";
                $GLOBALS['db']->query($sql);
                $row['user_rank']=0;
            }
        }
        /* 鍙栧緱鐢ㄦ埛绛夌骇鍜屾姌鎵 */
        if ($row['user_rank'] == 0)
        {
            // 闈炵壒娈婄瓑绾э紝鏍规嵁绛夌骇绉?垎璁＄畻鐢ㄦ埛绛夌骇锛堟敞鎰忥細涓嶅寘鎷?壒娈婄瓑绾э級
            $sql = 'SELECT rank_id, discount FROM ' . $GLOBALS['ecs']->table('user_rank') . " WHERE special_rank = '0' AND min_points <= " . intval($row['rank_points']) . ' AND max_points > ' . intval($row['rank_points']);
            if ($row = $GLOBALS['db']->getRow($sql))
            {
                $_SESSION['user_rank'] = $row['rank_id'];
                $_SESSION['discount']  = $row['discount'] / 100.00;
            }
            else
            {
                $_SESSION['user_rank'] = 0;
                $_SESSION['discount']  = 1;
            }
        }
        else
        {
            // 鐗规畩绛夌骇
            $sql = 'SELECT rank_id, discount FROM ' . $GLOBALS['ecs']->table('user_rank') . " WHERE rank_id = '$row[user_rank]'";
            if ($row = $GLOBALS['db']->getRow($sql))
            {
                $_SESSION['user_rank'] = $row['rank_id'];
                $_SESSION['discount']  = $row['discount'] / 100.00;
            }
            else
            {
                $_SESSION['user_rank'] = 0;
                $_SESSION['discount']  = 1;
            }
        }
    }

    /* 鏇存柊鐧诲綍鏃堕棿锛岀櫥褰曟?鏁板強鐧诲綍ip */
    $sql = "UPDATE " .$GLOBALS['ecs']->table('users'). " SET".
           " visit_count = visit_count + 1, ".
           " last_ip = '" .real_ip(). "',".
           " last_login = '" .gmtime(). "'".
           " WHERE user_id = '" . $_SESSION['user_id'] . "'";
    $GLOBALS['db']->query($sql);
}

/**
 *  鑾峰彇鐢ㄦ埛淇℃伅鏁扮粍
 *
 * @access  public
 * @param
 *
 * @return array        $user       鐢ㄦ埛淇℃伅鏁扮粍
 */
function get_user_info($id=0)
{
    if ($id == 0)
    {
        $id = $_SESSION['user_id'];
    }
    $time = date('Y-m-d');
    $sql  = 'SELECT u.user_id, u.email, u.user_name, u.user_money, u.pay_points'.
            ' FROM ' .$GLOBALS['ecs']->table('users'). ' AS u ' .
            " WHERE u.user_id = '$id'";
    $user = $GLOBALS['db']->getRow($sql);
    $bonus = get_user_bonus($id);
    $user_name = explode('@',$user['user_name']);
    $user['username']    = $user_name[0];
    $user['user_points'] = $user['pay_points'] . $GLOBALS['_CFG']['integral_name'];
    $user['user_money']  = price_format($user['user_money'], false);
    $user['user_bonus']  = price_format($bonus['bonus_value'], false);

    return $user;
}
/**
 * 鍙栧緱褰撳墠浣嶇疆鍜岄〉闈㈡爣棰
 *
 * @access  public
 * @param   integer     $cat    鍒嗙被缂栧彿锛堝彧鏈夊晢鍝佸強鍒嗙被銆佹枃绔犲強鍒嗙被鐢ㄥ埌锛
 * @param   string      $str    鍟嗗搧鍚嶃€佹枃绔犳爣棰樻垨鍏朵粬闄勫姞鐨勫唴瀹癸紙鏃犻摼鎺ワ級
 * @return  array
 */
function assign_ur_here($cat = 0, $str = '')
{
    /* 鍒ゆ柇鏄?惁閲嶅啓锛屽彇寰楁枃浠跺悕 */
    $cur_url = basename(PHP_SELF);
    if (intval($GLOBALS['_CFG']['rewrite']))
    {
        $filename = strpos($cur_url,'-') ? substr($cur_url, 0, strpos($cur_url,'-')) : substr($cur_url, 0, -4);
    }
    else
    {
        $filename = substr($cur_url, 0, -4);
    }

    /* 鍒濆?鍖栤€滈〉闈㈡爣棰樷€濆拰鈥滃綋鍓嶄綅缃?€ */
    //鍘婚櫎鐗堟潈
//    if ($GLOBALS['personal']['level_info']['action']['remove_copyright'])
//    {
        $page_title = $GLOBALS['_CFG']['shop_title'];
//    }
//    else
//    {
//        $page_title = $GLOBALS['_CFG']['shop_title'] . ' - ' . 'Powered by wdwd.com';
//    }
    $ur_here    = '<a href=".">' . $GLOBALS['_LANG']['home'] . '</a>';

    /* 鏍规嵁鏂囦欢鍚嶅垎鍒??鐞嗕腑闂寸殑閮ㄥ垎 */
    if ($filename != 'index')
    {
        /* 澶勭悊鏈夊垎绫荤殑 */
        if (in_array($filename, array('category', 'goods', 'article_cat', 'article', 'brand')))
        {
            /* 鍟嗗搧鍒嗙被鎴栧晢鍝 */
            if ('category' == $filename || 'goods' == $filename || 'brand' == $filename)
            {
                if ($cat > 0)
                {
                    $cat_arr = get_parent_cats($cat);

                    $key     = 'cid';
                    $type    = 'category';
                }
                else
                {
                    $cat_arr = array();
                }
            }
            /* 鏂囩珷鍒嗙被鎴栨枃绔 */
            elseif ('article_cat' == $filename || 'article' == $filename)
            {
                if ($cat > 0)
                {
                    $cat_arr = get_article_parent_cats($cat);

                    $key  = 'acid';
                    $type = 'article_cat';
                }
                else
                {
                    $cat_arr = array();
                }
            }

            /* 寰?幆鍒嗙被 */
            if (!empty($cat_arr))
            {
                krsort($cat_arr);
                foreach ($cat_arr AS $val)
                {
                    $page_title = htmlspecialchars($val['cat_name']) . '_' . $page_title;
                    $args       = array($key => $val['cat_id']);
                    $ur_here   .= ' <code>&gt;</code> <a href="' . build_uri($type, $args, $val['cat_name']) . '">' .
                                    htmlspecialchars($val['cat_name']) . '</a>';
                }
            }
        }
        /* 澶勭悊鏃犲垎绫荤殑 */
        else
        {
            /* 鍥㈣喘 */
            if ('group_buy' == $filename)
            {
                $page_title = $GLOBALS['_LANG']['group_buy_goods'] . '_' . $page_title;
                $args       = array('gbid' => '0');
                $ur_here   .= ' <code>&gt;</code> <a href="group_buy.php">' .
                                $GLOBALS['_LANG']['group_buy_goods'] . '</a>';
            }
            /* 鎷嶅崠 */
            elseif ('auction' == $filename)
            {
                $page_title = $GLOBALS['_LANG']['auction'] . '_' . $page_title;
                $args       = array('auid' => '0');
                $ur_here   .= ' <code>&gt;</code> <a href="auction.php">' .
                                $GLOBALS['_LANG']['auction'] . '</a>';
            }
            /* 澶哄疂 */
            elseif ('snatch' == $filename)
            {
                $page_title = $GLOBALS['_LANG']['snatch'] . '_' . $page_title;
                $args       = array('id' => '0');
                $ur_here   .= ' <code> &gt; </code><a href="snatch.php">' .                                 $GLOBALS['_LANG']['snatch_list'] . '</a>';
            }
            /* 鎵瑰彂 */
            elseif ('wholesale' == $filename)
            {
                $page_title = $GLOBALS['_LANG']['wholesale'] . '_' . $page_title;
                $args       = array('wsid' => '0');
                $ur_here   .= ' <code>&gt;</code> <a href="wholesale.php">' .
                                $GLOBALS['_LANG']['wholesale'] . '</a>';
            }
            /* 绉?垎鍏戞崲 */
            elseif ('exchange' == $filename)
            {
                $page_title = $GLOBALS['_LANG']['exchange'] . '_' . $page_title;
                $args       = array('wsid' => '0');
                $ur_here   .= ' <code>&gt;</code> <a href="exchange.php">' .
                                $GLOBALS['_LANG']['exchange'] . '</a>';
            }
            /* 鍏朵粬鐨勫湪杩欓噷琛ュ厖 */
        }
    }

    /* 澶勭悊鏈€鍚庝竴閮ㄥ垎 */
    if (!empty($str))
    {
        $page_title  = $str . '_' . $page_title;
        $ur_here    .= ' <code>&gt;</code> ' . $str;
    }

    /* 杩斿洖鍊 */
    return array('title' => $page_title, 'ur_here' => $ur_here);
}

/**
 * 鑾峰緱鎸囧畾鍒嗙被鐨勬墍鏈変笂绾у垎绫
 *
 * @access  public
 * @param   integer $cat    鍒嗙被缂栧彿
 * @return  array
 */
function get_parent_cats($cat)
{
    if ($cat == 0)
    {
        return array();
    }

    $arr = $GLOBALS['db']->GetAll('SELECT cat_id, cat_name, parent_id FROM ' . $GLOBALS['ecs']->table('category'));

    if (empty($arr))
    {
        return array();
    }

    $index = 0;
    $cats  = array();

    while (1)
    {
        foreach ($arr AS $row)
        {
            if ($cat == $row['cat_id'])
            {
                $cat = $row['parent_id'];

                $cats[$index]['cat_id']   = $row['cat_id'];
                $cats[$index]['cat_name'] = $row['cat_name'];

                $index++;
                break;
            }
        }

        if ($index == 0 || $cat == 0)
        {
            break;
        }
    }

    return $cats;
}

/**
 * 鏍规嵁鎻愪緵鐨勬暟缁勭紪璇戞垚椤甸潰鏍囬?
 *
 * @access  public
 * @param   string  $type   绫诲瀷
 * @param   array   $arr    鍒嗙被鏁扮粍
 * @return  string
 */
function build_pagetitle($arr, $type = 'category')
{
    $str = '';

    foreach ($arr AS $val)
    {
        $str .= htmlspecialchars($val['cat_name']) . '_';
    }

    return $str;
}

/**
 * 鏍规嵁鎻愪緵鐨勬暟缁勭紪璇戞垚褰撳墠浣嶇疆
 *
 * @access  public
 * @param   string  $type   绫诲瀷
 * @param   array   $arr    鍒嗙被鏁扮粍
 * @return  void
 */
function build_urhere($arr, $type = 'category')
{
    krsort($arr);

    $str = '';
    foreach ($arr AS $val)
    {
        switch ($type)
        {
            case 'category':
            case 'brand':
                $args = array('cid' => $val['cat_id']);
                break;
            case 'article_cat':
                $args = array('acid' => $val['cat_id']);
                break;
        }

        $str .= ' <code>&gt;</code> <a href="' . build_uri($type, $args). '">' . htmlspecialchars($val['cat_name']) . '</a>';
    }

    return $str;
}

/**
 * 鑾峰緱鎸囧畾椤甸潰鐨勫姩鎬佸唴瀹
 *
 * @access  public
 * @param   string  $tmp    妯℃澘鍚嶇О
 * @return  void
 */
function assign_dynamic($tmp)
{
    $sql = 'SELECT id, number, type FROM ' . $GLOBALS['ecs']->table('template') .
        " WHERE filename = '$tmp' AND type > 0 AND remarks ='' AND theme='" . $GLOBALS['_CFG']['template'] . "'";
    $res = $GLOBALS['db']->getAll($sql);

   foreach ($res AS $row)
    {
        switch ($row['type'])
        {
            case 1:
                /* 鍒嗙被涓嬬殑鍟嗗搧 */
                $GLOBALS['smarty']->assign('goods_cat_' . $row['id'], assign_cat_goods($row['id'], $row['number']));
            break;
            case 2:
                /* 鍝佺墝鐨勫晢鍝 */
                $brand_goods = assign_brand_goods($row['id'], $row['number']);

                $GLOBALS['smarty']->assign('brand_goods_' . $row['id'], $brand_goods['goods']);
                $GLOBALS['smarty']->assign('goods_brand_' . $row['id'], $brand_goods['brand']);
            break;
            case 3:
                /* 鏂囩珷鍒楄〃 */
                $cat_articles = assign_articles($row['id'], $row['number']);

                $GLOBALS['smarty']->assign('articles_cat_' . $row['id'], $cat_articles['cat']);
                $GLOBALS['smarty']->assign('articles_' . $row['id'], $cat_articles['arr']);
            break;
        }
    }
}

/**
 * 鍒嗛厤鏂囩珷鍒楄〃缁檚marty
 *
 * @access  public
 * @param   integer     $id     鏂囩珷鍒嗙被鐨勭紪鍙
 * @param   integer     $num    鏂囩珷鏁伴噺
 * @return  array
 */
function assign_articles($id, $num)
{
    $sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE cat_id = '" . $id ."'";

    $cat['id']       = $id;
    $cat['name']     = $GLOBALS['db']->getOne($sql);
    $cat['url']      = build_uri('article_cat', array('acid' => $id), $cat['name']);

    $articles['cat'] = $cat;
    $articles['arr'] = get_cat_articles($id, 1, $num);

    return $articles;
}

/**
 * 鍒嗛厤甯?姪淇℃伅
 *
 * @access  public
 * @return  array
 */
function get_shop_help()
{
    $sql = 'SELECT c.cat_id, c.cat_name, c.sort_order, a.article_id, a.title, a.file_url, a.open_type ' .
            'FROM ' .$GLOBALS['ecs']->table('article'). ' AS a ' .
            'LEFT JOIN ' .$GLOBALS['ecs']->table('article_cat'). ' AS c ' .
            'ON a.cat_id = c.cat_id WHERE c.cat_type = 5 AND a.is_open = 1 ' .
            'ORDER BY c.sort_order ASC, a.article_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $key => $row)
    {
        $arr[$row['cat_id']]['cat_id']                       = build_uri('article_cat', array('acid'=> $row['cat_id']), $row['cat_name']);
        $arr[$row['cat_id']]['cat_name']                     = $row['cat_name'];
        $arr[$row['cat_id']]['article'][$key]['article_id']  = $row['article_id'];
        $arr[$row['cat_id']]['article'][$key]['title']       = $row['title'];
        $arr[$row['cat_id']]['article'][$key]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
            sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
        $arr[$row['cat_id']]['article'][$key]['url']         = $row['open_type'] != 1 ?
            build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
    }

    return $arr;
}

/**
 * 鍒涘缓鍒嗛〉淇℃伅
 *
 * @access  public
 * @param   string  $app            绋嬪簭鍚嶇О锛屽?category
 * @param   string  $cat            鍒嗙被ID
 * @param   string  $record_count   璁板綍鎬绘暟
 * @param   string  $size           姣忛〉璁板綍鏁
 * @param   string  $sort           鎺掑簭绫诲瀷
 * @param   string  $order          鎺掑簭椤哄簭
 * @param   string  $page           褰撳墠椤
 * @param   string  $keywords       鏌ヨ?鍏抽敭瀛
 * @param   string  $brand          鍝佺墝
 * @param   string  $price_min      鏈€灏忎环鏍
 * @param   string  $price_max      鏈€楂樹环鏍
 * @return  void
 */
function assign_pager($app, $cat, $record_count, $size, $sort, $order, $page = 1,
                        $keywords = '', $brand = 0, $price_min = 0, $price_max = 0, $display_type = 'list', $filter_attr='', $url_format='', $sch_array='')
{
    $sch = array('keywords'  => $keywords,
                 'sort'      => $sort,
                 'order'     => $order,
                 'cat'       => $cat,
                 'brand'     => $brand,
                 'price_min' => $price_min,
                 'price_max' => $price_max,
                 'filter_attr'=>$filter_attr,
                 'display'   => $display_type
        );

    $page = intval($page);
    if ($page < 1)
    {
        $page = 1;
    }

    $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;

    $pager['page']         = $page;
    $pager['size']         = $size;
    $pager['sort']         = $sort;
    $pager['order']        = $order;
    $pager['record_count'] = $record_count;
    $pager['page_count']   = $page_count;
    $pager['display']      = $display_type;

    switch ($app)
    {
        case 'category':
            $uri_args = array('cid' => $cat, 'bid' => $brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'sort' => $sort, 'order' => $order, 'display' => $display_type);
            break;
        case 'article_cat':
            $uri_args = array('acid' => $cat, 'sort' => $sort, 'order' => $order);
            break;
        case 'brand':
            $uri_args = array('cid' => $cat, 'bid' => $brand, 'sort' => $sort, 'order' => $order, 'display' => $display_type);
            break;
        case 'search':
            $uri_args = array('cid' => $cat, 'bid' => $brand, 'sort' => $sort, 'order' => $order);
            break;
        case 'exchange':
            $uri_args = array('cid' => $cat, 'integral_min'=>$price_min, 'integral_max'=>$price_max, 'sort' => $sort, 'order' => $order, 'display' => $display_type);
            break;
    }
    /* 鍒嗛〉鏍峰紡 */
    $pager['styleid'] = isset($GLOBALS['_CFG']['page_style'])? intval($GLOBALS['_CFG']['page_style']) : 0;

    $page_prev  = ($page > 1) ? $page - 1 : 1;
    $page_next  = ($page < $page_count) ? $page + 1 : $page_count;
    if ($pager['styleid'] == 0)
    {
        if (!empty($url_format))
        {
            $pager['page_first'] = $url_format . 1;
            $pager['page_prev']  = $url_format . $page_prev;
            $pager['page_next']  = $url_format . $page_next;
            $pager['page_last']  = $url_format . $page_count;
        }
        else
        {
            $pager['page_first'] = build_uri($app, $uri_args, '', 1, $sch);
            $pager['page_prev']  = build_uri($app, $uri_args, '', $page_prev);
            $pager['page_next']  = build_uri($app, $uri_args, '', $page_next);
            $pager['page_last']  = build_uri($app, $uri_args, '', $page_count);
        }
        $pager['array']      = array();

        for ($i = 1; $i <= $page_count; $i++)
        {
            $pager['array'][$i] = $i;
        }
    }
    else
    {
        $_pagenum = 10;     // 鏄剧ず鐨勯〉鐮
        $_offset = 2;       // 褰撳墠椤靛亸绉诲€
        $_from = $_to = 0;  // 寮€濮嬮〉, 缁撴潫椤
        if($_pagenum > $page_count)
        {
            $_from = 1;
            $_to = $page_count;
        }
        else
        {
            $_from = $page - $_offset;
            $_to = $_from + $_pagenum - 1;
            if($_from < 1)
            {
                $_to = $page + 1 - $_from;
                $_from = 1;
                if($_to - $_from < $_pagenum)
                {
                    $_to = $_pagenum;
                }
            }
            elseif($_to > $page_count)
            {
                $_from = $page_count - $_pagenum + 1;
                $_to = $page_count;
            }
        }
        if (!empty($url_format))
        {
            $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 : '';
            $pager['page_prev']  = ($page > 1) ? $url_format . $page_prev : '';
            $pager['page_next']  = ($page < $page_count) ? $url_format . $page_next : '';
            $pager['page_last']  = ($_to < $page_count) ? $url_format . $page_count : '';
            $pager['page_kbd']  = ($_pagenum < $page_count) ? true : false;
            $pager['page_number'] = array();
            for ($i=$_from;$i<=$_to;++$i)
            {
                $pager['page_number'][$i] = $url_format . $i;
            }
        }
        else
        {
            $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? build_uri($app, $uri_args, '', 1) : '';
            $pager['page_prev']  = ($page > 1) ? build_uri($app, $uri_args, '', $page_prev) : '';
            $pager['page_next']  = ($page < $page_count) ? build_uri($app, $uri_args, '', $page_next) : '';
            $pager['page_last']  = ($_to < $page_count) ? build_uri($app, $uri_args, '', $page_count) : '';
            $pager['page_kbd']  = ($_pagenum < $page_count) ? true : false;
            $pager['page_number'] = array();
            for ($i=$_from;$i<=$_to;++$i)
            {
                $pager['page_number'][$i] = build_uri($app, $uri_args, '', $i);
            }
        }
    }
    if (!empty($sch_array))
    {
        $pager['search'] = $sch_array;
    }
    else
    {
        $pager['search']['category'] = $cat;
        foreach ($sch AS $key => $row)
        {
            $pager['search'][$key] = $row;
        }
    }

    $GLOBALS['smarty']->assign('pager', $pager);
}

/**
 *  鐢熸垚缁檖ager.lbi璧嬪€肩殑鏁扮粍
 *
 * @access  public
 * @param   string      $url        鍒嗛〉鐨勯摼鎺ュ湴鍧€(蹇呴』鏄?甫鏈夊弬鏁扮殑鍦板潃锛岃嫢涓嶆槸鍙?互浼?€犱竴涓?棤鐢ㄥ弬鏁?
 * @param   array       $param      閾炬帴鍙傛暟 key涓哄弬鏁板悕锛寁alue涓哄弬鏁板€
 * @param   int         $record     璁板綍鎬绘暟閲
 * @param   int         $page       褰撳墠椤垫暟
 * @param   int         $size       姣忛〉澶у皬
 *
 * @return  array       $pager
 */
function get_pager($url, $param, $record_count, $page = 1, $size = 10)
{
    $size = intval($size);
    if ($size < 1)
    {
        $size = 10;
    }

    $page = intval($page);
    if ($page < 1)
    {
        $page = 1;
    }

    $record_count = intval($record_count);

    $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
    if ($page > $page_count)
    {
        $page = $page_count;
    }
    /* 鍒嗛〉鏍峰紡 */
    $pager['styleid'] = isset($GLOBALS['_CFG']['page_style'])? intval($GLOBALS['_CFG']['page_style']) : 0;

    $page_prev  = ($page > 1) ? $page - 1 : 1;
    $page_next  = ($page < $page_count) ? $page + 1 : $page_count;

    /* 灏嗗弬鏁板悎鎴恥rl瀛椾覆 */
    $param_url = '?';
    foreach ($param AS $key => $value)
    {
        $param_url .= $key . '=' . $value . '&';
    }

    $pager['url']          = $url;
    $pager['start']        = ($page -1) * $size;
    $pager['page']         = $page;
    $pager['size']         = $size;
    $pager['record_count'] = $record_count;
    $pager['page_count']   = $page_count;

    if ($pager['styleid'] == 0)
    {
        $pager['page_first']   = $url . $param_url . 'page=1';
        $pager['page_prev']    = $url . $param_url . 'page=' . $page_prev;
        $pager['page_next']    = $url . $param_url . 'page=' . $page_next;
        $pager['page_last']    = $url . $param_url . 'page=' . $page_count;
        $pager['array']  = array();
        for ($i = 1; $i <= $page_count; $i++)
        {
            $pager['array'][$i] = $i;
        }
    }
    else
    {
        $_pagenum = 10;     // 鏄剧ず鐨勯〉鐮
        $_offset = 2;       // 褰撳墠椤靛亸绉诲€
        $_from = $_to = 0;  // 寮€濮嬮〉, 缁撴潫椤
        if($_pagenum > $page_count)
        {
            $_from = 1;
            $_to = $page_count;
        }
        else
        {
            $_from = $page - $_offset;
            $_to = $_from + $_pagenum - 1;
            if($_from < 1)
            {
                $_to = $page + 1 - $_from;
                $_from = 1;
                if($_to - $_from < $_pagenum)
                {
                    $_to = $_pagenum;
                }
            }
            elseif($_to > $page_count)
            {
                $_from = $page_count - $_pagenum + 1;
                $_to = $page_count;
            }
        }
        $url_format = $url . $param_url . 'page=';
        $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 : '';
        $pager['page_prev']  = ($page > 1) ? $url_format . $page_prev : '';
        $pager['page_next']  = ($page < $page_count) ? $url_format . $page_next : '';
        $pager['page_last']  = ($_to < $page_count) ? $url_format . $page_count : '';
        $pager['page_kbd']  = ($_pagenum < $page_count) ? true : false;
        $pager['page_number'] = array();
        for ($i=$_from;$i<=$_to;++$i)
        {
            $pager['page_number'][$i] = $url_format . $i;
        }
    }
    $pager['search'] = $param;

    return $pager;
}

/**
 * 璋冪敤璋冩煡鍐呭?
 *
 * @access  public
 * @param   integer $id   璋冩煡鐨勭紪鍙
 * @return  array
 */
function get_vote($id = '')
{
    /* 闅忔満鍙栧緱涓€涓?皟鏌ョ殑涓婚? */
    if (empty($id))
    {
        $time = gmtime();
        $sql = 'SELECT vote_id, vote_name, can_multi, vote_count, RAND() AS rnd' .
               ' FROM ' . $GLOBALS['ecs']->table('vote') .
               " WHERE start_time <= '$time' AND end_time >= '$time' ".
               ' ORDER BY rnd LIMIT 1';
    }
    else
    {
        $sql = 'SELECT vote_id, vote_name, can_multi, vote_count' .
               ' FROM ' . $GLOBALS['ecs']->table('vote').
               " WHERE vote_id = '$id'";
    }

    $vote_arr = $GLOBALS['db']->getRow($sql);

    if ($vote_arr !== false && !empty($vote_arr))
    {
        /* 閫氳繃璋冩煡鐨処D,鏌ヨ?璋冩煡閫夐」 */
        $sql_option = 'SELECT v.*, o.option_id, o.vote_id, o.option_name, o.option_count ' .
                      'FROM ' . $GLOBALS['ecs']->table('vote') . ' AS v, ' .
                            $GLOBALS['ecs']->table('vote_option') . ' AS o ' .
                      "WHERE o.vote_id = v.vote_id AND o.vote_id = '$vote_arr[vote_id]'";
        $res = $GLOBALS['db']->getAll($sql_option);

        /* 鎬荤エ鏁 */
        $sql = 'SELECT SUM(option_count) AS all_option FROM ' . $GLOBALS['ecs']->table('vote_option') .
               " WHERE vote_id = '" . $vote_arr['vote_id'] . "' GROUP BY vote_id";
        $option_num = $GLOBALS['db']->getOne($sql);

        $arr = array();
        $count = 100;
        foreach ($res AS $idx => $row)
        {
            if ($option_num > 0 && $idx == count($res) - 1)
            {
                $percent = $count;
            }
            else
            {
                $percent = ($row['vote_count'] > 0 && $option_num > 0) ? round(($row['option_count'] / $option_num) * 100) : 0;

                $count -= $percent;
            }
            $arr[$row['vote_id']]['options'][$row['option_id']]['percent'] = $percent;

            $arr[$row['vote_id']]['vote_id']    = $row['vote_id'];
            $arr[$row['vote_id']]['vote_name']  = $row['vote_name'];
            $arr[$row['vote_id']]['can_multi']  = $row['can_multi'];
            $arr[$row['vote_id']]['vote_count'] = $row['vote_count'];

            $arr[$row['vote_id']]['options'][$row['option_id']]['option_id']    = $row['option_id'];
            $arr[$row['vote_id']]['options'][$row['option_id']]['option_name']  = $row['option_name'];
            $arr[$row['vote_id']]['options'][$row['option_id']]['option_count'] = $row['option_count'];
        }

        $vote_arr['vote_id'] = (!empty($vote_arr['vote_id'])) ? $vote_arr['vote_id'] : '';

        $vote = array('id' => $vote_arr['vote_id'], 'content' => $arr);

        return $vote;
    }
}

/**
 * 鑾峰緱娴忚?鍣ㄥ悕绉板拰鐗堟湰
 *
 * @access  public
 * @return  string
 */
function get_user_browser()
{
    if (empty($_SERVER['HTTP_USER_AGENT']))
    {
        return '';
    }

    $agent       = $_SERVER['HTTP_USER_AGENT'];
    $browser     = '';
    $browser_ver = '';

    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs))
    {
        $browser     = 'Internet Explorer';
        $browser_ver = $regs[1];
    }
    elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs))
    {
        $browser     = 'FireFox';
        $browser_ver = $regs[1];
    }
    elseif (preg_match('/Maxthon/i', $agent, $regs))
    {
        $browser     = '(Internet Explorer ' .$browser_ver. ') Maxthon';
        $browser_ver = '';
    }
    elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs))
    {
        $browser     = 'Opera';
        $browser_ver = $regs[1];
    }
    elseif (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs))
    {
        $browser     = 'OmniWeb';
        $browser_ver = $regs[2];
    }
    elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs))
    {
        $browser     = 'Netscape';
        $browser_ver = $regs[2];
    }
    elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs))
    {
        $browser     = 'Safari';
        $browser_ver = $regs[1];
    }
    elseif (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs))
    {
        $browser     = '(Internet Explorer ' .$browser_ver. ') NetCaptor';
        $browser_ver = $regs[1];
    }
    elseif (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs))
    {
        $browser     = 'Lynx';
        $browser_ver = $regs[1];
    }

    if (!empty($browser))
    {
       return addslashes($browser . ' ' . $browser_ver);
    }
    else
    {
        return 'Unknow browser';
    }
}

/**
 * 鍒ゆ柇鏄?惁涓烘悳绱㈠紩鎿庤湗铔
 *
 * @access  public
 * @return  string
 */
function is_spider($record = true)
{
    static $spider = NULL;

    if ($spider !== NULL)
    {
        return $spider;
    }

    if (empty($_SERVER['HTTP_USER_AGENT']))
    {
        $spider = '';

        return '';
    }

    $searchengine_bot = array(
        'googlebot',
        'mediapartners-google',
        'baiduspider+',
        'msnbot',
        'yodaobot',
        'yahoo! slurp;',
        'yahoo! slurp china;',
        'iaskspider',
        'sogou web spider',
        'sogou push spider'
    );

    $searchengine_name = array(
        'GOOGLE',
        'GOOGLE ADSENSE',
        'BAIDU',
        'MSN',
        'YODAO',
        'YAHOO',
        'Yahoo China',
        'IASK',
        'SOGOU',
        'SOGOU'
    );

    $spider = strtolower($_SERVER['HTTP_USER_AGENT']);

    foreach ($searchengine_bot AS $key => $value)
    {
        if (strpos($spider, $value) !== false)
        {
            $spider = $searchengine_name[$key];

            if ($record === true)
            {
                $GLOBALS['db']->autoReplace($GLOBALS['ecs']->table('searchengine'), array('date' => local_date('Y-m-d'), 'searchengine' => $spider, 'count' => 1), array('count' => 1));
            }

            return $spider;
        }
    }

    $spider = '';

    return '';
}

/**
 * 鑾峰緱瀹㈡埛绔?殑鎿嶄綔绯荤粺
 *
 * @access  private
 * @return  void
 */
function get_os()
{
    if (empty($_SERVER['HTTP_USER_AGENT']))
    {
        return 'Unknown';
    }

    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $os    = '';

    if (strpos($agent, 'win') !== false)
    {
        if (strpos($agent, 'nt 5.1') !== false)
        {
            $os = 'Windows XP';
        }
        elseif (strpos($agent, 'nt 5.2') !== false)
        {
            $os = 'Windows 2003';
        }
        elseif (strpos($agent, 'nt 5.0') !== false)
        {
            $os = 'Windows 2000';
        }
        elseif (strpos($agent, 'nt 6.0') !== false)
        {
            $os = 'Windows Vista';
        }
        elseif (strpos($agent, 'nt') !== false)
        {
            $os = 'Windows NT';
        }
        elseif (strpos($agent, 'win 9x') !== false && strpos($agent, '4.90') !== false)
        {
            $os = 'Windows ME';
        }
        elseif (strpos($agent, '98') !== false)
        {
            $os = 'Windows 98';
        }
        elseif (strpos($agent, '95') !== false)
        {
            $os = 'Windows 95';
        }
        elseif (strpos($agent, '32') !== false)
        {
            $os = 'Windows 32';
        }
        elseif (strpos($agent, 'ce') !== false)
        {
            $os = 'Windows CE';
        }
    }
    elseif (strpos($agent, 'linux') !== false)
    {
        $os = 'Linux';
    }
    elseif (strpos($agent, 'unix') !== false)
    {
        $os = 'Unix';
    }
    elseif (strpos($agent, 'sun') !== false && strpos($agent, 'os') !== false)
    {
        $os = 'SunOS';
    }
    elseif (strpos($agent, 'ibm') !== false && strpos($agent, 'os') !== false)
    {
        $os = 'IBM OS/2';
    }
    elseif (strpos($agent, 'mac') !== false && strpos($agent, 'pc') !== false)
    {
        $os = 'Macintosh';
    }
    elseif (strpos($agent, 'powerpc') !== false)
    {
        $os = 'PowerPC';
    }
    elseif (strpos($agent, 'aix') !== false)
    {
        $os = 'AIX';
    }
    elseif (strpos($agent, 'hpux') !== false)
    {
        $os = 'HPUX';
    }
    elseif (strpos($agent, 'netbsd') !== false)
    {
        $os = 'NetBSD';
    }
    elseif (strpos($agent, 'bsd') !== false)
    {
        $os = 'BSD';
    }
    elseif (strpos($agent, 'osf1') !== false)
    {
        $os = 'OSF1';
    }
    elseif (strpos($agent, 'irix') !== false)
    {
        $os = 'IRIX';
    }
    elseif (strpos($agent, 'freebsd') !== false)
    {
        $os = 'FreeBSD';
    }
    elseif (strpos($agent, 'teleport') !== false)
    {
        $os = 'teleport';
    }
    elseif (strpos($agent, 'flashget') !== false)
    {
        $os = 'flashget';
    }
    elseif (strpos($agent, 'webzip') !== false)
    {
        $os = 'webzip';
    }
    elseif (strpos($agent, 'offline') !== false)
    {
        $os = 'offline';
    }
    else
    {
        $os = 'Unknown';
    }

    return $os;
}

/**
 * 缁熻?璁块棶淇℃伅
 *
 * @access  public
 * @return  void
 */
function visit_stats()
{
    return;
    if (isset($GLOBALS['_CFG']['visit_stats']) && $GLOBALS['_CFG']['visit_stats'] == 'off')
    {
        return;
    }
    $time = gmtime();
    /* 妫€鏌ュ?鎴风?鏄?惁瀛樺湪璁块棶缁熻?鐨刢ookie */
    $visit_times = (!empty($_COOKIE['ECS']['visit_times'])) ? intval($_COOKIE['ECS']['visit_times']) + 1 : 1;
    setcookie('ECS[visit_times]', $visit_times, $time + 86400 * 365, '/');

    $browser  = get_user_browser();
    $os       = get_os();
    $ip       = real_ip();
    $area     = ecs_geoip($ip);

    /* 璇?█ */
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
    {
        $pos  = strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ';');
        $lang = addslashes(($pos !== false) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, $pos) : $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }
    else
    {
        $lang = '';
    }

    /* 鏉ユ簮 */
    if (!empty($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER']) > 9)
    {
        $pos = strpos($_SERVER['HTTP_REFERER'], '/', 9);
        if ($pos !== false)
        {
            $domain = substr($_SERVER['HTTP_REFERER'], 0, $pos);
            $path   = substr($_SERVER['HTTP_REFERER'], $pos);

            /* 鏉ユ簮鍏抽敭瀛 */
            if (!empty($domain) && !empty($path))
            {
                save_searchengine_keyword($domain, $path);
            }
        }
        else
        {
            $domain = $path = '';
        }
    }
    else
    {
        $domain = $path = '';
    }

    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('stats') . ' ( ' .
                'ip_address, visit_times, browser, system, language, area, ' .
                'referer_domain, referer_path, access_url, access_time' .
            ') VALUES (' .
                "'$ip', '$visit_times', '$browser', '$os', '$lang', '$area', ".
                "'" . addslashes($domain) ."', '" . addslashes($path) ."', '" . addslashes(PHP_SELF) ."', '" . $time . "')";
    $GLOBALS['db']->query($sql);
}

/**
 * 淇濆瓨鎼滅储寮曟搸鍏抽敭瀛
 *
 * @access  public
 * @return  void
 */
function save_searchengine_keyword($domain, $path)
{
    if (strpos($domain, 'google.com.tw') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'GOOGLE TAIWAN';
        $keywords = urldecode($regs[1]); // google taiwan
    }
    if (strpos($domain, 'google.cn') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'GOOGLE CHINA';
        $keywords = urldecode($regs[1]); // google china
    }
    if (strpos($domain, 'google.com') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'GOOGLE';
        $keywords = urldecode($regs[1]); // google
    }
    elseif (strpos($domain, 'baidu.') !== false && preg_match('/wd=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'BAIDU';
        $keywords = urldecode($regs[1]); // baidu
    }
    elseif (strpos($domain, 'baidu.') !== false && preg_match('/word=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'BAIDU';
        $keywords = urldecode($regs[1]); // baidu
    }
    elseif (strpos($domain, '114.vnet.cn') !== false && preg_match('/kw=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'CT114';
        $keywords = urldecode($regs[1]); // ct114
    }
    elseif (strpos($domain, 'iask.com') !== false && preg_match('/k=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'IASK';
        $keywords = urldecode($regs[1]); // iask
    }
    elseif (strpos($domain, 'soso.com') !== false && preg_match('/w=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'SOSO';
        $keywords = urldecode($regs[1]); // soso
    }
    elseif (strpos($domain, 'sogou.com') !== false && preg_match('/query=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'SOGOU';
        $keywords = urldecode($regs[1]); // sogou
    }
    elseif (strpos($domain, 'so.163.com') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'NETEASE';
        $keywords = urldecode($regs[1]); // netease
    }
    elseif (strpos($domain, 'yodao.com') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'YODAO';
        $keywords = urldecode($regs[1]); // yodao
    }
    elseif (strpos($domain, 'zhongsou.com') !== false && preg_match('/word=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'ZHONGSOU';
        $keywords = urldecode($regs[1]); // zhongsou
    }
    elseif (strpos($domain, 'search.tom.com') !== false && preg_match('/w=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'TOM';
        $keywords = urldecode($regs[1]); // tom
    }
    elseif (strpos($domain, 'live.com') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'MSLIVE';
        $keywords = urldecode($regs[1]); // MSLIVE
    }
    elseif (strpos($domain, 'tw.search.yahoo.com') !== false && preg_match('/p=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'YAHOO TAIWAN';
        $keywords = urldecode($regs[1]); // yahoo taiwan
    }
    elseif (strpos($domain, 'cn.yahoo.') !== false && preg_match('/p=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'YAHOO CHINA';
        $keywords = urldecode($regs[1]); // yahoo china
    }
    elseif (strpos($domain, 'yahoo.') !== false && preg_match('/p=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'YAHOO';
        $keywords = urldecode($regs[1]); // yahoo
    }
    elseif (strpos($domain, 'msn.com.tw') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'MSN TAIWAN';
        $keywords = urldecode($regs[1]); // msn taiwan
    }
    elseif (strpos($domain, 'msn.com.cn') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'MSN CHINA';
        $keywords = urldecode($regs[1]); // msn china
    }
    elseif (strpos($domain, 'msn.com') !== false && preg_match('/q=([^&]*)/i', $path, $regs))
    {
        $searchengine = 'MSN';
        $keywords = urldecode($regs[1]); // msn
    }

    if (!empty($keywords))
    {
        $gb_search = array('YAHOO CHINA', 'TOM', 'ZHONGSOU', 'NETEASE', 'SOGOU', 'SOSO', 'IASK', 'CT114', 'BAIDU');
        if (EC_CHARSET == 'utf-8' && in_array($searchengine, $gb_search))
        {
            $keywords = ecs_iconv('GBK', 'UTF8', $keywords);
        }
        if (EC_CHARSET == 'gbk' && !in_array($searchengine, $gb_search))
        {
            $keywords = ecs_iconv('UTF8', 'GBK', $keywords);
        }

        $GLOBALS['db']->autoReplace($GLOBALS['ecs']->table('keywords'), array('date' => local_date('Y-m-d'), 'searchengine' => $searchengine, 'keyword' => addslashes($keywords), 'count' => 1), array('count' => 1));
    }
}

/**
 * 鑾峰緱鎸囧畾鐢ㄦ埛銆佸晢鍝佺殑鎵€鏈夋爣璁
 *
 * @access  public
 * @param   integer $goods_id
 * @param   integer $user_id
 * @return  array
 */
function get_tags($goods_id = 0, $user_id = 0)
{
    $where = '';
    if ($goods_id > 0)
    {
        $where .= " goods_id = '$goods_id'";
    }

    if ($user_id > 0)
    {
        if ($goods_id > 0)
        {
            $where .= " AND";
        }
        $where .= " user_id = '$user_id'";
    }

    if ($where > '')
    {
        $where = ' WHERE' . $where;
    }

    $sql = 'SELECT tag_id, user_id, tag_words, COUNT(tag_id) AS tag_count' .
            ' FROM ' . $GLOBALS['ecs']->table('tag') .
            "$where GROUP BY tag_words";
    $arr = $GLOBALS['db']->getAll($sql);

    return $arr;
}

/**
 * 鑾峰彇鎸囧畾涓婚?鏌愪釜妯℃澘鐨勪富棰樼殑鍔ㄦ€佹ā鍧
 *
 * @access  public
 * @param   string       $theme    妯℃澘涓婚?
 * @param   string       $tmp      妯℃澘鍚嶇О
 *
 * @return array()
 */
function get_dyna_libs($theme, $tmp)
{
    $ext = end(explode('.', $tmp));
    $tmp = basename($tmp,".$ext");
    $sql = 'SELECT region, library, sort_order, id, number, type' .
            ' FROM ' . $GLOBALS['ecs']->table('template') .
            " WHERE theme = '$theme' AND filename = '" . $tmp . "' AND type > 0 AND remarks=''".
            ' ORDER BY region, library, sort_order';
    $res = $GLOBALS['db']->getAll($sql);

    $dyna_libs = array();
    foreach ($res AS $row)
    {
        $dyna_libs[$row['region']][$row['library']][] = array(
            'id'     => $row['id'],
            'number' => $row['number'],
            'type'   => $row['type']
        );
    }

    return $dyna_libs;
}

/**
 * 鏇挎崲鍔ㄦ€佹ā鍧
 *
 * @access  public
 * @param   string       $matches    鍖归厤鍐呭?
 *
 * @return string        缁撴灉
 */
function dyna_libs_replace($matches)
{
    $key = '/' . $matches[1];

    if ($row = array_shift($GLOBALS['libs'][$key]))
    {
        $str = '';
        switch($row['type'])
        {
            case 1:
                // 鍒嗙被鐨勫晢鍝
                $str = '{assign var="cat_goods" value=$cat_goods_' .$row['id']. '}{assign var="goods_cat" value=$goods_cat_' .$row['id']. '}';
                break;
            case 2:
                // 鍝佺墝鐨勫晢鍝
                $str = '{assign var="brand_goods" value=$brand_goods_' .$row['id']. '}{assign var="goods_brand" value=$goods_brand_' .$row['id']. '}';
                break;
            case 3:
                // 鏂囩珷鍒楄〃
                $str = '{assign var="articles" value=$articles_' .$row['id']. '}{assign var="articles_cat" value=$articles_cat_' .$row['id']. '}';
                break;
            case 4:
                //骞垮憡浣
                $str = '{assign var="ads_id" value=' . $row['id'] . '}{assign var="ads_num" value=' . $row['number'] . '}';
                break;
        }
        return $str . $matches[0];
    }
    else
    {
        return $matches[0];
    }
}

/**
 * 澶勭悊涓婁紶鏂囦欢锛屽苟杩斿洖涓婁紶鍥剧墖鍚?涓婁紶澶辫触鏃惰繑鍥炲浘鐗囧悕涓虹┖锛
 *
 * @access  public
 * @param array     $upload     $_FILES 鏁扮粍
 * @param array     $type       鍥剧墖鎵€灞炵被鍒?紝鍗砫ata鐩?綍涓嬬殑鏂囦欢澶瑰悕
 *
 * @return string               涓婁紶鍥剧墖鍚
 */
function upload_file($upload, $type)
{
    if (!empty($upload['tmp_name']))
    {
        $ftype = check_file_type($upload['tmp_name'], $upload['name'], '|png|jpg|jpeg|gif|doc|xls|txt|zip|ppt|pdf|rar|docx|xlsx|pptx|');
        if (!empty($ftype))
        {
            $name = date('Ymd');
            for ($i = 0; $i < 6; $i++)
            {
                $name .= chr(mt_rand(97, 122));
            }

            $name = $_SESSION['user_id'] . '_' . $name . '.' . $ftype;

            $target = ROOT_PATH . DATA_DIR . '/' . $type . '/' . $name;
            if (!move_upload_file($upload['tmp_name'], $target))
            {
                $GLOBALS['err']->add($GLOBALS['_LANG']['upload_file_error'], 1);

                return false;
            }
            else
            {
                return $name;
            }
        }
        else
        {
            $GLOBALS['err']->add($GLOBALS['_LANG']['upload_file_type'], 1);

            return false;
        }
    }
    else
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['upload_file_error']);
        return false;
    }
}

/**
 * 鏄剧ず涓€涓?彁绀轰俊鎭
 *
 * @access  public
 * @param   string  $content
 * @param   string  $link
 * @param   string  $href
 * @param   string  $type               淇℃伅绫诲瀷锛歸arning, error, info
 * @param   string  $auto_redirect      鏄?惁鑷?姩璺宠浆
 * @return  void
 */
function show_message($content, $links = '', $hrefs = '', $type = 'info', $auto_redirect = true)
{
    assign_template();

    $msg['content'] = $content;
    if (is_array($links) && is_array($hrefs))
    {
        if (!empty($links) && count($links) == count($hrefs))
        {
            foreach($links as $key =>$val)
            {
                $msg['url_info'][$val] = $hrefs[$key];
            }
            $msg['back_url'] = $hrefs['0'];
        }
    }
    else
    {
        $link   = empty($links) ? $GLOBALS['_LANG']['back_up_page'] : $links;
        $href    = empty($hrefs) ? 'javascript:history.back()'       : $hrefs;
        $msg['url_info'][$link] = $href;
        $msg['back_url'] = $href;
    }

    $msg['type']    = $type;
    $position = assign_ur_here(0, $GLOBALS['_LANG']['sys_msg']);
    $GLOBALS['smarty']->assign('page_title', $position['title']);   // 椤甸潰鏍囬?
    $GLOBALS['smarty']->assign('ur_here',    $position['ur_here']); // 褰撳墠浣嶇疆

    if (is_null($GLOBALS['smarty']->get_template_vars('helps')))
    {
        $GLOBALS['smarty']->assign('helps', get_shop_help()); // 缃戝簵甯?姪
    }

    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);
    $GLOBALS['smarty']->assign('message', $msg);
    $GLOBALS['smarty']->display('message.dwt');

    exit;
}

/**
 * 灏嗕竴涓?舰濡?10, 10, -10, 10%鐨勫瓧涓茶浆鎹?负鐩稿簲鏁板瓧锛屽苟杩斿洖鎿嶄綔绗﹀彿
 *
 * @access  public
 * @param   string      str     瑕佹牸寮忓寲鐨勬暟鎹
 * @param   char        operate 鎿嶄綔绗﹀彿锛屽彧鑳借繑鍥炩€?鈥欐垨鈥?鈥橕
 * @return  float       value   娴?偣鏁
 */
function parse_rate_value($str, &$operate)
{
    $operate = '+';
    $is_rate = false;

    $str = trim($str);
    if (empty($str))
    {
        return 0;
    }
    if ($str[strlen($str) - 1] == '%')
    {
        $value = floatval($str);
        if ($value > 0)
        {
            $operate = '*';

            return $value / 100;
        }
        else
        {
            return 0;
        }
    }
    else
    {
        return floatval($str);
    }
}

/**
 * 閲嶆柊璁＄畻璐?墿杞︿腑鐨勫晢鍝佷环鏍硷細鐩?殑鏄?綋鐢ㄦ埛鐧诲綍鏃朵韩鍙椾細鍛樹环鏍硷紝褰撶敤鎴烽€€鍑虹櫥褰曟椂涓嶄韩鍙椾細鍛樹环鏍
 * 濡傛灉鍟嗗搧鏈変績閿€锛屼环鏍间笉鍙
 *
 * @access  public
 * @return  void
 */
function recalculate_price()
{
    /* 鍙栧緱鏈夊彲鑳芥敼鍙樹环鏍肩殑鍟嗗搧锛氶櫎閰嶄欢鍜岃禒鍝佷箣澶栫殑鍟嗗搧 */
    $sql = 'SELECT c.rec_id, c.goods_id, c.goods_attr_id, g.promote_price, g.promote_start_date, c.goods_number,'.
                "g.promote_end_date, IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS member_price ".
            'FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c '.
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = c.goods_id '.
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '" . $_SESSION['user_rank'] . "' ".
            "WHERE session_id = '" .SESS_ID. "' AND c.parent_id = 0 AND c.is_gift = 0 AND c.goods_id > 0 " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "' AND c.extension_code <> 'package_buy'";

            $res = $GLOBALS['db']->getAll($sql);

    foreach ($res AS $row)
    {
        $attr_id    = empty($row['goods_attr_id']) ? array() : explode(',', $row['goods_attr_id']);


        $goods_price = get_final_price($row['goods_id'], $row['goods_number'], true, $attr_id);


        $goods_sql = "UPDATE " .$GLOBALS['ecs']->table('cart'). " SET goods_price = '$goods_price' ".
                     "WHERE goods_id = '" . $row['goods_id'] . "' AND session_id = '" . SESS_ID . "' AND rec_id = '" . $row['rec_id'] . "'";

        $GLOBALS['db']->query($goods_sql);
    }

    /* 鍒犻櫎璧犲搧锛岄噸鏂伴€夋嫨 */
    $GLOBALS['db']->query('DELETE FROM ' . $GLOBALS['ecs']->table('cart') .
        " WHERE session_id = '" . SESS_ID . "' AND is_gift > 0");
}

/**
 * 鏌ヨ?璇勮?鍐呭?
 *
 * @access  public
 * @params  integer     $id
 * @params  integer     $type
 * @params  integer     $page
 * @return  array
 */
function assign_comment($id, $type, $page = 1)
{
    /* 鍙栧緱璇勮?鍒楄〃 */
    $count = $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('comment').
           " WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0");
    $size  = !empty($GLOBALS['_CFG']['comments_number']) ? $GLOBALS['_CFG']['comments_number'] : 5;

    $page_count = ($count > 0) ? intval(ceil($count / $size)) : 1;

    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('comment') .
            " WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0".
            ' ORDER BY comment_id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);

    $arr = array();
    $ids = '';
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ids .= $ids ? ",$row[comment_id]" : $row['comment_id'];
        $arr[$row['comment_id']]['id']       = $row['comment_id'];
        $arr[$row['comment_id']]['email']    = $row['email'];
        $arr[$row['comment_id']]['username'] = $row['user_name'];
        $arr[$row['comment_id']]['content']  = str_replace('\r\n', '<br />', htmlspecialchars($row['content']));
        $arr[$row['comment_id']]['content']  = str_replace('\n', '<br />', $arr[$row['comment_id']]['content']);
        $arr[$row['comment_id']]['rank']     = $row['comment_rank'];
        $arr[$row['comment_id']]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
    }
    /* 鍙栧緱宸叉湁鍥炲?鐨勮瘎璁 */
    if ($ids)
    {
        $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('comment') .
                " WHERE parent_id IN( $ids )";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetch_array($res))
        {
            $arr[$row['parent_id']]['re_content']  = str_replace('\n', '<br />', htmlspecialchars($row['content']));
            $arr[$row['parent_id']]['re_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
            $arr[$row['parent_id']]['re_email']    = $row['email'];
            $arr[$row['parent_id']]['re_username'] = $row['user_name'];
        }
    }
    /* 鍒嗛〉鏍峰紡 */
    //$pager['styleid'] = isset($GLOBALS['_CFG']['page_style'])? intval($GLOBALS['_CFG']['page_style']) : 0;
    $pager['page']         = $page;
    $pager['size']         = $size;
    $pager['record_count'] = $count;
    $pager['page_count']   = $page_count;
    $pager['page_first']   = "javascript:gotoPage(1,$id,$type)";
    $pager['page_prev']    = $page > 1 ? "javascript:gotoPage(" .($page-1). ",$id,$type)" : 'javascript:;';
    $pager['page_next']    = $page < $page_count ? 'javascript:gotoPage(' .($page + 1) . ",$id,$type)" : 'javascript:;';
    $pager['page_last']    = $page < $page_count ? 'javascript:gotoPage(' .$page_count. ",$id,$type)"  : 'javascript:;';

    $cmt = array('comments' => $arr, 'pager' => $pager);

    return $cmt;
}

function assign_template($ctype = '', $catlist = array())
{
    global $smarty;
    assignKefu();
    $smarty->assign('enable_kefu',   $GLOBALS['_CFG']['enable_kefu']);
    $smarty->assign('image_width',   $GLOBALS['_CFG']['image_width']);
    $smarty->assign('image_height',  $GLOBALS['_CFG']['image_height']);
    $smarty->assign('points_name',   $GLOBALS['_CFG']['integral_name']);
    //$smarty->assign('qq',            explode(',', $GLOBALS['_CFG']['qq']));
    //$smarty->assign('ww',            explode(',', $GLOBALS['_CFG']['ww']));
    //$smarty->assign('ym',            explode(',', $GLOBALS['_CFG']['ym']));
    //$smarty->assign('msn',           explode(',', $GLOBALS['_CFG']['msn']));
    //$smarty->assign('skype',         explode(',', $GLOBALS['_CFG']['skype']));
    $smarty->assign('stats_code',    $GLOBALS['_CFG']['stats_code']);
    $smarty->assign('copyright',     sprintf($GLOBALS['_LANG']['copyright'], date('Y'), $GLOBALS['_CFG']['shop_name']));
    $smarty->assign('shop_name',     $GLOBALS['_CFG']['shop_name']);
    $smarty->assign('service_email', $GLOBALS['_CFG']['service_email']);
    $smarty->assign('service_phone', $GLOBALS['_CFG']['service_phone']);
    $smarty->assign('shop_address',  $GLOBALS['_CFG']['shop_address']);
    $smarty->assign('licensed',      license_info());
    $smarty->assign('ecs_version',   VERSION);
    $smarty->assign('icp_number',    $GLOBALS['_CFG']['icp_number']);
    $smarty->assign('username',      !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : '');
    $smarty->assign('category_list', cat_list(0, 0, true,  2, false));
    $smarty->assign('catalog_list',  cat_list(0, 0, false, 1, false));
    $smarty->assign('navigator_list',        get_navigator($ctype, $catlist));  //鑷?畾涔夊?鑸?爮

    if (!empty($GLOBALS['_CFG']['search_keywords']))
    {
        $searchkeywords = explode(',', trim($GLOBALS['_CFG']['search_keywords']));
    }
    else
    {
        $searchkeywords = array();
    }
    $smarty->assign('searchkeywords', $searchkeywords);
}
//鑾峰彇鍦ㄧ嚎瀹㈡湇 鍚屾椂鍏煎?鑰佺増鏈 author:doudou
function assignKefu()
{
    global $smarty;
    global $kefu_list;
    $smarty->assign('kfstyle',   $kefu_list[$GLOBALS['_CFG']['kefu']]['code']);
    $kefu_qq_tem=array();
    $kefu_ww_tem=array();
    $kefu_msn_tem=array();
    $kefu_skype_tem=array();
    $kefu_ym_tem=array();
    //澶勭悊qq瀹㈡湇
    $kefu_qq_tem=@explode(',', $GLOBALS['_CFG']['qq']);
    if(is_array($kefu_qq_tem) && !empty($kefu_qq_tem['0']))
    {
       $i=0;
       $kefu=array();
       $kefu_tem=array();
       foreach ($kefu_qq_tem AS $key=>$value)
       {
                $arr=array();
                $arr=explode(':',$value,2);
                $i=$key+1;
                $arr['1']=empty($arr['1']) ? '鍦ㄧ嚎瀹㈡湇'.$i : $arr['1'];
                $kefu_tem[$key]=$arr;
                $kefu[$key] =$arr['0'];
       }
       $smarty->assign('qq',            $kefu);
       $smarty->assign('kefuqq',    $kefu_tem);
    }
    $kefu_ww_tem=@explode(',', $GLOBALS['_CFG']['ww']);
    if(is_array($kefu_ww_tem) && !empty($kefu_ww_tem['0']))
    {
       $i=0;
       $kefu=array();
       $kefu_tem=array();
       foreach ($kefu_ww_tem AS $key=>$value)
       {
                $arr=array();
                $arr=explode(':',$value,2);
                $i=$key+1;
                $arr['1']=empty($arr['1']) ? '鍦ㄧ嚎瀹㈡湇'.$i : $arr['1'];
                $kefu_tem[$key]=$arr;
                $kefu[$key] =$arr['0'];
       }
       $smarty->assign('ww',            $kefu);
       $smarty->assign('kefuww',    $kefu_tem);
    }
    $kefu_msn_tem=@explode(',', $GLOBALS['_CFG']['msn']);
    if(is_array($kefu_msn_tem) && !empty($kefu_msn_tem['0']))
    {
       $i=0;
       $kefu=array();
       $kefu_tem=array();
       foreach ($kefu_msn_tem AS $key=>$value)
       {
                $arr=array();
                $arr=explode(':',$value,2);
                $i=$key+1;
                $arr['1']=empty($arr['1']) ? '鍦ㄧ嚎瀹㈡湇'.$i : $arr['1'];
                $kefu_tem[$key]=$arr;
                $kefu[$key] =$arr['0'];
       }
       $smarty->assign('msn',            $kefu);
       $smarty->assign('kefumsn',    $kefu_tem);
    }
    $kefu_skype_tem=@explode(',', $GLOBALS['_CFG']['skype']);
    if(is_array($kefu_skype_tem) && !empty($kefu_skype_tem['0']))
    {
       $i=0;
       $kefu=array();
       $kefu_tem=array();
       foreach ($kefu_skype_tem AS $key=>$value)
       {
                $arr=array();
                $arr=explode(':',$value,2);
                $i=$key+1;
                $arr['1']=empty($arr['1']) ? '鍦ㄧ嚎瀹㈡湇'.$i : $arr['1'];
                $kefu_tem[$key]=$arr;
                $kefu[$key] =$arr['0'];
       }
       $smarty->assign('skype',            $kefu);
       $smarty->assign('kefuskype',    $kefu_tem);
    }
    $kefu_ym_tem=@explode(',', $GLOBALS['_CFG']['ym']);
    if(is_array($kefu_ym_tem) && !empty($kefu_ym_tem['0']))
    {
       $i=0;
       $kefu=array();
       $kefu_tem=array();
       foreach ($kefu_ym_tem AS $key=>$value)
       {
                $arr=array();
                $arr=explode(':',$value,2);
                $i=$key+1;
                $arr['1']=empty($arr['1']) ? '鍦ㄧ嚎瀹㈡湇'.$i : $arr['1'];
                $kefu_tem[$key]=$arr;
                $kefu[$key] =$arr['0'];
       }
       $smarty->assign('ym',            $kefu);
       $smarty->assign('kefuym',    $kefu_tem);
    }
}

/**
 * 灏嗕竴涓?湰鍦版椂闂存埑杞?垚GMT鏃堕棿鎴
 *
 * @access  public
 * @param   int     $time
 *
 * @return int      $gmt_time;
 */
function time2gmt($time)
{
    return strtotime(gmdate('Y-m-d H:i:s', $time));
}

/**
 * 鏌ヨ?浼氬憳鐨勭孩鍖呴噾棰
 *
 * @access  public
 * @param   integer     $user_id
 * @return  void
 */
function get_user_bonus($user_id = 0)
{
    if ($user_id == 0)
    {
        $user_id = $_SESSION['user_id'];
    }

    $sql = "SELECT SUM(bt.type_money) AS bonus_value, COUNT(*) AS bonus_count ".
            "FROM " .$GLOBALS['ecs']->table('user_bonus'). " AS ub, ".
                $GLOBALS['ecs']->table('bonus_type') . " AS bt ".
            "WHERE ub.user_id = '$user_id' AND ub.bonus_type_id = bt.type_id AND ub.order_id = 0";
    $row = $GLOBALS['db']->getRow($sql);

    return $row;
}

/**
 * 淇濆瓨鎺ㄨ崘uid
 *
 * @access  public
 * @param   void
 *
 * @return void
 * @author xuanyan
 **/
function set_affiliate()
{
    $config = unserialize($GLOBALS['_CFG']['affiliate']);
    if (!empty($_GET['u']) && $config['on'] == 1)
    {
        if(!empty($config['config']['expire']))
        {
            if($config['config']['expire_unit'] == 'hour')
            {
                $c = 1;
            }
            elseif($config['config']['expire_unit'] == 'day')
            {
                $c = 24;
            }
            elseif($config['config']['expire_unit'] == 'week')
            {
                $c = 24 * 7;
            }
            else
            {
                $c = 1;
            }
            setcookie('ecshop_affiliate_uid', intval($_GET['u']), gmtime() + 3600 * $config['config']['expire'] * $c);
        }
        else
        {
            setcookie('ecshop_affiliate_uid', intval($_GET['u']), gmtime() + 3600 * 24); // 杩囨湡鏃堕棿涓 1 澶
        }
    }
}

/**
 * 鑾峰彇鎺ㄨ崘uid
 *
 * @access  public
 * @param   void
 *
 * @return int
 * @author xuanyan
 **/
function get_affiliate()
{
    if (!empty($_COOKIE['ecshop_affiliate_uid']))
    {
        $uid = intval($_COOKIE['ecshop_affiliate_uid']);
        if ($GLOBALS['db']->getOne('SELECT user_id FROM ' . $GLOBALS['ecs']->table('users') . "WHERE user_id = '$uid'"))
        {
            return $uid;
        }
        else
        {
            setcookie('ecshop_affiliate_uid', '', 1);
        }
    }

    return 0;
}

/**
 * 鑾峰緱鎸囧畾鍒嗙被鍚岀骇鐨勬墍鏈夊垎绫讳互鍙婅?鍒嗙被涓嬬殑瀛愬垎绫
 *
 * @access  public
 * @param   integer     $cat_id     鍒嗙被缂栧彿
 * @return  array
 */
function article_categories_tree($cat_id = 0)
{
    if ($cat_id > 0)
    {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE cat_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $parent_id = 0;
    }

    /*
     鍒ゆ柇褰撳墠鍒嗙被涓?叏鏄?槸鍚︽槸搴曠骇鍒嗙被锛
     濡傛灉鏄?彇鍑哄簳绾у垎绫讳笂绾у垎绫伙紝
     濡傛灉涓嶆槸鍙栧綋鍓嶅垎绫诲強鍏朵笅鐨勫瓙鍒嗙被
    */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE parent_id = '$parent_id'";
    if ($GLOBALS['db']->getOne($sql))
    {
        /* 鑾峰彇褰撳墠鍒嗙被鍙婂叾瀛愬垎绫 */
        $sql = 'SELECT a.cat_id, a.cat_name, a.sort_order AS parent_order, a.cat_id, ' .
                    'b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order AS child_order ' .
                'FROM ' . $GLOBALS['ecs']->table('article_cat') . ' AS a ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('article_cat') . ' AS b ON b.parent_id = a.cat_id ' .
                "WHERE a.parent_id = '$parent_id' AND a.cat_type=1 ORDER BY parent_order ASC, a.cat_id ASC, child_order ASC";
    }
    else
    {
        /* 鑾峰彇褰撳墠鍒嗙被鍙婂叾鐖跺垎绫 */
        $sql = 'SELECT a.cat_id, a.cat_name, b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order ' .
                'FROM ' . $GLOBALS['ecs']->table('article_cat') . ' AS a ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('article_cat') . ' AS b ON b.parent_id = a.cat_id ' .
                "WHERE b.parent_id = '$parent_id' AND b.cat_type = 1 ORDER BY sort_order ASC";
    }
    $res = $GLOBALS['db']->getAll($sql);

    $cat_arr = array();
    foreach ($res AS $row)
    {
        $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
        $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
        $cat_arr[$row['cat_id']]['url']  = build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);

        if ($row['child_id'] != NULL)
        {
            $cat_arr[$row['cat_id']]['children'][$row['child_id']]['id']   = $row['child_id'];
            $cat_arr[$row['cat_id']]['children'][$row['child_id']]['name'] = $row['child_name'];
            $cat_arr[$row['cat_id']]['children'][$row['child_id']]['url']  = build_uri('article_cat', array('acid' => $row['child_id']), $row['child_name']);
        }
    }

    return $cat_arr;
}

/**
 * 鑾峰緱鎸囧畾鏂囩珷鍒嗙被鐨勬墍鏈変笂绾у垎绫
 *
 * @access  public
 * @param   integer $cat    鍒嗙被缂栧彿
 * @return  array
 */
function get_article_parent_cats($cat)
{
    if ($cat == 0)
    {
        return array();
    }

    $arr = $GLOBALS['db']->GetAll('SELECT cat_id, cat_name, parent_id FROM ' . $GLOBALS['ecs']->table('article_cat'));

    if (empty($arr))
    {
        return array();
    }

    $index = 0;
    $cats  = array();

    while (1)
    {
        foreach ($arr AS $row)
        {
            if ($cat == $row['cat_id'])
            {
                $cat = $row['parent_id'];

                $cats[$index]['cat_id']   = $row['cat_id'];
                $cats[$index]['cat_name'] = $row['cat_name'];

                $index++;
                break;
            }
        }

        if ($index == 0 || $cat == 0)
        {
            break;
        }
    }

    return $cats;
}

/**
 * 鍙栧緱鏌愭ā鏉挎煇搴撹?缃?殑鏁伴噺
 * @param   string      $template   妯℃澘鍚嶏紝濡俰ndex
 * @param   string      $library    搴撳悕锛屽?recommend_best
 * @param   int         $def_num    榛樿?鏁伴噺锛氬?鏋滄病鏈夎?缃?ā鏉匡紝鏄剧ず鐨勬暟閲
 * @return  int         鏁伴噺
 */
function get_library_number($library, $template = null)
{
    global $page_libs;

    if (empty($template))
    {
        $template = basename(PHP_SELF);
        $template = substr($template, 0, strrpos($template, '.'));
    }
    $template = addslashes($template);

    static $lib_list = array();

    /* 濡傛灉娌℃湁璇ユā鏉跨殑淇℃伅锛屽彇寰楄?妯℃澘鐨勪俊鎭 */
    if (!isset($lib_list[$template]))
    {
        $lib_list[$template] = array();
        $sql = "SELECT library, number FROM " . $GLOBALS['ecs']->table('template') .
                " WHERE theme = '" . $GLOBALS['_CFG']['template'] . "'" .
                " AND filename = '$template' AND remarks='' ";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $lib = basename(strtolower(substr($row['library'], 0, strpos($row['library'], '.'))));
            $lib_list[$template][$lib] = $row['number'];
        }
    }

    $num = 0;
    if (isset($lib_list[$template][$library]))
    {
        $num = intval($lib_list[$template][$library]);
    }
    else
    {
        /* 妯℃澘璁剧疆鏂囦欢鏌ユ壘榛樿?鍊 */
        include_once(ROOT_PATH . 'admin/includes/lib_template.php');
        static $static_page_libs = null;
        if ($static_page_libs == null)
        {
            $static_page_libs = $page_libs;
        }
        $lib = '/library/' . $library . '.lbi';

        $num = isset($static_page_libs[$template][$lib]) ? $static_page_libs[$template][$lib] :  3;
    }

    return $num;
}

/**
 * 鍙栧緱鑷?畾涔夊?鑸?爮鍒楄〃
 * @param   string      $type    浣嶇疆锛屽?top銆乥ottom銆乵iddle
 * @return  array         鍒楄〃
 */
function get_navigator($ctype = '', $catlist = array())
{
    $sql = 'SELECT * FROM '. $GLOBALS['ecs']->table('nav') . '
            WHERE ifshow = \'1\' ORDER BY type, vieworder';
    $res = $GLOBALS['db']->query($sql);

    $cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);

    if (intval($GLOBALS['_CFG']['rewrite']))
    {
        if(strpos($cur_url, '-'))
        {
            preg_match('/([a-z]*)-([0-9]*)/',$cur_url,$matches);
            $cur_url = $matches[1].'.php?id='.$matches[2];
        }
    }
    else
    {
        $cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);
    }

    $noindex = false;
    $active = 0;
    $navlist = array(
        'top' => array(),
        'middle' => array(),
        'bottom' => array()
    );
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $navlist[$row['type']][] = array(
            'name'      =>  $row['name'],
            'opennew'   =>  $row['opennew'],
            'url'       =>  $row['url'],
            'ctype'     =>  $row['ctype'],
            'cid'       =>  $row['cid'],
            );
    }

    /*閬嶅巻鑷?畾涔夋槸鍚﹀瓨鍦╟urrentPage*/
    foreach($navlist['middle'] as $k=>$v)
    {
        $condition = empty($ctype) ? (strpos($cur_url, $v['url']) === 0) : (strpos($cur_url, $v['url']) === 0 && strlen($cur_url) == strlen($v['url']));
        if ($condition)
        {
            $navlist['middle'][$k]['active'] = 1;
            $noindex = true;
            $active += 1;
        }
    }

    if(!empty($ctype) && $active < 1)
    {
        foreach($catlist as $key => $val)
        {
            foreach($navlist['middle'] as $k=>$v)
            {
                if(!empty($v['ctype']) && $v['ctype'] == $ctype && $v['cid'] == $val && $active < 1)
                {
                    $navlist['middle'][$k]['active'] = 1;
                    $noindex = true;
                    $active += 1;
                }
            }
        }
    }

    if ($noindex == false) {
        $navlist['config']['index'] = 1;
    }

    return $navlist;
}

/**
 * 鎺堟潈淇℃伅鍐呭?
 *
 * @return  str
 */
function license_info()
{
    if($GLOBALS['_CFG']['licensed'] > 0)
    {
        /* 鑾峰彇HOST */
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
        {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        }
        elseif (isset($_SERVER['HTTP_HOST']))
        {
            $host = $_SERVER['HTTP_HOST'];
        }
        //$license = '<a href="http://license.comsenz.com/?pid=4&host='. $host .'">Licensed</a>';
        $host = 'http://' . $host . '/';
        $license = '<a href="http://service.shopex.cn/auth.php?product=ecshop&url=' . urlencode($host) . '">Licensed</a>';
        return $license;
    }
    else
    {
        return '';
    }
}

?>