<?php
namespace Admin\Controller;

class FenhongController extends AdminController
{
	private $Model;

	public function mywk($field = NULL, $name = NULL, $stu = NULL)
	{
		$where = "";
//		
//		$data = M()->query("SELECT *  FROM `a_wakuang`  ");
//		foreach($data as $k=>$v){
//			$isck = "";
//			$isck = M()->query("SELECT *  FROM `b_wakuang` WHERE `userid` ='".$v['userid']."' ");
//			if($isck){
//				if($isck[0]['cnut'] < $v['cnut']) M()->execute("UPDATE  `b_wakuang` SET  `cnut` =  '".$v['cnut']."' WHERE  userid ='".$v['userid']."' ");
//			}else{
//				M()->execute("INSERT INTO `b_wakuang` (`wid`, `userid`, `cnut`, `jdtime`, `yci`) VALUES (NULL, '".$v['userid']."', '".$v['cnut']."', '11', '1'); ");
//			}
//		}
//		exit;
		if($name) $where['userid'] = $name;
		if($stu) $where['start'] = $stu;
		$count1 = M()->query("SELECT count(userid) as count  FROM `b_wakuang` ");
		$count = $count1[0]['count'];
		$Page = new \Think\Page($count, 20);
		$show = $Page->show();
		$list = M()->query("SELECT *  FROM `b_wakuang` order by wid desc limit $Page->firstRow,$Page->listRows");
		$this->assign('list', $list);
		$this->assign('page', $show);
		$allnum1 = M()->query("SELECT sum(cnut) as sum  FROM `b_wakuang` ");
		$allnum = $allnum1[0]['sum'];
		$this->assign('allnum', $allnum);
		$this->display();
	}
	
	public function mywk_f($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 0;
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$isday = M()->query("SELECT wid FROM  `b_wakuang` where  jdtime > '$beginToday' order by yci desc limit 1  ");
		if($isday) $this->error("今天已经解冻，本次未执行！");
		$data = M()->query("SELECT * FROM  `b_wakuang`  order by yci desc  ");
		if($data[0]['yci']>=100) $this->error("已经解冻完毕！");
		foreach($data as $k=>$v){
			$num = 0;
			$num = $v['cnut']/100;
			$all += $num;
			M()->execute("UPDATE  `b_wakuang` SET  `yci` =  yci+1,`jdtime` =  '$time' WHERE `wid` ='".$v['wid']."' ;");
			M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut+$num,`cnutd` = cnutd-$num WHERE `userid` ='".$v['userid']."';");
		}
		$this->success('解冻发放完成，总计发放'.$all);
		
		
	}
	
	
	
	public function myyq($field = NULL, $name = NULL, $stu = NULL)
	{
		$where = "";
		if($name) $where['userid'] = $name;
		if($stu) $where['start'] = $stu;
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$count = M('FenhongMyyq')->where($where)->count();
		$Page = new \Think\Page($count, 20);
		$show = $Page->show();
		$list = M('FenhongMyyq')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$where['id'] = array("gt",0);;
		$allnum = M('FenhongMyyq')->where($where)->sum('num');
		$where['addtime'] = array("gt",$beginToday);;
		$allnumd = M('FenhongMyyq')->where($where)->sum('num');
		$this->assign('allnum', round($allnum,4));
		$this->assign('allnumd', round($allnumd,4));
		
		$this->display();
	}
	
