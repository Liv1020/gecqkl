<?php

Class DataTablesAction extends Action
{

    /**
     * 异步获取数据返回Json
     * @param  [string] $sTable       [表名]
     * @param  [string] $sIndexColumn [主键名]
     * @param  [array] $aColumns     [查询的字段]
     * @return [json]               []
     */
    public function get($sTable, $sIndexColumn, $aColumns)
    {
        //分页设置
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " .
                intval($_GET['iDisplayLength']);
        }

        //排序
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $aColumns[intval($_GET['iSortCol_' . $i])] . "` " .
                        ($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        //过滤
        $sWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        //单字段过滤
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
            }
        }

        //构造SQL
        $sQuery = "
				SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
				FROM   $sTable
				$sWhere
				$sOrder
				$sLimit
			";
        //执行SQL
        $db = new Model();

        $rResult = $db->query($sQuery);

        /* Data set length after filtering */
        $sQuery = "
				SELECT FOUND_ROWS()
			";
        $rResultFilterTotal = $db->query($sQuery);
        $iFilteredTotal = $rResultFilterTotal[0]['FOUND_ROWS()'];

        /* Total data set length */
        $sQuery = "
				SELECT COUNT(" . $sIndexColumn . ")
				FROM   $sTable
			";
        $rResultTotal = $db->query($sQuery);
        $iTotal = $rResultTotal[0]['COUNT(id)'];

        // 输出
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        // Return array of values
        foreach ($rResult as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == "logtime") {
                    // 格式化特殊字段
                    $row[] = date('Y-m-d H:i:s', $aRow[$aColumns[$i]]);
                } else if ($aColumns[$i] == 'logtype') {
                    $row[] = $aRow[$aColumns[$i]] == 'admin' ? '管理员操作' : '会员操作';
                } else if ($aColumns[$i] != ' ') {
                    $row[] = $aRow[$aColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo $_GET['callback'] . '(' . json_encode($output) . ');';
    }

    /**
     *
     */
    public function timetask()
    {
        $path = './APP/Conf/system.php';
        $config = include $path;
        $avg_cl = $config['avg_cl'];//每天总产量
        $start_up = $config['start_up'];//平台开始时间
        if ($start_up < time()) {
            $order = M('order');
            $yield = M("yield");
            $member = M("member");
            $sclhquqq = M('sclhquqq');
            $gyc = M("gyc");
            //获取平台总生产力
            //$zongcl=$order->field("sum(lixi) as zcl")->find();
            //$tudi=$order->where()->select();
            /*体验十天 十天后不可分配种子*/
            $sttime = 86400 * 10;
            $user = $member->where("wallet_state = 1 or (wallet_state = 0 and regdate > " . (time() - $sttime) . ")")->select();
            $u_cl = $member->field("sum(shengchanli) as ucl")->find();
            $scl_cl = $sclhquqq->where(array("end_time" => array("gt", time())))->field("sum(scl) as scl_cl")->select();
            $zongcl = $u_cl['ucl'] + $scl_cl['scl_cl'];
            $yielst = $yield->where()->select();
            //未收取的种子进入公益池
            if ($yielst) {
                foreach ($yielst as $k => $v) {
                    import('ORG.Util.BlockChain');
                    $bc = new BlockChain();
                    $bc->transaction(C('chain_address'), C('gyc_address'), $v['yield'], C('chain_address_password'));

                    $gycdata['user_id'] = $v['user_id'];
                    $gycdata['yield'] = $v['yield'];
                    $gycdata['reason'] = '贡献爱心';
                    $gycdata['time'] = date("Y-m-d");
                    $gyc->add($gycdata);
                }

            }
            $yield->where("1 = 1")->delete();//
            $fpzl = 0;
            foreach ($user as $key => $val) {
                $grcl = $sclhquqq->where(array("user_id" => $val['id'], "end_time" => array("gt", time())))->field("sum(scl) as grcl")->find();

                $td = $order->where(array("user_id" => $val['id'], "end_time" => array("gt", time())))->select();
                $grscl = $val['shengchanli'] + $grcl['grcl'];
                if (count($td)) {
                    $qyzs = 0;
                    foreach ($td as $k => $v) {
                        $lixi += $v['lixi'];
                    }
                    $qyzs = round($grscl / $lixi, 2);
                    $qyzs = $qyzs > 1 ? 1 : $qyzs;
                } else {
                    $qyzs = 0;
                }

                if ($qyzs > 0) {//权益指数>0操作分配

                    //$gerenchanliang = $avg_cl*$val['lixi']/$zongcl['zcl'];
                    $gerenchanliang = $avg_cl / $zongcl * $grscl * $qyzs;
                    $meifen = round($gerenchanliang / 10, 4);
                    //$member->where(array("id"=>$val['user_id']))->save(array('sqczcs'=>5));
                    for ($i = 0; $i < 10; $i++) {
                        $data = array("user_id" => $val['id'], "yield" => $meifen, "time" => time());
                        $fpzl += $meifen;
                        $yield->add($data);
                    }
                }
            }
            if (($avg_cl - $fpzl) > 0) {
                import('ORG.Util.BlockChain');
                $bc = new BlockChain();
                $bc->transaction(C('chain_address'), C('gyc_address'), $avg_cl - $fpzl, C('chain_address_password'));

                $gycdata1['user_id'] = 0;
                $gycdata1['yield'] = $avg_cl - $fpzl;
                $gycdata1['reason'] = '未分配出去种子自动进入公益池';
                $gycdata1['time'] = date("Y-m-d");
                $gyc->add($gycdata1);
            }
        }

    }

    /**
     * 同步种子
     */
    public function syncseed()
    {
        import('ORG.Util.BlockChain');
        $bc = new BlockChain();

        $size = 200;
        $done = 0;
        $count = M("member")->field('COUNT(*) as count')->where('wallet_code IS NOT NULL')->find();
        while ($done < $count['count']) {
            $users = M("member")->field('id,wallet_code,password,jinbi')->where('wallet_code IS NOT NULL')->limit($done, $size)->select();
            foreach ($users as $user) {
                try {
                    $wallet = $bc->findWallet($user['wallet_code'], $user['password']);
                    M("member")->where(['id' => $user['id']])->setField('jinbi', $wallet['value']);
                } catch (\Exception $e) {
                    M("member")->where(['id' => $user['id']])->setField('jinbi', 0);
                }
            }

            $done += $size;
        }
    }

    public function ceshi()
    {
        $x1 = 36.101631;
        $y1 = 103.758901;
        $x2 = 36.108809;
        $y2 = 103.731031;
        echo $this->getDistance($x1, $y1, $x2, $y2);
        $this->display();
    }

    /**
     *
     * Enter description here ... 经纬度计算距离
     * @param $lat1 经度1
     * @param $lng1 维度1
     * @param $lat2 经度2
     * @param $lng2 维度2
     */
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {

        //将角度转为狐度

        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度

        $radLat2 = deg2rad($lat2);

        $radLng1 = deg2rad($lng1);

        $radLng2 = deg2rad($lng2);

        $a = $radLat1 - $radLat2;

        $b = $radLng1 - $radLng2;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;

        return $s;

    }
}

?>