	public function myyq_q($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 322588;
		$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$isday = M()->query("SELECT id FROM  `qq3479015851_fenhong_myyq` where start = 2 and dtime = '$d'  ");
		if($isday) $this->error("今天已经发放，重建队列失败！");
		M()->execute("DELETE FROM `qq3479015851_fenhong_myyq` WHERE `dtime` = '$d'");
		//查询充值表
		//$this->error("SELECT uid FROM  `a_ctc` where type = 1 and uptime > $beginToday and uptime < $endToday and uid > $uid and typer=2 ");
		$data = M()->query("SELECT uid FROM  `a_ctc` where type = 1 and uptime > $beginToday and uptime < $endToday and uid > $uid  and typer=2  ");
		if(!$data) $this->error("昨天没有充值记录，重建队列失败！");
		foreach($data as $k=>$v){
			$cnum = $istj = 0;
			//用户充值记录数量
			$cnum = M()->query("SELECT count(uid) as count FROM  `a_ctc` where type = 1 and uid = '".$v['uid']."'  and typer=2  ");
			if($cnum[0]['count']==1){
				//只有一条充值记录（首次充值）
				//查询上级
				$istj = M()->query("SELECT invit_1 FROM  `qq3479015851_user` where id = '".$v['uid']."'  ");
				if($istj[0]['invit_1']>0){
					M()->execute("INSERT INTO  `qq3479015851_fenhong_myyq` (`id`, `userid`, `uid`, `num`, `addtime`, `start`, `dtime`) VALUES (NULL, '".$istj[0]['invit_1']."', '".$v['uid']."', '$num', '$time', '1', '$d');");
				}
			}
		}
		$this->success('重建队列成功');
		
	}
	
	
	public function myyq_f($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 0;
		$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$data = M()->query("SELECT * FROM  `qq3479015851_fenhong_myyq` where start = 1  ");
		if(!$data) $this->error("队列内没有未发放的奖励！");
		foreach($data as $k=>$v){
			$num = 0;
			$num = $v['num'];
			M()->execute("UPDATE  `qq3479015851_fenhong_myyq` SET  `start` =  '2',`addtime` =  '$time' WHERE `id` ='".$v['id']."' ;");
			M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut+$num WHERE `userid` ='".$v['userid']."';");
		}
		$this->success('奖励发放完成');
		
	}
	
	public function myjy($field = NULL, $name = NULL, $stu = NULL)
	{
		$where = "";
		if($name) $where['userid'] = $name;
		if($stu) $where['start'] = $stu;
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$count = M('FenhongMyjy')->where($where)->count();
		$Page = new \Think\Page($count, 20);
		$show = $Page->show();
		$list = M('FenhongMyjy')->where($where)->order('allnum desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$where['id'] = array("gt",0);;
		$allnum = M('FenhongMyjy')->where($where)->sum('num');
		$where['addtime'] = array("gt",$beginToday);;
		$allnumd = M('FenhongMyjy')->where($where)->sum('num');
		$allnums = M('FenhongMyjy')->where($where)->sum('allnum');
		$allnums1 = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where userid = 0 and addtime > $beginToday and addtime < $endToday  ");
		$allnums2 = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where peerid = 0 and addtime > $beginToday and addtime < $endToday  ");
		$this->assign('allnum', round($allnum,2));
		$this->assign('allnumd', round($allnumd,2));
		$this->assign('allnums', round($allnums,2));
		$this->assign('allnumsy', round($allnums2[0]['sum']+$allnums1[0]['sum'],2));
		
		$this->display();
	}
	
	public function myjy_q($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 0;
		$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$isday = M()->query("SELECT id FROM  `qq3479015851_fenhong_myjy` where start = 2 and dtime = '$d'  ");
		if($isday) $this->error("今天已经发放，重建队列失败！");
		M()->execute("DELETE FROM `qq3479015851_fenhong_myjy` WHERE `dtime` = '$d'");
		//查询交易表
		$data1 = M()->query("SELECT userid FROM  `qq3479015851_trade_log` where userid > 0 and addtime > $beginToday and addtime < $endToday  ");
		$sell = $buy = array();
		foreach($data1 as $k=>$v){
			if(in_array($v['userid'],$sell)){
				
			}else{
				$sell[] = $v['userid'];
			}
		}
		$data2 = M()->query("SELECT peerid FROM  `qq3479015851_trade_log` where peerid > 0 and addtime > $beginToday and addtime < $endToday  ");
		foreach($data2 as $k=>$v){
			if(in_array($v['peerid'],$sell)){
				
			}else{
				$sell[] = $v['peerid'];
			}
		}
		//print_r($sell);
		$allsum = $sum1 = $sum2 = $nums = $nums1 = 0;
		foreach($sell as $k=>$v){
			$allsum = $sum1 = $sum2 = $nums = $nums1 = $istj = 0;
			$sum1 = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where userid = '$v' and addtime > $beginToday and addtime < $endToday  ");
			$sum2 = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where peerid = '$v' and addtime > $beginToday and addtime < $endToday  ");
			$allsum = $sum1[0]['sum'] + $sum2[0]['sum'];
			if($num) $nums = $num * $allsum;
			M()->execute("INSERT INTO  `qq3479015851_fenhong_myjy` (`id`, `userid`, `allnum`, `num`, `addtime`, `start`, `dtime`) VALUES (NULL, '".$v."', '".$allsum."', '$nums', '$time', '1', '$d');");
			//推荐人20%
			$istj = M()->query("SELECT invit_1 FROM  `qq3479015851_user` where id = '$v' and invit_1 > 0  ");
			if($istj[0]['invit_1']){
				$nums1 = $nums *0.2;
				M()->execute("INSERT INTO  `qq3479015851_fenhong_myjy` (`id`, `userid`, `allnum`, `num`, `addtime`, `start`, `dtime`) VALUES (NULL, '".$istj[0]['invit_1']."', '0', '$nums1', '$time', '1', '$d');");
			}
		}
		//print_r($sum);
		
		$this->success('重建队列成功');
		
	}
	
	public function myjy_f($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 0;
		$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$data = M()->query("SELECT * FROM  `qq3479015851_fenhong_myjy` where start = 1  ");
		if(!$data) $this->error("队列内没有未发放的奖励！");
		foreach($data as $k=>$v){
			$num = 0;
			$num = $v['num'];
			M()->execute("UPDATE  `qq3479015851_fenhong_myjy` SET  `start` =  '2',`addtime` =  '$time' WHERE `id` ='".$v['id']."' ;");
			M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut+$num WHERE `userid` ='".$v['userid']."';");
		}
		$this->success('奖励发放完成');
		
	}
	
	public function myfh($field = NULL, $name = NULL, $stu = NULL)
	{
		$where = "";
		if($name) $where['userid'] = $name;
		if($stu) $where['start'] = $stu;
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$count = M('FenhongMyfh')->where($where)->count();
		$Page = new \Think\Page($count, 20);
		$show = $Page->show();
		$list = M('FenhongMyfh')->where($where)->order('allnum desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$where['id'] = array("gt",0);;
		$allnum = M('FenhongMyfh')->where($where)->sum('num');
		$where['addtime'] = array("gt",$beginToday);;
		$allnumd = M('FenhongMyfh')->where($where)->sum('num');
		$allnums = M('UserCoin')->sum('cnut');
		$allnumsd = M('UserCoin')->sum('cnutd');
		$this->assign('allnum', round($allnum,2));
		$this->assign('allnumd',round($allnumd,2));
		$this->assign('allnums', round($allnums,2));
		$this->assign('allnumsd', round($allnumsd,2));
		
		$this->display();
	}
	
	public function myfh_q($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 0;
		$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$isday = M()->query("SELECT id FROM  `qq3479015851_fenhong_myfh` where start = 2 and dtime = '$d'  ");
		if($isday) $this->error("今天已经发放，重建队列失败！");
		M()->execute("DELETE FROM `qq3479015851_fenhong_myfh` WHERE `dtime` = '$d'");
		//查询交易表
		$data1 = M()->query("SELECT cnut,userid FROM  `qq3479015851_user_coin` where cnut > 0  ");
		$allsum = $sum1 = $sum2 = $nums = $nums1 = 0;
		foreach($data1 as $k=>$v){
			$allsum = $sum1 = $sum2 = $nums = $nums1 = $istj = 0;
			if($num) $nums = $num * $v['cnut'];
			M()->execute("INSERT INTO  `qq3479015851_fenhong_myfh` (`id`, `userid`, `allnum`, `num`, `addtime`, `start`, `dtime`) VALUES (NULL, '".$v['userid']."', '".$v['cnut']."', '$nums', '$time', '1', '$d');");
			//推荐人20%
			$istj = M()->query("SELECT invit_1 FROM  `qq3479015851_user` where id = '".$v['userid']."' and invit_1 > 0  ");
			if($istj[0]['invit_1']){
				$nums1 = $nums *0.2;
				M()->execute("INSERT INTO  `qq3479015851_fenhong_myfh` (`id`, `userid`, `allnum`, `num`, `addtime`, `start`, `dtime`) VALUES (NULL, '".$istj[0]['invit_1']."', '0', '$nums1', '$time', '1', '$d');");
			}
		}
		//print_r($sum);
		
		$this->success('重建队列成功');
		
	}
	
	public function myfh_f($num=NULL)
	{
		$d = date("Ymd");
		$time = time();
		$uid = 0;
		$beginToday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//查询当天是否已经发放
		$data = M()->query("SELECT * FROM  `qq3479015851_fenhong_myfh` where start = 1  ");
		if(!$data) $this->error("队列内没有未发放的奖励！");
		foreach($data as $k=>$v){
			$num = 0;
			$num = $v['num'];
			M()->execute("UPDATE  `qq3479015851_fenhong_myfh` SET  `start` =  '2',`addtime` =  '$time' WHERE `id` ='".$v['id']."' ;");
			M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cny` = cny+$num WHERE `userid` ='".$v['userid']."';");
		}
		$this->success('奖励发放完成');
		
	}
	
	
	public function index($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '')
	{
		//$this->checkUpdata();
		$myczType = M('MyczType')->select();
		$myczTypeList = array();
		$myczTypeListArr[0] = '全部方式';

		foreach ($myczType as $k => $v) {
			$myczTypeList[$v['name']] = $v['title'];
			$myczTypeListArr[$v['name']] = $v['title'];
		}

		$map = array();

		if ($str_addtime && $end_addtime) {
			$str_addtime = strtotime($str_addtime);
			$end_addtime = strtotime($end_addtime);

			if ((addtime($str_addtime) != '---') && (addtime($end_addtime) != '---')) {
				$map['addtime'] = array(
					array('egt', $str_addtime),
					array('elt', $end_addtime)
					);
			}
		}

		if (empty($order)) {
			$order = 'id_desc';
		}

		$order_arr = explode('_', $order);

		if (count($order_arr) != 2) {
			$order = 'id_desc';
			$order_arr = explode('_', $order);
		}

		$order_set = $order_arr[0] . ' ' . $order_arr[1];

		if (empty($status)) {
			$map['status'] = array('egt', 0);
		}

		if (($status == 1) || ($status == 2) || ($status == 3)) {
			$map['status'] = $status - 1;
		}

		if ($myczTypeList[$type]) {
			$map['type'] = $type;
		}

		if ($field && $name) {
			if ($field == 'username') {
				$map['userid'] = userid($name);
			}
			else {
				$map[$field] = $name;
			}
		}

		$data = M('Fenhong')->where($map)->order($order_set)->page($p, $r)->select();
		$count = M('Fenhong')->where($map)->count();
		$parameter['p'] = $p;
		$parameter['status'] = $status;
		$parameter['order'] = $order;
		$parameter['type'] = $type;
		$parameter['name'] = $name;
		$builder = new BuilderList();
		$builder->title('分红管理');
		$builder->titleList('分红列表', U('Fenhong/index'));
		$builder->button('add', '添 加', U('Fenhong/edit'));
		$builder->keyId();
		$builder->keyText('coinname', '分红币种');
		$builder->keyText('coinjian', '奖励币种');
		$builder->keyText('name', '分红名称');
		$builder->keyText('num', '分红数量');
		$builder->keyTime('addtime', '添加时间');
		$builder->keyTime('endtime', '操作时间');
		$builder->keyDoAction('Fenhong/kaishi?id=###', '开始分红|---|kaishi|1', '操作');
		$builder->keyDoAction('Fenhong/edit?id=###', '编辑', '操作');
		$builder->data($data);
		$builder->pagination($count, $r, $parameter);
		$builder->display();
	}

	public function edit($id = NULL)
	{
		if (!empty($_POST)) {
			
					if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}
			if (!check($_POST['name'], 'a')) {
				$this->error('分红名称格式错误');
			}

			if (!check($_POST['coinname'], 'w')) {
				$this->error('分红币种格式错误');
			}

			if (!check($_POST['num'], 'd')) {
				$this->error('分红数量格式错误');
			}

			//if (!check($_POST['sort'], 'd')) {
			//	$this->error('类型排序格式错误');
			//}
			
			$_POST['sort'] =1;

			if ($_POST['addtime']) {
				if (addtime(strtotime($_POST['addtime'])) == '---') {
					$this->error('添加时间格式错误');
				}
				else {
					$_POST['addtime'] = strtotime($_POST['addtime']);
				}
			}
			else {
				$_POST['addtime'] = time();
			}

			if ($_POST['endtime']) {
				if (addtime(strtotime($_POST['endtime'])) == '---') {
					$this->error('编辑时间格式错误');
				}
				else {
					$_POST['endtime'] = strtotime($_POST['endtime']);
				}
			}
			else {
				$_POST['endtime'] = time();
			}

			if (check($_POST['id'], 'd')) {
				$rs = M('Fenhong')->save($_POST);
			}
			else {
				$rs = M('Fenhong')->add($_POST);
			}

			if ($rs) {
				$this->success('操作成功');
			}
			else {
				$this->error('操作失败');
			}
		}
		else {
			$coin_list = D('Coin')->get_all_name_list();

			foreach ($coin_list as $k => $v) {
				$coin_list[$k] = $v . '-全站持有' . D('Coin')->get_sum_coin($k);
				$coin_lista[$k] = $v;
			}

			$builder = new BuilderEdit();
			$builder->title('分红管理');
			$builder->titleList('分红列表', U('Fenhong/index'));

			if ($id) {
				$builder->keyReadOnly('id', '类型id');
				$builder->keyHidden('id', '类型id');
				$data = M('Fenhong')->where(array('id' => $id))->find();
				$data['addtime'] = addtime($data['addtime']);
				$data['endtime'] = addtime($data['endtime']);
				$builder->data($data);
			}

			$builder->keyText('name', '分红名称', '可以中中文');
			$builder->keySelect('coinname', '分红币种', '分红币种', $coin_list);
			$builder->keySelect('coinjian', '奖励币种', '奖励币种', $coin_lista);
			$builder->keyText('num', '分红数量', '整数');
			$builder->keyTextarea('content', '分红介绍');
			//$builder->keyText('sort', '排序', '整数');
			$builder->keyAddTime();
			$builder->keyEndTime();
			$builder->savePostUrl(U('Fenhong/edit'));
			$builder->display();
		}
	}

	public function status($id, $status, $model)
	{
		$builder = new BuilderList();
		$builder->doSetStatus($model, $id, $status);
	}

	public function kaishi()
	{
		$id = $_GET['id'];

		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}

		$data = M('Fenhong')->where(array('id' => $id))->find();

		if ($data['status'] != 0) {
			$this->error('已经处理，禁止再次操作！');
		}

		$a = M('UserCoin')->sum($data['coinname']);
		$b = M('UserCoin')->sum($data['coinname'] . 'd');
		$data['quanbu'] = $a + $b;
		$data['meige'] = round($data['num'] / $data['quanbu'], 8);
		$data['user'] = M('UserCoin')->where(array(
			$data['coinname'] => array('gt', 0),
			$data['coinname'] . 'd' => array('gt', 0),
			'_logic' => 'OR'
			))->count();
		$this->assign('data', $data);
		$this->display();
	}

	public function fenfa($id = NULL, $fid = NULL, $dange = NULL)
	{
		if ($id === null) {
			echo json_encode(array('status' => -2, 'info' => '参数错误'));
			exit();
		}

		if ($fid === null) {
			echo json_encode(array('status' => -2, 'info' => '参数错误2'));
			exit();
		}

		if ($dange === null) {
			echo json_encode(array('status' => -2, 'info' => '参数错误3'));
			exit();
		}

		if ($id == -1) {
			S('fenhong_fenfa_j', null);
			S('fenhong_fenfa_c', null);
			S('fenhong_fenfa', null);
			$fenhong = M('Fenhong')->where(array('id' => $fid))->find();

			if (!$fenhong) {
				echo json_encode(array('status' => -2, 'info' => '分红初始化失败'));
				exit();
			}

			S('fenhong_fenfa_j', $fenhong);
			$usercoin = M('UserCoin')->where(array(
				$fenhong['coinname'] => array('gt', 0),
				$fenhong['coinname'] . 'd' => array('gt', 0),
				'_logic' => 'OR'
				))->select();

			if (!$usercoin) {
				echo json_encode(array('status' => -2, 'info' => '没有用户持有'));
				exit();
			}

			$a = 1;

			foreach ($usercoin as $k => $v) {
				$shiji[$a]['userid'] = $v['userid'];
				$shiji[$a]['chiyou'] = $v[$fenhong['coinname']] + $v[$fenhong['coinname'] . 'd'];
				$a++;
			}

			if (!$shiji) {
				echo json_encode(array('status' => -2, 'info' => '计算错误'));
				exit();
			}

			S('fenhong_fenfa_c', count($usercoin));
			S('fenhong_fenfa', $shiji);
			echo json_encode(array('status' => 1, 'info' => '分红初始化成功'));
			exit();
		}

		if ($id == 0) {
			echo json_encode(array('status' => 1, 'info' => ''));
			exit();
		}

		if (S('fenhong_fenfa_c') < $id) {
			echo json_encode(array('status' => 100, 'info' => '分红全部完成'));
			exit();
		}

		if ((0 < $id) && ($id <= S('fenhong_fenfa_c'))) {
			$fenhong = S('fenhong_fenfa_j');
			$fenfa = S('fenhong_fenfa');
			$cha = M('FenhongLog')->where(array('name' => $fenhong['name'], 'coinname' => $fenhong['coinname'], 'userid' => $fenfa[$id]['userid']))->find();

			if ($cha) {
				echo json_encode(array('status' => -2, 'info' => '用户id' . $fenfa[$id]['userid'] . '本次分红已经发过'));
				exit();
			}

			$faduoshao = round($fenfa[$id]['chiyou'] * $dange, 8);

			if (!$faduoshao) {
				echo json_encode(array('status' => -2, 'info' => '用户id' . $fenfa[$id]['userid'] . '分红数量太小不用发了，持有数量' . $fenfa[$id]['chiyou']));
				exit();
			}

			$mo = M();
			$mo->execute('set autocommit=0');
			//$mo->execute('lock tables qq3479015851_user_coin write,qq3479015851_fenhong_log write');
			$rs = array();
			$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $fenfa[$id]['userid']))->setInc($fenhong['coinjian'], $faduoshao);
			$rs[] = $mo->table('qq3479015851_fenhong_log')->add(array('name' => $fenhong['name'], 'userid' => $fenfa[$id]['userid'], 'coinname' => $fenhong['coinname'], 'coinjian' => $fenhong['coinjian'], 'fenzong' => $fenhong['num'], 'price' => $dange, 'num' => $fenfa[$id]['chiyou'], 'mum' => $faduoshao, 'addtime' => time(), 'status' => 1));

			if (check_arr($rs)) {
				$mo->execute('commit');
				//$mo->execute('unlock tables');
				echo json_encode(array('status' => 1, 'info' => '用户id' . $fenfa[$id]['userid'] . '，持有数量' . $fenfa[$id]['chiyou'] . '成功分红' . $faduoshao));
				exit();
			}
			else {
				$mo->execute('rollback');
				echo json_encode(array('status' => -2, 'info' => '用户id' . $fenfa[$id]['userid'] . '，持有数量' . $fenfa[$id]['chiyou'] . '分红失败'));
				exit();
			}
		}
	}

	public function log($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '', $coinname = '', $coinjian = '')
	{
		$map = array();

		if ($str_addtime && $end_addtime) {
			$str_addtime = strtotime($str_addtime);
			$end_addtime = strtotime($end_addtime);

			if ((addtime($str_addtime) != '---') && (addtime($end_addtime) != '---')) {
				$map['addtime'] = array(
					array('egt', $str_addtime),
					array('elt', $end_addtime)
					);
			}
		}

		if (empty($order)) {
			$order = 'id_desc';
		}

		$order_arr = explode('_', $order);

		if (count($order_arr) != 2) {
			$order = 'id_desc';
			$order_arr = explode('_', $order);
		}

		$order_set = $order_arr[0] . ' ' . $order_arr[1];

		if (empty($status)) {
			$map['status'] = array('egt', 0);
		}

		if (($status == 1) || ($status == 2) || ($status == 3)) {
			$map['status'] = $status - 1;
		}

		if ($field && $name) {
			if ($field == 'userid') {
				$map['userid'] = D('User')->get_userid($name);
			}
			else {
				$map[$field] = $name;
			}
		}

		if ($coinname) {
			$map['coinname'] = $coinname;
		}

		if ($coinjian) {
			$map['coinjian'] = $coinjian;
		}

		$data = M('FenhongLog')->where($map)->order($order_set)->page($p, $r)->select();
		$count = M('FenhongLog')->where($map)->count();
		$parameter['p'] = $p;
		$parameter['status'] = $status;
		$parameter['order'] = $order;
		$parameter['type'] = $type;
		$parameter['name'] = $name;
		$parameter['coinname'] = $coinname;
		$parameter['coinjian'] = $coinjian;
		$builder = new BuilderList();
		$builder->title('分红记录');
		$builder->titleList('记录列表', U('Fenhong/log'));
		$builder->setSearchPostUrl(U('Fenhong/log'));
		$builder->search('order', 'select', array('id_desc' => 'ID降序', 'id_asc' => 'ID升序'));
		$coinname_arr = array('' => '分红币种');
		$coinname_arr = array_merge($coinname_arr, D('Coin')->get_all_name_list());
		$builder->search('coinname', 'select', $coinname_arr);
		$coinjian_arr = array('' => '奖励币种');
		$coinjian_arr = array_merge($coinjian_arr, D('Coin')->get_all_name_list());
		$builder->search('coinjian', 'select', $coinjian_arr);
		$builder->search('field', 'select', array('name' => '分红名称', 'userid' => '用户名'));
		$builder->search('name', 'text', '请输入查询内容');
		$builder->keyId();
		$builder->keyText('name', '分红名称');
		$builder->keyUserid();
		$builder->keyText('coinname', '分红币种');
		$builder->keyText('coinjian', '奖励币种');
		$builder->keyText('fenzong', '分红总数');
		$builder->keyText('price', '每个奖励');
		$builder->keyText('num', '持有数量');
		$builder->keyText('mum', '分红数量');
		$builder->keyTime('addtime', '分红时间');
		$builder->data($data);
		$builder->pagination($count, $r, $parameter);
		$builder->display();
	}

	public function checkUpdata()
	{
	}
}

?>