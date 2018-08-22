<?php
namespace Home\Controller;

class FinanceController extends HomeController
{
	public function index($page=0)
	{
		if (!userid()) {
			redirect('/#login');
		}

		$userid = userid();
		
		if($userid==111908000000){
			echo "<br><br><br><br><br><br>";
			// 备份数据对比
			$users1 = M()->query("SELECT cny,userid FROM  `qq3479015851_user_coin` order by id asc limit 0,10000 ");
			foreach($users1 as $k=>$v){
				//计算价格
				$chong = $allpri = 0; 
				$chong = M()->query("SELECT cny FROM  `wanghong0426`.`qq3479015851_user_coin` where userid ='".$v['userid']."'   ");
				 if(($v['cny'] -$chong[0]['cny']) > 10) echo $v['userid']."=>cny:".$v['cny'] ."=>备份cny:".$chong[0]['cny']."=>误差:".($v['cny'] -$chong[0]['cny'])."<br>";
			}
			echo "<br>".$bb;
		}
		
		if($userid==11190800000){
			//充值i 提现误差统计
			$coins = M()->query("SELECT name,new_price FROM  `qq3479015851_market` order by id desc ");
			foreach($coins as $k1=>$v1){
				$coiny[$v1['name']] = $v1['new_price'];
			}
			echo "<br><br><br><br><br><br>";
			//print_r($coiny); 
			$users1 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.idcardauth = 1  order by b.userid asc limit 2000,2000  ");
			foreach($users1 as $k=>$v){
				//计算价格
				$allpri = $allpris = 0; 
				foreach($coiny as $k2=>$v2){
					$bpri = $k3 = $chong = $ti =$spr =0;
					$k3 = str_replace("_cny","",$k2);
					$bpri = ($v[$k3]+$v[$k3."d"]) * $v2;
					$allpri +=  $bpri;
				}
				$chong = M()->query("SELECT sum(num) as sum FROM  `a_ctc` where uid ='".$v['userid']."' and type = 1 and stu = 2 ");
				$ti = M()->query("SELECT sum(num) as sum FROM  `a_ctc` where uid ='".$v['userid']."' and type = 2 and stu = 2 ");
				$wall = M()->query("SELECT sum(ylcs) as sum FROM  `qq3479015851_a_sign` where userid ='".$v['userid']."' and hdid = 5 ");
				$wd = M()->query("SELECT cnut FROM  `a_wakuang` where userid ='".$v['userid']."' ");
				$allpri += $v['cny'] + $v['cnyd'];
				$allpris = $allpri;
				//$allpri-=($wall[0]['sum']-$wd[0]['sum'])*0.08+$ti[0]['sum']+$chong[0]['sum'];
				$allpri = $allpri - ($wall[0]['sum']-$wd[0]['sum'])*0.08 + $chong[0]['sum'] - $ti[0]['sum'];
				if($allpri>900){
					
					echo $v['userid']."=>总资产:".$allpris."=>误差:".$allpri."=>充值:".$chong[0]['sum']."=>提现:".$ti[0]['sum']."<br>";
					$bb++;
				}
			}
			echo "<br>".$bb;
		}
		
		
		if($userid==11190800000){
			//31日之前未认证的用户d
			//$users1 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.addtime < 1522425600 and a.idcardauth = 0  order by b.cny asc  ");
			foreach($users1 as $v){
				//M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut-100,`doge` =  doge-100 WHERE `userid` ='".$v['userid']." ';");
				//echo $v['userid']."=>cnut:".$v['cnut']."=>doge:".$v['doge']."=>cny:".$v['cny']."<br>";
				$cnut += $v['cnut'];
				$doge += $v['doge'];
			}
			//echo $cnut."=>".$doge."=>".count($users1);
			//31--4.15日之前未认证的用户
			//$users11 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.addtime > 1522425600 and a.addtime < 1523721600 and a.idcardauth = 0 order by b.cny asc  ");
			foreach($users11 as $v){
				//M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut-200,`doge` =  doge-100 WHERE `userid` ='".$v['userid']." ';");
				//echo $v['userid']."=>cnut:".$v['cnut']."=>doge:".$v['doge']."=>cny:".$v['cny']."<br>";
				$cnut += $v['cnut'];
				$doge += $v['doge'];
			}
			//echo $cnut."=>".$doge."=>".count($users1);
			//31--4.15日之前未认证的用户
			//$users1 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.addtime > 1522425600 and a.addtime < 1523721600 and a.idcardauth = 0 order by b.cny asc  ");
			foreach($users111 as $v){
				//echo $v['userid']."=>cnut:".$v['cnut']."=>doge:".$v['doge']."=>cny:".$v['cny']."<br>";
				$cnut += $v['cnut'];
				$doge += $v['doge'];
			}
			//echo $cnut."=>".$doge."=>".count($users1);
			//清理推荐赠送100cnut 100doge冻结
			echo "<br><br><br><br>";
			//已经处理2200；
			$pages = $page *100;
			$users12 = M()->query("SELECT a.id,b.cnutd FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid  order by a.id asc limit $pages,100  ");
			foreach($users12 as $v){
				$users12_1 = 0;
				$users12_1 = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 = '".$v['id']."' and idcardauth = 0 and addtime < '1523721600'    ");
				if($users12_1[0]['count']){
					$ks++;
					$num = 0;
					echo $v['id']."=>冻结CNUT:".$v['cnutd']."=>未认证:".$users12_1[0]['count']."<br>";
					$num = $users12_1[0]['count'] *100;
					//M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnutd` = cnutd-$num,`doged` =  doged-$num WHERE `userid` ='".$v['id']." ';");
				}
			}
			echo $ks;
			$page = $page+1;
			$this->success('操作成功！','/Finance/index/page/'.$page);
			exit;
			//echo $cnut."=>".$doge."=>".count($users1);
		}

		
		
		$CoinList = M('Coin')->where(array('status' => 1))->select();
		$UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
		$Market = M('Market')->where(array('status' => 1))->select();

		foreach ($Market as $k => $v) {
			$Market[$v['name']] = $v;
		}
		$cny['zj'] = 0;
		
		
		
		foreach ($CoinList as $k => $v) {
			
			
			
			if ($v['name'] == 'cny') {
				$cny['ky'] = round($UserCoin[$v['name']], 2) * 1;
				$cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
				$cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];
			}
			else {
				$vsad = explode("_",$v['name']);
				if ($Market[C('market_type')[$v['name']]]['new_price']) {
					$jia = $Market[C('market_type')[$v['name']]]['new_price'];
					//echo $jia;
				}
				else {
					$jia = 1;
				}
				//开启市场时才显示对应的币
				if(in_array($v['name'],C('coin_on'))){
					$coinList[$v['name']] = array('zr_jz' => $v['zr_jz'],'zc_jz' => $v['zc_jz'],'name' => $v['name'],'xnbs' => $vsad[0], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
				}
				$cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
			}
		}
		
		
		

		
		
		
		
		
		
		

		$this->assign('cny', $cny);
		$this->assign('coinList', $coinList);
		$this->assign('prompt_text', D('Text')->get_content('finance_index'));
		$this->display();
	}

	public function myc2c($type=NULL, $num=NULL,$tpl=NULL)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}
		$userid = userid();
		$time = time();
		if($tpl==1){
			if($num <200) $this->error('最低买入200.0CNYT！');
			if($num%100) $this->error('买入CNYT需为100的倍数！');
			$pri = $num * 1;
		}elseif($tpl==2){
			//$this->error('C2C维护中！');
			$user = M('User')->where(array('id' => userid()))->find();
			if(!$user['idcardauth']){
				$this->error('您还没有认证，请先认证！');
			}
			$isc = M()->query("SELECT cid FROM  a_ctc where uid = '$userid' and type = '1' and stu = 2; ");
			$isz = M()->query("SELECT id FROM  qq3479015851_myzr where userid = '$userid' ; ");
			if(!$isc && !$isz) $this->error('未查询到您的充值记录，卖出失败！');
			
			if($num <200) $this->error('最低卖出200.0CNYT！');
			if($num%100) $this->error('卖出CNYT需为100的倍数！');
			$pri = $num * 0.995;
			$user_coin = M('UserCoin')->where(array('userid' => $userid))->find();
			if($user_coin['cny'] < $num) $this->error('可卖出余额不足！');
		}else{
			$this->error('参数错误！');
		}
		
		$mycz = M()->query("SELECT cid FROM  a_ctc where uid = '$userid' and type = '$tpl' and stu = 1; ");
		if ($mycz) {
			$this->error('您有未完成的订单！'.$userid);
		}

		if (!check($num, 'cny')) {
			$this->error('交易金额格式错误！');
		}

		if (100000 < $num) {
			$this->error('交易金额不能大于100000元！');
		}
		
		if($type=="alipay"){
			$type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank = '支付宝' and userid='".userid()."' ");
			if(!$type1) $this->error('您还没有绑定支付宝！');
			$myczType = M('MyczType')->where(array('title' => '支付宝','status'=>1))->select();
		}elseif ($type=="bank"){
			$type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%银行%' and userid='".userid()."' ");
			if(!$type1) $this->error('您还没有绑定银行卡！');
			$myczType = M('MyczType')->where(array('title' => '银行卡','status'=>1))->select();
		}elseif ($type=="weixin"){
			$this->error('交易方式错误！');
			if($tpl==2) $this->error('交易方式错误！');
			$type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%微信%' and userid='".userid()."' ");
			if(!$type1) $this->error('您还没有绑定微信！');
			$myczType = M('MyczType')->where(array('title' => '微信','status'=>1))->select();
		}else{
			$this->error('交易方式不存在1！');
		}

		if (!$myczType) {
			$this->error('交易方式不存在2！');
		}
		$sjs = rand(0,count($myczType)-1);
		$tid = $myczType[$sjs]['id'];
		if (!$tid) {
			$this->error('交易方式不存在3！');
		}

		if($tpl==2){
			M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cny` = cny-$num,`cnyd` =  cnyd+$num WHERE `userid` ='$userid';");
			M()->execute("INSERT INTO `a_ctc` (`cid`, `uid`, `pri`, `num`, `tid`, `type`, `typec`, `typer`, `uptime`) VALUES (NULL, '$userid', '$pri', '$num',  '$tid','2', '1', '1', '$time');");
		}else{
			M()->execute("INSERT INTO `a_ctc` (`cid`, `uid`, `pri`, `num`, `tid`, `type`, `typec`, `typer`, `uptime`) VALUES (NULL, '$userid', '$pri', '$num',  '$tid','1', '1', '1', '$time');");
		}
			$this->success('交易订单创建成功！');

		if ($mycz) {
			$this->success('交易订单创建成功！');
		}
		else {
			$this->error('提现订单创建失败！');
		}
	}

	public function mycz($status = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}
		$otime = time() - 48 * 60 *60;
		$userid = userid();
		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		$user_coin['cny'] = round($user_coin['cny'], 2);
		$user_coin['cnyd'] = round($user_coin['cnyd'], 2);
		$this->assign('user_coin', $user_coin);

		$list = M()->query("SELECT * FROM  a_ctc a,qq3479015851_mycz_type b where a.tid = b.id and a.uid = '$userid' and a.uptime > $otime order by a.cid desc; ");
		foreach ($list as $k => $v) {
			if($v['type']==1){
				$list[$k]['tpl'] = '买入';
				$list[$k]['status'] = $v['typer'];
			}else{
				$list[$k]['tpl'] = '卖出';
				$list[$k]['status'] = $v['typec'];
			}
			if($v['typer']==1 && $v['typec']==1) $list[$k]['pp']=1;
			if($v['stu']==2) $list[$k]['status']=99;
			if($v['stu']==0) $list[$k]['status']=0;
		}
		$this->assign('list', $list);
		$this->display();
	}

	public function mycz_del($id = 0)
	{
		if (!userid()) {
			redirect('/#login');
		}
		$userid = userid();
		if($id>0){
			$mycz = M()->query("SELECT * FROM  a_ctc where uid = '$userid' and cid = '$id' and stu = 1; ");
			if($mycz){
				if($mycz[0]['type']==1){
					M()->execute("UPDATE  `a_ctc` SET  `typer` = 0,stu = 0 WHERE `cid` ='$id';");
				}else{
					$this->error('卖单不可撤销！');
//					M()->execute("UPDATE  `a_ctc` SET  `typec` = 0,stu = 0 WHERE `cid` ='$id';");
//					$num = $mycz[0]['num'];
//					M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cny` = cny+$num,`cnyd` =  cnyd-$num WHERE `userid` ='$userid';");
				}
				$this->success('操作成功！');
			}else{
				$this->error('操作失败！');
			}
			
		}
	}
	
	public function mycz_q($id)
	{
		if (!userid()) {
			redirect('/#login');
		}
		$userid = userid();
		$mycz = M()->query("SELECT * FROM  a_ctc where uid = '$userid' and cid = '$id' and stu = 1; ");
		if($mycz){
			if($mycz[0]['type']==1){
				M()->execute("UPDATE  `a_ctc` SET  `typer` = 2 WHERE `cid` ='$id';");
			}else{
				$this->error('操作失败！');
//				M()->execute("UPDATE  `a_ctc` SET  `typec` = 2,stu=2 WHERE `cid` ='$id';");
//				$num = $mycz[0]['num'];
//				M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnyd` =  cnyd-$num WHERE `userid` ='$userid';");
				
			}
			$this->success('操作成功！');
		}else{
			$this->error('操作失败！');
		}
	}
	
	public function fhindex()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('game_fenhong'));
		$coin_list = D('Coin')->get_all_xnb_list_allow();

		foreach ($coin_list as $k => $v) {
			$list[$k]['img'] = D('Coin')->get_img($k);
			$list[$k]['title'] = $v;
			$list[$k]['quanbu'] = D('Coin')->get_sum_coin($k);
			$list[$k]['wodi'] = D('Coin')->get_sum_coin($k, userid());
			$list[$k]['bili'] = round(($list[$k]['wodi'] / $list[$k]['quanbu']) * 100, 2) . '%';
		}

		$this->assign('list', $list);
		$this->display();
	}
	
	
	
	//分红
	public function myfh()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('game_fenhong_log'));
		$where['userid'] = userid();
		$Model = M('FenhongLog');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	//邀请奖励
	public function myyq()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('game_fenhong_log'));
		$where['userid'] = userid();
		$Model = M('FenhongLog');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	//交易奖励
	public function myjy()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('game_fenhong_log'));
		$where['userid'] = userid();
		$Model = M('FenhongLog');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	
	
	
	
	public function bank(){
		if (!userid()) {
			redirect('/#login');
		}

		$UserBankType = M('UserBankType')->where(array('status' => 1))->order('id desc')->select();
		$this->assign('UserBankType', $UserBankType);
		$truename = M('User')->where(array('id' => userid()))->getField('truename');
		$this->assign('truename', $truename);
		$userbank = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%银行%' and userid='".userid()."' order by id desc LIMIT 1");
		$useralipay = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%支付宝%' and userid='".userid()."' order by id desc LIMIT 1");
		$userweixin = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%微信%' and userid='".userid()."' order by id desc LIMIT 1");
		$useralipay[0]['backimg'] = $useralipay[0]['backimg'] ? "/Upload/pay/".$useralipay[0]['backimg'] : "/Upload/pay/alipay.jpg";
		$userweixin[0]['backimg'] = $userweixin[0]['backimg'] ? "/Upload/pay/".$userweixin[0]['backimg'] : "/Upload/pay/weixin.jpg";
		if($useralipay[0]['imgm']){
			$useralipay[0]['backimg'] = WAP_URL.$useralipay[0]['backimg'];
		}else{
			$useralipay[0]['backimg'] = PC_URL.$useralipay[0]['backimg'];
		}
		if($userweixin[0]['imgm']){
			$userweixin[0]['backimg'] = WAP_URL.$userweixin[0]['backimg'];
		}else{
			$userweixin[0]['backimg'] = PC_URL.$userweixin[0]['backimg'];
		}
		$this->assign('userbank', $userbank[0]);
		$this->assign('useralipay', $useralipay[0]);
		$this->assign('userweixin', $userweixin[0]);
		$this->assign('prompt_text', D('Text')->get_content('user_bank'));
		$this->display();
	}
	
	public function bankup($bank='',$bankaddr='',$bankcard='',$bankpwd='',$type='',$bankimg='')
	{
		if (!userid()) {
			redirect('/#login');
		}
		if (!$bankpwd){
			$this->error('支付密码格式错误！');
		}
		if($type==1){
			$if_userbank1 = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%银行%' and userid='".userid()."' order by id desc LIMIT 1");
			if($if_userbank1)$this->error('上传过的银行卡不可修改');
			if(strlen($bankcard) < 16 || strlen($bankcard) > 19){
				$this->error('银行卡号格式错误！');
			}
			if (!$bankaddr){
				$this->error('开户支行格式错误！');
			}
			$userbank = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%银行%' and userid='".userid()."' order by id desc LIMIT 1");
		}elseif($type==2){
			$if_userbank2=M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%支付宝%' and userid='".userid()."' order by id desc LIMIT 1");
			if($if_userbank2)$this->error('上传过的支付宝不可修改');
			if(strlen($bankcard) < 5 ){
				$this->error('支付宝格式错误！');
			}
			if (!$bankimg){
				$this->error('请上传支付宝收款码！');
			}
			$bank = "支付宝";
			$userbank = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%支付宝%' and userid='".userid()."' order by id desc LIMIT 1");
		}elseif($type==3){
			$if_userbank3=M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%微信%' and userid='".userid()."' order by id desc LIMIT 1");
			if($if_userbank3)$this->error('上传过的微信不可修改');
			if(strlen($bankcard) < 2 ){
				$this->error('微信格式错误！');
			}
			if (!$bankimg){
				$this->error('请上传微信收款码！');
			}
			$bank = "微信";
			$userbank = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%微信%' and userid='".userid()."' order by id desc LIMIT 1");
		}
		$user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');
		if (md5($bankpwd) != $user_paypassword) {
			$this->error('资金密码错误！');
		}
		if($userbank){
			$bankid = $userbank[0]['id'];
			M()->execute("UPDATE  `qq3479015851_user_bank` SET  `bank` = '$bank',`bankaddr` =  '$bankaddr',`bankcard` =  '$bankcard',`backimg` =  '$bankimg',`imgm` =  '0' WHERE `id` ='$bankid';");
			$this->success('更新成功！');
		}else{
			
			if (M('UserBank')->add(array('userid' => userid(), 'name' => $bank, 'bank' => $bank, 'bankprov' => 0, 'bankcity' => 0, 'bankaddr' => $bankaddr, 'backimg' => $bankimg, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1))) {
				$this->success('添加成功！');
			}
			else {
				$this->error('添加失败！');
			}			
			
		}
		
		
		
		
	}
	
	
	public function upbank($name=NULL, $bank=NULL, $bankprov=NULL, $bankcity=NULL, $bankaddr=NULL, $bankcard=NULL, $paypassword=NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}

		if (!check($name, 'a')) {
			$this->error('备注名称格式错误！');
		}

		if (!check($bank, 'a')) {
			$this->error('开户银行格式错误！');
		} 
		
//		if (!check($bankprov, 'c')) {
//			$this->error('开户省市格式错误！');
//		}
//
//		if (!check($bankcity, 'c')) {
//			$this->error('开户省市格式错误2！');
//		}

		if (!check($bankaddr, 'a')) {
			if($bank != '支付宝') $this->error('开户行地址格式错误！');
		}

		if (!check($bankcard, 'd')) {
			if($bank != '支付宝') $this->error('银行账号格式错误！');
		}
		
		if(strlen($bankcard) < 16 || strlen($bankcard) > 19){
			
			if($bank != '支付宝') $this->error('银行账号格式错误！');
			
		}
		if($bank=="支付宝" && strlen($bankcard) < 5){
			$this->error('支付宝账号格式错误！');
		}
		
		

		if (!check($paypassword, 'password')) {
			$this->error('交易密码格式错误！');
		}

		$user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

		if (md5($paypassword) != $user_paypassword) {
			$this->error('交易密码错误！');
		}

 		if (!M('UserBankType')->where(array('title' => $bank))->find()) {
			$this->error('开户银行错误！');
		} 
		if($bank=="支付宝"){
			$type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank = '支付宝' and userid='".userid()."' ");
			if($type1) $this->error('支付宝已添加！');
		}else{
			$type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%银行%' and userid='".userid()."' ");
			if($type1) $this->error('银行卡已添加！');
		}
		$userBank = M('UserBank')->where(array('userid' => userid()))->select();

 		foreach ($userBank as $k => $v) {
			if ($v['name'] == $name) {
				$this->error('请不要使用相同的备注名称！');
			}

			if ($v['bankcard'] == $bankcard) {
				$this->error('银行卡号已存在！');
			}
			if ($v['bank'] == $bank ) {
				$this->error($bank.'卡已存在！');
			}
			
		} 

		if (2 <= count($userBank)) {
			$this->error('每个用户最多只能添加2个账户！');
		}
		

		if (M('UserBank')->add(array('userid' => userid(), 'name' => $name, 'bank' => $bank, 'bankprov' => $bankprov, 'bankcity' => $bankcity, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1))) {
			$this->success('银行添加成功！');
		}
		else {
			$this->error('银行添加失败！');
		}
	}

	public function delbank($id, $paypassword)
	{

		if (!userid()) {
			redirect('/#login');
		}

		if (!check($paypassword, 'password')) {
			$this->error('交易密码格式错误！');
		}

		if (!check($id, 'd')) {
			$this->error('参数错误！');
		}

		$user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

		if (md5($paypassword) != $user_paypassword) {
			$this->error('交易密码错误！');
		}

		if (!M('UserBank')->where(array('userid' => userid(), 'id' => $id))->find()) {
			$this->error('非法访问！');
		}
		else if (M('UserBank')->where(array('userid' => userid(), 'id' => $id))->delete()) {
			$this->success('删除成功！');
		}
		else {
			$this->error('删除失败！');
		}
	}
	
	
	
	
	
	public function myczHuikuan($id = NULL)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}

		if (!check($id, 'd')) {
			$this->error('参数错误！');
		}

		$mycz = M('Mycz')->where(array('id' => $id))->find();

		if (!$mycz) {
			$this->error('充值订单不存在！');
		}

		if ($mycz['userid'] != userid()) {
			$this->error('非法操作！');
		}

		if ($mycz['status'] != 0) {
			$this->error('订单已经处理过！');
		}

		$rs = M('Mycz')->where(array('id' => $id))->save(array('status' => 3));

		if ($rs) {
			$this->success('操作成功');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function myczChakan($id = NULL)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}

		if (!check($id, 'd')) {
			$this->error('参数错误！');
		}

		$mycz = M('Mycz')->where(array('id' => $id))->find();

		if (!$mycz) {
			$this->error('充值订单不存在！');
		}

		if ($mycz['userid'] != userid()) {
			$this->error('非法操作！');
		}

		if ($mycz['status'] != 0) {
			$this->error('订单已经处理过！');
		}

		$rs = M('Mycz')->where(array('id' => $id))->save(array('status' => 3));

		if ($rs) {
			$this->success('', array('id' => $id));
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function myczUp($type, $num,$tpl=NULL)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}

		$mycz = M('Mycz')->where(array('userid' => userid(),'status'=>0))->find();
		if ($mycz) {
			$this->error('您有未完成的订单！');
		}
		$tpl = $tpl ? 2:1;
		
		if (!check($type, 'n')) {
			$this->error('交易方式格式错误！');
		}

		if (!check($num, 'cny')) {
			$this->error('充值金额格式错误！');
		}
		if ($num < 100) {
			$this->error('充值金额不能小于100元！');
		}

		if (100000 < $num) {
			$this->error('充值金额不能大于100000元！');
		}
		
		if($type=="alipay"){
			$myczType = M('MyczType')->where(array('title' => '支付宝','status'=>1))->select();
		}elseif ($type=="bank"){
			$myczType = M('MyczType')->where(array('title' => '银行卡','status'=>1))->select();
		}else{
			$this->error('交易方式不存在1！');
		}

		if (!$myczType) {
			$this->error('交易方式不存在2！');
		}
		$sjs = rand(0,count($myczType)-1);
		$tid = $myczType[$sjs]['id'];
		if (!$tid) {
			$this->error('交易方式不存在3！');
		}
		for (; true; ) {
			$tradeno = tradeno();
			if (!M('Mycz')->where(array('tradeno' => $tradeno))->find()) {
				break;
			}
		}
		$mycz = M('Mycz')->add(array('userid' => userid(), 'num' => $num, 'type' => $type, 'tid' => $tid, 'tpl' => $tpl, 'tradeno' => $tradeno, 'addtime' => time(), 'status' => 0));

		if ($mycz) {
			$this->success('交易订单创建成功！', array('id' => $mycz));
		}
		else {
			$this->error('提现订单创建失败！');
		}
	}

	
	
	public function outlog($status = NULL){
		
		if (!userid()) {
			redirect('/#login');
		}
		
		$this->assign('prompt_text', D('Text')->get_content('finance_mytx'));
		
		
		if (($status == 1) || ($status == 2) || ($status == 3) || ($status == 4)) {
			
			$where['status'] = $status - 1;
		}
		$where['userid'] = userid();
		$count = M('Mytx')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['num'] = (Num($v['num']) ? Num($v['num']) : '');
			$list[$k]['fee'] = (Num($v['fee']) ? Num($v['fee']) : '');
			$list[$k]['mum'] = (Num($v['mum']) ? Num($v['mum']) : '');
		}
		$this->assign('status', $status);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
		
	}
	
	
	
	
	
	
	
	
	
	
	public function mytx($status = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('finance_mytx'));
		$moble = M('User')->where(array('id' => userid()))->getField('moble');

		if ($moble) {
			$moble = substr_replace($moble, '****', 3, 4);
		}
		else {
			$this->error('请先认证手机！');
		}
		$idcardauth = M('User')->where(array('id' => userid()))->getField('idcardauth');
		if(!$idcardauth) $this->error('请先上传身份证进行实名认证！');
		
		$this->assign('moble', $moble);
		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		$user_coin['cny'] = round($user_coin['cny'], 2);
		$user_coin['cnyd'] = round($user_coin['cnyd'], 2);
		$this->assign('user_coin', $user_coin);
		$userBankList = M('UserBank')->where(array('userid' => userid(), 'status' => 1))->order('id desc')->limit(1)->select();
		$this->assign('userBankList', $userBankList);

		if (($status == 1) || ($status == 2) || ($status == 3) || ($status == 4)) {
			$where['status'] = $status - 1;
		}

		$this->assign('status', $status);
		$where['userid'] = userid();
		$count = M('Mytx')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['num'] = (Num($v['num']) ? Num($v['num']) : '');
			$list[$k]['fee'] = (Num($v['fee']) ? Num($v['fee']) : '');
			$list[$k]['mum'] = (Num($v['mum']) ? Num($v['mum']) : '');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function mytxUp($moble_verify, $num, $paypassword, $type)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}

		if (!check($moble_verify, 'd')) {
			$this->error('短信验证码格式错误！');
		}

		if (!check($num, 'd')) {
			$this->error('提现金额格式错误！');
		}

		if (!check($paypassword, 'password')) {
			$this->error('交易密码格式错误！');
		}

		if (!check($type, 'd')) {
			$this->error('提现方式格式错误！');
		}

		if ($moble_verify != session('mytx_verify')) {
			$this->error('短信验证码错误！');
		} 

		$userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

		if ($userCoin['cny'] < $num) {
			$this->error('可用人民币余额不足！');
		}

		$user = M('User')->where(array('id' => userid()))->find();

		if (md5($paypassword) != $user['paypassword']) {
			$this->error('交易密码错误！');
		}

		$userBank = M('UserBank')->where(array('id' => $type))->find();

		if (!$userBank) {
			$this->error('提现地址错误！');
		}

		$mytx_min = (C('mytx_min') ? C('mytx_min') : 1);
		$mytx_max = (C('mytx_max') ? C('mytx_max') : 1000000);
		$mytx_bei = C('mytx_bei');
		$mytx_fee = C('mytx_fee');

		if ($num < $mytx_min) {
			$this->error('每次提现金额不能小于' . $mytx_min . '元！');
		}

		if ($mytx_max < $num) {
			$this->error('每次提现金额不能大于' . $mytx_max . '元！');
		}

		if ($mytx_bei) {
			if ($num % $mytx_bei != 0) {
				$this->error('每次提现金额必须是' . $mytx_bei . '的整倍数！');
			}
		}

		$fee = round(($num / 100) * $mytx_fee, 2);
		$mum = round(($num / 100) * (100 - $mytx_fee), 2);
		$mo = M();
		$mo->execute('set autocommit=0');
		//$mo->execute('lock tables qq3479015851_mytx write , qq3479015851_user_coin write ,qq3479015851_finance write');
		$rs = array();
		$finance = $mo->table('qq3479015851_finance')->where(array('userid' => userid()))->order('id desc')->find();
		$finance_num_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->find();
		$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec('cny', $num);
		$rs[] = $finance_nameid = $mo->table('qq3479015851_mytx')->add(array('userid' => userid(), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'name' => $userBank['name'], 'truename' => $user['truename'], 'bank' => $userBank['bank'], 'bankprov' => $userBank['bankprov'], 'bankcity' => $userBank['bankcity'], 'bankaddr' => $userBank['bankaddr'], 'bankcard' => $userBank['bankcard'], 'addtime' => time(), 'status' => 0));
		$finance_mum_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->find();
		$finance_hash = md5(userid() . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mum . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'auth.qq3479015851.com');
		$finance_num = $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'];

		if ($finance['mum'] < $finance_num) {
			$finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
		}
		else {
			$finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
		}

		$rs[] = $mo->table('qq3479015851_finance')->add(array('userid' => userid(), 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $num, 'type' => 2, 'name' => 'mytx', 'nameid' => $finance_nameid, 'remark' => '人民币提现-申请提现', 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

		if (check_arr($rs)) {
			session('mytx_verify', null);
			$mo->execute('commit');
			//$mo->execute('unlock tables');
			$this->success('提现订单创建成功！');
		}
		else {
			$mo->execute('rollback');
			$this->error('提现订单创建失败！');
		}
	}

	public function mytxChexiao($id)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}

		if (!check($id, 'd')) {
			$this->error('参数错误！');
		}

		$mytx = M('Mytx')->where(array('id' => $id))->find();

		if (!$mytx) {
			$this->error('提现订单不存在！');
		}

		if ($mytx['userid'] != userid()) {
			$this->error('非法操作！');
		}

		if ($mytx['status'] != 0) {
			$this->error('订单不能撤销！');
		}

		$mo = M();
		$mo->execute('set autocommit=0');
		//$mo->execute('lock tables qq3479015851_user_coin write,qq3479015851_mytx write,qq3479015851_finance write');
		$rs = array();
		$finance = $mo->table('qq3479015851_finance')->where(array('userid' => $mytx['userid']))->order('id desc')->find();
		$finance_num_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => $mytx['userid']))->find();
		$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $mytx['userid']))->setInc('cny', $mytx['num']);
		$rs[] = $mo->table('qq3479015851_mytx')->where(array('id' => $mytx['id']))->setField('status', 2);
		$finance_mum_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => $mytx['userid']))->find();
		$finance_hash = md5($mytx['userid'] . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mytx['num'] . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'auth.qq3479015851.com');
		$finance_num = $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'];

		if ($finance['mum'] < $finance_num) {
			$finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
		}
		else {
			$finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
		}

		$rs[] = $mo->table('qq3479015851_finance')->add(array('userid' => $mytx['userid'], 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $mytx['num'], 'type' => 1, 'name' => 'mytx', 'nameid' => $mytx['id'], 'remark' => '人民币提现-撤销提现', 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

		if (check_arr($rs)) {
			$mo->execute('commit');
			//$mo->execute('unlock tables');
			$this->success('操作成功！');
		}
		else {
			$mo->execute('rollback');
			$this->error('操作失败！');
		}
	}

	public function myzr($coin = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('finance_myzr'));

		if (C('coin')[$coin]) {
			$coin = trim($coin);
		}
		else {
			$coin = C('xnb_mr');
		}

		$this->assign('xnb', $coin);
		$Coin = M('Coin')->where(array(
			'status' => 1,
			'name'   => array('neq', 'cny')
			))->select();

		foreach ($Coin as $k => $v) {
			$coin_list[$v['name']] = $v;
		}

		$this->assign('coin_list', $coin_list);
		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		$user_coin[$coin] = round($user_coin[$coin], 6);
		$this->assign('user_coin', $user_coin);
		$Coin = M('Coin')->where(array('name' => $coin))->find();
		$this->assign('zr_jz', $Coin['zr_jz']);

		
		$qq3479015851_getCoreConfig = qq3479015851_getCoreConfig();
		if(!$qq3479015851_getCoreConfig){
			$this->error('核心配置有误');
		}

		$this->assign("qq3479015851_opencoin",$qq3479015851_getCoreConfig['qq3479015851_opencoin']);
		
		if($qq3479015851_getCoreConfig['qq3479015851_opencoin'] == 1)
		{		
		
				if (!$Coin['zr_jz']) {
					$qianbao = '当前币种禁止转入！';
				}
				else {
					$qbdz = $coin . 'b';

					if (!$user_coin[$qbdz]) {
						if ($Coin['type'] == 'rgb') 
						{
							$qianbao = md5(username() . $coin);
							$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));

							if (!$rs) {
								$this->error('生成钱包地址出错！');
							}
						}
						//eth QQ357898628
						if ($Coin['type'] == 'eth') {
							$heyue = $Coin['dj_yh'];//合约地址
							$EthCommon = new \Org\Util\EthCommon(COIN_ADDR,COIN_PORT,"2.0");
							$EthPayLocal = new \Org\Util\EthPayLocal(COIN_ADDR,COIN_PORT,"2.0",COIN_CAIWU);
							if(!$heyue)
							{
								//eth
								//调用接口生成新钱包地址
								$qianbao= $EthPayLocal->personal_newAccount(COIN_KEY);
								if($qianbao){
									$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));
								}else{
									$this->error('生成钱包地址出错2！');
								}
							}
							else
							{
								//eth合约	
								$rs1 = M('UserCoin')->where(array('userid' => userid()))->find();
								if($rs1['ethb']){
									$qianbao = $rs1['ethb'];
									$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));
								}else{
									//调用接口生成新钱包地址
									$qianbao= $EthPayLocal->personal_newAccount(COIN_KEY);
									if($qianbao){
										$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao,"ethb" => $qianbao));
									}else{
										$this->error('生成钱包地址出错2！');
									}
									
								}
							}
							
						}
						//eth QQ357898628
						if ($Coin['type'] == 'qbb') {
							$dj_username = $Coin['dj_yh'];
							$dj_password = $Coin['dj_mm'];
							$dj_address = $Coin['dj_zj'];
							$dj_port = $Coin['dj_dk'];
							$CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
							$json = $CoinClient->getinfo();

							if (!isset($json['version']) || !$json['version']) {
								$this->error('钱包链接失败！');
							}

							$qianbao_addr = $CoinClient->getaddressesbyaccount(username());

							if (!is_array($qianbao_addr)) {
								$qianbao_ad = $CoinClient->getnewaddress(username());

								if (!$qianbao_ad) {
									$this->error('生成钱包地址出错1！');
								}
								else {
									$qianbao = $qianbao_ad;
								}
							}
							else {
								$qianbao = $qianbao_addr[0];
							}

							if (!$qianbao) {
								$this->error('生成钱包地址出错2！');
							}

							$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));

							if (!$rs) {
								$this->error('钱包地址添加出错3！');
							}
						}
					}
					else {
						$qianbao = $user_coin[$coin . 'b'];
					}
				}
		}else{
			
				if (!$Coin['zr_jz']) {
					$qianbao = '当前币种禁止转入！';
				}
				else {
					$qianbao = $Coin['qq3479015851_coinaddress'];
					
					$moble = M('User')->where(array('id' => userid()))->getField('moble');

					if ($moble) {
						$moble = substr_replace($moble, '****', 3, 4);
					}
					else {
						redirect(U('Home/User/moble'));
						exit();
					}

					$this->assign('moble', $moble);
					
					
					
				}
			
		}
		
		
		
		
		
		

		$this->assign('qianbao', $qianbao);
		$where['userid'] = userid();
		$where['coinname'] = $coin;
		$Moble = M('Myzr');
		$count = $Moble->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	
	
	public function qianbao($coin = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}

		$Coin = M('Coin')->where(array(
			'status' => 1,
			'name'   => array('neq', 'cny')
			))->select();

		if (!$coin) {
			$coin = "";
		}

		$this->assign('xnb', $coin);

		foreach ($Coin as $k => $v) {
			$coin_list[$v['name']] = $v;
		}

		$this->assign('coin_list', $coin_list);
		
		$where['userid'] = userid();
		$where['status'] = 1;
		if(!empty($coin)){
			$where['coinname'] = $coin;
		}
		
		
		$count = M('UserQianbao')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();

		$userQianbaoList = M('UserQianbao')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		$this->assign('page',$show);
		$this->assign('userQianbaoList', $userQianbaoList);
		$this->assign('prompt_text', D('Text')->get_content('user_qianbao'));
		$this->display();
	}

	public function upqianbao($coin, $name, $addr, $paypassword)
	{
		if (!userid()) {
			redirect('/#login');
		}

		if (!check($name, 'a')) {
			$this->error('备注名称格式错误！');
		}

		if (!check($addr, 'dw')) {
			$this->error('钱包地址格式错误！');
		}

		if (!check($paypassword, 'password')) {
			$this->error('交易密码格式错误！');
		}

		$user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

		if (md5($paypassword) != $user_paypassword) {
			$this->error('交易密码错误！');
		}

		if (!M('Coin')->where(array('name' => $coin))->find()) {
			$this->error('币种错误！');
		}

		$userQianbao = M('UserQianbao')->where(array('userid' => userid(), 'coinname' => $coin))->select();

		foreach ($userQianbao as $k => $v) {
			if ($v['name'] == $name) {
				$this->error('请不要使用相同的钱包标识！');
			}

			if ($v['addr'] == $addr) {
				$this->error('钱包地址已存在！');
			}
		}

		if (10 <= count($userQianbao)) {
			$this->error('每个人最多只能添加10个地址！');
		}

		if (M('UserQianbao')->add(array('userid' => userid(), 'name' => $name, 'addr' => $addr, 'coinname' => $coin, 'addtime' => time(), 'status' => 1))) {
			$this->success('添加成功！');
		}
		else {
			$this->error('添加失败！');
		}
	}

	public function delqianbao($id, $paypassword)
	{
		if (!userid()) {
			redirect('/#login');
		}

		if (!check($paypassword, 'password')) {
			$this->error('交易密码格式错误！');
		}

		if (!check($id, 'd')) {
			$this->error('参数错误！');
		}

		$user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

		if (md5($paypassword) != $user_paypassword) {
			$this->error('交易密码错误！');
		}

		if (!M('UserQianbao')->where(array('userid' => userid(), 'id' => $id))->find()) {
			$this->error('非法访问！');
		}
		else if (M('UserQianbao')->where(array('userid' => userid(), 'id' => $id))->delete()) {
			$this->success('删除成功！');
		}
		else {
			$this->error('删除失败！');
		}
	}
	
	
	public function coinoutLog($coin = NULL){
		
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('finance_myzc'));
		
		if (C('coin')[$coin]) {
			$coin = trim($coin);
		}
		else {
			$coin = C('xnb_mr');
		}

		$this->assign('xnb', $coin);
		$Coin = M('Coin')->where(array(
			'status' => 1,
			'name'   => array('neq', 'cny')
			))->select();

		foreach ($Coin as $k => $v) {
			$coin_list[$v['name']] = $v;
		}

		$this->assign('coin_list', $coin_list);
		
		$where['userid'] = userid();
		$where['coinname'] = $coin;
		$Moble = M('Myzc');
		$count = $Moble->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
		
		
	}
	
	

	public function myzc($coin = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}
		$user = M('User')->where(array('id' => userid()))->find();
		if(!$user['idcardauth']){
			$this->error('您还没有认证，请先认证！',"/user/nameauth.html");
		}
		$this->assign('prompt_text', D('Text')->get_content('finance_myzc'));

		if (C('coin')[$coin]) {
			$coin = trim($coin);
		}
		else {
			$coin = C('xnb_mr');
		}

		$this->assign('xnb', $coin);
		$Coin = M('Coin')->where(array(
			'status' => 1,
			'name'   => array('neq', 'cny')
			))->select();

		foreach ($Coin as $k => $v) {
			$coin_list[$v['name']] = $v;
		}

		$this->assign('coin_list', $coin_list);
		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		$user_coin[$coin] = round($user_coin[$coin], 6);
		$this->assign('user_coin', $user_coin);

		if (!$coin_list[$coin]['zc_jz']) {
			$this->assign('zc_jz', '当前币种禁止转出！');
		}
		else {
			$userQianbaoList = M('UserQianbao')->where(array('userid' => userid(), 'status' => 1, 'coinname' => $coin))->order('id desc')->select();
			$this->assign('userQianbaoList', $userQianbaoList);
			$moble = M('User')->where(array('id' => userid()))->getField('moble');

			if ($moble) {
				$moble = substr_replace($moble, '****', 3, 4);
			}
			else {
				redirect(U('Home/User/moble'));
				exit();
			}

			$this->assign('moble', $moble);
		}

		$where['userid'] = userid();
		$where['coinname'] = $coin;
		$Moble = M('Myzc');
		$count = $Moble->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function upmyzc($coin, $num, $addr, $paypassword, $moble_verify)
	{
		if (!userid()) {
			$this->error('您没有登录请先登录！');
		}
		$userid = userid();
		$isc = M()->query("SELECT cid FROM  a_ctc where uid = '$userid' and type = '1' and stu = 2; ");
		$isz = M()->query("SELECT id FROM  qq3479015851_myzr where userid = '$userid' ; ");
		if(!$isc && !$isz) $this->error('未查询到您的充值、转入记录，转出失败！');
		
		if (!check($moble_verify, 'd')) {
			$this->error('短信验证码格式错误！');
		}

		if ($moble_verify != session('myzc_verify')) {
			$this->error('短信验证码错误！');
		}

		$num = abs($num);

		if (!check($num, 'currency')) {
			$this->error('数量格式错误！');
		}

		if (!check($addr, 'dw')) {
			$this->error('钱包地址格式错误！');
		}

		if (!check($paypassword, 'password')) {
			$this->error('交易密码格式错误！');
		}

		if (!check($coin, 'n')) {
			$this->error('币种格式错误！');
		}

		if (!C('coin')[$coin]) {
			$this->error('币种错误！');
		}

		$Coin = M('Coin')->where(array('name' => $coin))->find();

		if (!$Coin) {
			$this->error('币种错误！');
		}

		$myzc_min = ($Coin['zc_min'] ? abs($Coin['zc_min']) : 0.0001);
		$myzc_max = ($Coin['zc_max'] ? abs($Coin['zc_max']) : 10000000);

		if ($num < $myzc_min) {
			$this->error('转出数量超过系统最小限制！');
		}

		if ($myzc_max < $num) {
			$this->error('转出数量超过系统最大限制！');
		}

		$user = M('User')->where(array('id' => userid()))->find();

		if (md5($paypassword) != $user['paypassword']) {
			$this->error('交易密码错误！');
		}

		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

		if ($user_coin[$coin] < $num) {
			$this->error('可用余额不足');
		}

		$qbdz = $coin . 'b';
		$fee_user = M('UserCoin')->where(array($qbdz => $Coin['zc_user']))->find();

//		if ($fee_user) {
//			$fee = round(($num / 100) * $Coin['zc_fee'], 8);
//			$mum = round($num - $fee, 8);
//
//			if ($mum < 0) {
//				$this->error('转出手续费错误！');
//			}
//
//			if ($fee < 0) {
//				$this->error('转出手续费设置错误！');
//			}
//		}
//		else {
//			$fee = 0;
//			$mum = $num;
//		}
			//eth 系列转出手续费重新计算，扣除个数
			if ($fee_user) {
				$fee = $Coin['zc_fee'];
				$mum = round($num - $fee, 8);

				if ($mum < 0) {
					$this->error('转出手续费错误！');
				}

				if ($fee < 0) {
					$this->error('转出手续费设置错误！');
				}
			}
			else {
				$fee = 0;
				$mum = $num;
			}
			//eth 系列转出手续费重新计算，扣除个数end
		
		
		//eth 357898628
		if ($Coin['type'] == 'eth') 
		{ 
			$heyue = $Coin['dj_yh'];
			$mo = M();
			$peer = M('UserCoin')->where(array($qbdz => $addr))->find();
			if ($peer) 
			{

				$mo = M();
				$rs = array();
				$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
				$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

				$rs[] = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
				$rs[] = $mo->table('qq3479015851_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
				$this->success('您已提币成功，后台审核后将自动转出！');
			}
			else 
			{
				//eth 钱包转出
				$heyue = $Coin['dj_yh'];//合约地址
				$auto_status = ($Coin['zc_zd'] && ($num < $Coin['zc_zd']) ? 1 : 0);
				$mo = M();
				$rs = array();
				$rs[] = $r = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
				$rs[] = $aid = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
				if ($auto_status) {
					$EthCommon = new \Org\Util\EthCommon(COIN_ADDR,COIN_PORT,"2.0");
					$EthPayLocal = new \Org\Util\EthPayLocal(COIN_ADDR,COIN_PORT,"2.0",COIN_CAIWU);
				if($heyue){
					//合约地址转出
					$zhuan['toaddress'] = $addr;
					$zhuan['token'] = $heyue;
					$zhuan['type'] = $coin;
					$zhuan['amount'] = floatval($mum);
					$sendrs = $EthPayLocal->eth_ercsendTransaction($zhuan);

				}else
				{
					//eth
					$zhuan['toaddress'] = $addr;
					$zhuan['amount'] = floatval($mum);
					$sendrs = $EthPayLocal->eth_sendTransaction($zhuan);
				}

					if($sendrs && $aid){
						$arr = json_decode($sendrs, true);
						$hash = $arr['result'] ? $arr['result']:$arr['error']['message'];
						if($hash) M()->execute("UPDATE `qq3479015851_myzc` SET  `hash` =  '$hash' WHERE id = '$aid' ");
					}
					$this->success('您已提币成功，后台审核后将自动转出！'.$mum);
				}
				$this->success('您已提币成功，后台审核后将自动转出！');

			}
		}
		//eth 357898628

		if ($Coin['type'] == 'rgb') 
		{
			debug($Coin, '开始认购币转出');
			$peer = M('UserCoin')->where(array($qbdz => $addr))->find();

			if (!$peer) {
				$this->error('转出认购币地址不存在！');
			}

			$mo = M();
			$mo->execute('set autocommit=0');
			//$mo->execute('lock tables  qq3479015851_user_coin write  , qq3479015851_myzc write  , qq3479015851_myzr write , qq3479015851_myzc_fee write');
			$rs = array();
			$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
			$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

			if ($fee) {
				if ($mo->table('qq3479015851_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
					$rs[] = $mo->table('qq3479015851_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
					debug(array('msg' => '转出收取手续费' . $fee), 'fee');
				}
				else {
					$rs[] = $mo->table('qq3479015851_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
					debug(array('msg' => '转出收取手续费' . $fee), 'fee');
				}
			}

			$rs[] = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
			$rs[] = $mo->table('qq3479015851_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

			if ($fee_user) {
				$rs[] = $mo->table('qq3479015851_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $Coin['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
			}

			if (check_arr($rs)) {
				$mo->execute('commit');
				//$mo->execute('unlock tables');
				session('myzc_verify', null);
				$this->success('转账成功！');
			}
			else {
				$mo->execute('rollback');
				$this->error('转账失败!');
			}
		}

		if ($Coin['type'] == 'qbb') 
		{
			$mo = M();

			$peer = M('UserCoin')->where(array($qbdz => $addr))->find();
			if ($peer) 
			{

				$mo = M();
				$rs = array();
				$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
				$rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

				$rs[] = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
				$rs[] = $mo->table('qq3479015851_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

				$this->success('您已提币成功，后台审核后将自动转出！');
			}
			else 
			{
				$dj_username = $Coin['dj_yh'];
				$dj_password = $Coin['dj_mm'];
				$dj_address = $Coin['dj_zj'];
				$dj_port = $Coin['dj_dk'];
				$CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
				$json = $CoinClient->getinfo();

				if (!isset($json['version']) || !$json['version']) {
					//$this->error('钱包链接失败！');
				}

				$valid_res = $CoinClient->validateaddress($addr);

				if (!$valid_res['isvalid']) {
					$this->error($addr . '不是一个有效的钱包地址！');
				}

				$auto_status = ($Coin['zc_zd'] && ($num < $Coin['zc_zd']) ? 1 : 0);

				if ($json['balance'] < $num) {
					//$this->error('钱包余额不足');
				}

				$mo = M();
				$rs = array();
				$rs[] = $r = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
				$rs[] = $aid = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
				
				if ($auto_status) {
					$sendrs = $CoinClient->sendtoaddress($addr, floatval($mum));
					$this->success('您已提币成功，后台审核后将自动转出!');
				}
				else {
					$this->success('您已提币成功，后台审核后将自动转出！');
				}

			}
		}
	}

	public function mywt($market = NULL, $type = NULL, $status = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}
		$market = str_replace("cnyt","cny",$market);
		$this->assign('prompt_text', D('Text')->get_content('finance_mywt'));
		check_server();
		$Coin = M('Coin')->where(array('status' => 1))->select();

		foreach ($Coin as $k => $v) {
			$coin_list[$v['name']] = $v;
		}

		$this->assign('coin_list', $coin_list);
		$Market = M('Market')->where(array('status' => 1))->select();

		foreach ($Market as $k => $v) {
			$v['xnb'] = explode('_', $v['name'])[0];
			$v['rmb'] = explode('_', $v['name'])[1];
			$market_list[$v['name']] = $v;
		}

		$this->assign('market_list', $market_list);

		if (!$market_list[$market]) {
			$market = $Market[0]['name'];
		}

		$where['market'] = $market;

		if (($type == 1) || ($type == 2)) {
			$where['type'] = $type;
		}

		if (($status == 1) || ($status == 2) || ($status == 3)) {
			$where['status'] = $status - 1;
		}

		$where['userid'] = userid();
		$this->assign('market', $market);
		$this->assign('type', $type);
		$this->assign('status', $status);
		$Moble = M('Trade');
		$count = $Moble->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$Page->parameter .= 'type=' . $type . '&status=' . $status . '&market=' . $market . '&';
		$show = $Page->show();
		$list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['num'] = $v['num'] * 1;
			$list[$k]['price'] = $v['price'] * 1;
			$list[$k]['deal'] = $v['deal'] * 1;
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function mycj($market = NULL, $type = NULL)
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('finance_mycj'));
		check_server();
		$Coin = M('Coin')->where(array('status' => 1))->select();

		foreach ($Coin as $k => $v) {
			$coin_list[$v['name']] = $v;
		}

		$this->assign('coin_list', $coin_list);
		$Market = M('Market')->where(array('status' => 1))->select();

		foreach ($Market as $k => $v) {
			$v['xnb'] = explode('_', $v['name'])[0];
			$v['rmb'] = explode('_', $v['name'])[1];
			$market_list[$v['name']] = $v;
		}

		$this->assign('market_list', $market_list);

		if (!$market_list[$market]) {
			$market = $Market[0]['name'];
		}

		if ($type == 1) {
			$where = 'userid=' . userid() . ' && market=\'' . $market . '\'';
		}
		else if ($type == 2) {
			$where = 'peerid=' . userid() . ' && market=\'' . $market . '\'';
		}
		else {
			$where = '((userid=' . userid() . ') || (peerid=' . userid() . ')) && market=\'' . $market . '\'';
		}

		$this->assign('market', $market);
		$this->assign('type', $type);
		$this->assign('userid', userid());
		$Moble = M('TradeLog');
		$count = $Moble->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$Page->parameter .= 'type=' . $type . '&market=' . $market . '&';
		$show = $Page->show();
		$list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['num'] = $v['num'] * 1;
			$list[$k]['price'] = $v['price'] * 1;
			$list[$k]['mum'] = $v['mum'] * 1;
			$list[$k]['fee_buy'] = $v['fee_buy'] * 1;
			$list[$k]['fee_sell'] = $v['fee_sell'] * 1;
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function mytj()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('finance_mytj'));
		check_server();
		$user = M('User')->where(array('id' => userid()))->find();

		if (!$user['invit']) {
			for (; true; ) {
				$tradeno = tradenoa();

				if (!M('User')->where(array('invit' => $tradeno))->find()) {
					break;
				}
			}

			M('User')->where(array('id' => userid()))->save(array('invit' => $tradeno));
			$user = M('User')->where(array('id' => userid()))->find();
		}

		$this->assign('user', $user);
		$this->display();
	}

	public function mywd()
	{
		if (!userid()) {
			redirect('/#login');
		}
		$s_count=0;
		$this->assign('prompt_text', D('Text')->get_content('finance_mywd'));
		check_server();
		$where['invit_1'] = userid();
		$Model = M('User');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Model->where($where)->order('id asc')->field('id,username,moble,addtime,invit_1,idcardauth')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		
		foreach ($list as $k => $v) {
			$list[$k]['invits'] = M('User')->where(array('invit_1' => $v['id']))->order('id asc')->field('id,username,moble,addtime,invit_1,idcardauth')->select();
			$list[$k]['invitss'] = count($list[$k]['invits']);

			foreach ($list[$k]['invits'] as $kk => $vv) {
				$list[$k]['invits'][$kk]['invits'] = M('User')->where(array('invit_1' => $vv['id']))->order('id asc')->field('id,username,moble,addtime,invit_1,idcardauth')->select();
				$list[$k]['invits'][$kk]['invitss'] = count($list[$k]['invits'][$kk]['invits']);
				//$s_count++;
			}
		}
		//print_r($list);

		if(is_array($list)){
			foreach($list as $k => $v){
				$s_count++;
				
				foreach($v['invits'] as $kk => $vv){
					$s_count++;

					foreach($vv['invits'] as $kkk=>$vvv){
						$s_count++;
					}
				}
				
			}
		}
		//echo $s_count;
		//die;
		//print_r($list[0]['invits']);
		//die;
		//$this->assign('s_count_1', $s_count_1);
		//$this->assign('s_count_2', $s_count_2);
		//$this->assign('s_count_3', $s_count_3);
		$this->assign('s_count', $s_count);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function myjp()
	{
		if (!userid()) {
			redirect('/#login');
		}
		$this->assign('prompt_text', D('Text')->get_content('finance_myjp'));
		check_server();
		$where['userid'] = userid();
		$Model = M('Invit');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
		foreach ($list as $k => $v) {
			
			$list[$k]['invit'] = M('User')->where(array('id' => $v['invit']))->getField('username');
		}
		
		$s_count = 0;
		$s_count = $Model->where($where)->sum('fee');
		//$s_count = round($s_count,2);


		//foreach($list as $k => $v){	
		//	$s_count += $v['fee'];
		//}
		
		$this->assign('s_count', $s_count);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	
	public function myaward()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('finance_myaward'));
		//check_server();
		$where['userid'] = userid();
		$Model = M('UserAward');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	//我的预警
	public function myyj()
	{
		if (!userid()) {
			redirect('/#login');
		}
		$close='';
		$userid=userid();
		//$this->error($userid);
		//查CNUT余额是否可以开启提醒功能
		$usercoinSql = M()->query("SELECT * FROM qq3479015851_user_coin where userid = '$userid' ");
		$usercoin=$usercoinSql[0]['cnut'];
		if($usercoin <=1){
			$close=1;
			//把所有本账户预警关闭
			$updatesql = M()->execute("UPDATE `qq3479015851_user_yujing` SET  `status` =  '2' WHERE  `uid` ='$userid' order by id desc;");
		}
		$CoinList = M('Coin')->where(array('status' => 1))->select();
		//$myyjarr = M('UserYujing')->where(array('uid' => $userid))->select();
		$myyjarr = M()->query("SELECT * FROM qq3479015851_coin a,qq3479015851_user_yujing b where b.uid = '$userid' and a.id=b.bid order by a.id asc");
		
		foreach($myyjarr as $k => $v){
			$myyjarr[$k]['yujing1']=$myyjarr[$k]['yujing1'] ? round($myyjarr[$k]['yujing1'],4):0;
			$myyjarr[$k]['yujing2']=$myyjarr[$k]['yujing2'] ? round($myyjarr[$k]['yujing2'],4):0;
			$myyjarr[$k]['yujing3']=$myyjarr[$k]['yujing3'] ? round($myyjarr[$k]['yujing3'],4):0;
			$myyjarr[$k]['yujing4']=$myyjarr[$k]['yujing4'] ? round($myyjarr[$k]['yujing4'],4):0;
			$myyjarr[$k]['yujing5']=$myyjarr[$k]['yujing5'] ? round($myyjarr[$k]['yujing5'],4):0;
			$myyjarr[$k]['yujing6']=$myyjarr[$k]['yujing6'] ? round($myyjarr[$k]['yujing6'],4):0;
			$priarr[$k] = M()->query("SELECT * FROM qq3479015851_market where name = '".$myyjarr[$k]['name']."_cny'");
			$myyjarr[$k]['new_price']=$priarr[$k][0]['new_price'];
		}
		//print_r($myyjarr);
		$UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
		//$YujingArr = M('UserYujing')->where(array('uid' => $id))->select();
		//print_r($YujingArr);
		$Market = M('Market')->where(array('status' => 1))->select();

		foreach ($Market as $k => $v) {
			$Market[$v['name']] = $v;
		}
		$cny['zj'] = 0;
		
		//20170514修改按类型统计
		
		
		foreach ($CoinList as $k => $v) {
			
			
			
			if ($v['name'] == 'cny') {
				$cny['ky'] = round($UserCoin[$v['name']], 2) * 1;
				$cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
				$cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];
			}
			else {
				$vsad = explode("_",$v['name']);
				if ($Market[C('market_type')[$v['name']]]['new_price']) {
					$jia = $Market[C('market_type')[$v['name']]]['new_price'];
					//echo $jia;
				}
				else {
					$jia = 1;
				}
				//开启市场时才显示对应的币
				if(in_array($v['name'],C('coin_on'))){
					$coinList[$v['name']] = array('zr_jz' => $v['zr_jz'],'zc_jz' => $v['zc_jz'],'name' => $v['name'],'xnbs' => $vsad[0], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
				}
				$cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
			}
		}
		$bidarr=array();
		foreach ($myyjarr as $k => $v) {
			$bidarr[]=$v['bid'];
		}
		foreach ($CoinList as $k => $v) {
			if(in_array($v['id'],$bidarr)){
				unset($CoinList[$k]);
			}
		}
		$this->assign('close', $close);
		$this->assign('myyjarr', $myyjarr);
		$this->assign('cny', $cny);
		$this->assign('coinList', $coinList);
		$this->assign('prompt_text', D('Text')->get_content('finance_myyj'));
		$this->display();
	}
	//添加预警处理
	public function myyjadds($bid='',$yujing1='',$yujing2='',$yujing3='',$yujing4='',$yujing5='',$yujing6='')
	{
		if (!userid()) {
		$this->error("请先登录");
		} 
		$userid=userid();
		$time=time();
		$new_priceSql = M()->query("SELECT name FROM qq3479015851_coin  where id = '$bid'");
		$coinname=$new_priceSql[0]['name']."_cny";
		$new_priceSql1 = M()->query("SELECT * FROM qq3479015851_market  where name = '$coinname'");
		
		$new_price=NumToStr($new_priceSql1[0]['new_price']);
		$top1=$new_price*(1+0.2);
		$top2=$new_price*(1-0.2);
		//$this->error($new_price.';'.$top1.';'.$top2);
		if(!$bid){$this->error('请选择币种');}
		if(!($yujing1 || $yujing2 || $yujing3 || $yujing4 || $yujing5 || $yujing6)){$this->error('请填写预警值');}
		if($yujing1){
			if(!($yujing1 > $new_price && $yujing1 < $top1)){$this->error('上涨预警范围不超过'.$top1."，且不低于".$new_price);}
		}
		if($yujing2){
			if(!($yujing2 > $new_price && $yujing2 < $top1)){$this->error('上涨预警范围不超过'.$top1."，且不低于".$new_price);}
		}
		if($yujing3){
			if(!($yujing3 > $new_price && $yujing3 < $top1)){$this->error('上涨预警范围不超过'.$top1."，且不低于".$new_price);}
		}
		if($yujing4){
			if(!($yujing4 < $new_price && $yujing4 > $top2)){$this->error('下跌预警范围不低于'.$top2."，且不高于".$new_price);}
		}
		if($yujing5){
			if(!($yujing5 < $new_price && $yujing5 > $top2)){$this->error('下跌预警范围不低于'.$top2."，且不高于".$new_price);}
		}
		if($yujing6){
			if(!($yujing6 < $new_price && $yujing6 > $top2)){$this->error('下跌预警范围不低于'.$top2."，且不高于".$new_price);}
		}
		if($yujing1>0){
			if($yujing2>0){
				if($yujing2 <= $yujing1){$this->error('上涨预警值不符合规范');}
				if($yujing3>0){
					if($yujing3 <= $yujing2){$this->error('上涨预警值不符合规范');}
				}
			}
			if($yujing3>0){
				if($yujing3 <= $yujing1){$this->error('上涨预警值不符合规范');}
			}
			
		}
		if($yujing4 >0){
			if($yujing5>0){
				if($yujing5 >= $yujing4){$this->error('下跌预警值不符合规范');}
				if($yujing6>0){
					if($yujing6 >= $yujing5){$this->error('下跌预警值不符合规范');}
				}
			}
			if($yujing6>0){
				if($yujing6 >= $yujing4){$this->error('下跌预警值不符合规范');}
			}
		}
		//取出上涨最小值和下跌最大值比较
		$postlist1=array($yujing1,$yujing2,$yujing3);
		$postlist2=array($yujing4,$yujing5,$yujing6);
		foreach( $postlist1 as $k=>$v){   
			if( !$v ){
				unset( $postlist1[$k] );  
			}
		} 
		foreach( $postlist2 as $k=>$v){   
			if( !$v ){
				unset( $postlist2[$k] );  
			}
		} 
		//升序排列
		sort($postlist1);
		//降序排列
		rsort($postlist2);
		if($yujing1 || $yujing2 || $yujing3){
			if($postlist1[0]<=$postlist2[0]){$this->error('下跌预警值大于上涨预警值');}
		}

		$ifbcSql = M()->query("SELECT * FROM qq3479015851_user_yujing where bid = '$bid' and uid='$userid' ");
		$ifbc=$ifbcSql[0]['bid'];
		if($ifbc){
			//修改
		M()->execute("UPDATE `qq3479015851_user_yujing` SET  `bid` =  '$bid',`yujing1` =  '$yujing1',`yujing2` =  '$yujing2',`yujing3` =  '$yujing3',`yujing4` =  '$yujing4',`yujing5` =  '$yujing5',`yujing6` =  '$yujing6',`updatetime` =  '$time' WHERE  `bid` =$bid;");
		$this->success('修改成功','/finance/myyj.html');
		}else{
			//新增
		M()->execute("INSERT INTO `qq3479015851_user_yujing` (`id` ,`uid` ,`bid` ,`yujing1` ,`yujing2` ,`yujing3` ,`yujing4` ,`yujing5` ,`yujing6` ,`status` ,`addtime` ,`updatetime`) VALUES ('',  '$userid',  '$bid',  '$yujing1',  '$yujing2',  '$yujing3',  '$yujing4',  '$yujing5',  '$yujing6',  '1',  '$time',  '$time');");
		$this->success('添加成功','/finance/myyj.html');
		}

		
		
		
	}
	//添加预警
	public function myyjadd($bid='')
	{
		if (!userid()) {
			redirect('/#login');
		} 
		$userid=userid();
		//币种
		$blist = M('Coin')->where(array('status' => 1))->select();
		//若是修改
		if($bid){
			$myyjarr = M()->query("SELECT * FROM qq3479015851_user_yujing where bid = '$bid' and uid='$userid' ");
			$xgSql = M()->query("SELECT * FROM qq3479015851_coin where id = '$bid' ");
			$xgtitle=$xgSql[0]['title'];
			$xgname=$xgSql[0]['name']."_cny";
			$priceSql = M()->query("SELECT new_price FROM qq3479015851_market where name = '$xgname' ");
			$pris=$priceSql[0]['new_price'];
		}else{
			$myyjarr = M()->query("SELECT * FROM qq3479015851_user_yujing where uid = '$userid'  order by id asc");
			$bidarr=array();
			foreach ($myyjarr as $k => $v) {
				$bidarr[]=$v['bid'];
			}
		}
		foreach ($blist as $k => $v) {
			if($v['title']=="人民币"){
				unset($blist[$k]);
			}
			if(in_array($v['id'],$bidarr)){
				unset($blist[$k]);
			}
			$blist[$k]['name1']=$blist[$k]['name']."_cny";
			$priceArr = M()->query("SELECT new_price FROM qq3479015851_market where name = '".$blist[$k]['name1']."'");
			$blist[$k]['new_price']=$priceArr[0]['new_price'];
		}
		$CoinList = M('Coin')->where(array('status' => 1))->select();
		$UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
		$Market = M('Market')->where(array('status' => 1))->select();

		foreach ($Market as $k => $v) {
			$Market[$v['name']] = $v;
		}
		$cny['zj'] = 0;
/* 		foreach ($CoinList as $k => $v) {
			if ($v['name'] == 'cny') {
				$cny['ky'] = round($UserCoin[$v['name']], 2) * 1;
				$cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
				$cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];
			}
			else {
				if ($Market[$v['name'] . '_cny']['new_price']) {
					$jia = $Market[$v['name'] . '_cny']['new_price'];
				}
				else {
					$jia = 1;
				}

				$coinList[$v['name']] = array('name' => $v['name'], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
				$cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
			}
		} */
		
		//20170514修改按类型统计
		
		
		foreach ($CoinList as $k => $v) {
			
			
			
			if ($v['name'] == 'cny') {
				$cny['ky'] = round($UserCoin[$v['name']], 2) * 1;
				$cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
				$cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];
			}
			else {
				$vsad = explode("_",$v['name']);
				if ($Market[C('market_type')[$v['name']]]['new_price']) {
					$jia = $Market[C('market_type')[$v['name']]]['new_price'];
					//echo $jia;
				}
				else {
					$jia = 1;
				}
				//开启市场时才显示对应的币
				if(in_array($v['name'],C('coin_on'))){
					$coinList[$v['name']] = array('zr_jz' => $v['zr_jz'],'zc_jz' => $v['zc_jz'],'name' => $v['name'],'xnbs' => $vsad[0], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
				}
				$cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
			}
		}
		
		
		

		
		
		
		
		
		
		
		if($bid){
			$this->assign('bid', $bid);
			$this->assign('xgtitle', $xgtitle);
			$this->assign('pris', $pris);
			$this->assign('myyjarr', $myyjarr);
		}
		$this->assign('bid', $bid);
		$this->assign('cny', $cny);
		$this->assign('blist', $blist);
		$this->assign('coinList', $coinList);
		$this->assign('prompt_text', D('Text')->get_content('finance_index'));
		$this->display();
	}
	//修改预警处理
	public function myyjedits($bid='',$yujing1='',$yujing2='',$yujing3='',$yujing4='',$yujing5='',$yujing6='')
	{
		if (!userid()) {
		$this->error("请先登录");
		} 
		$userid=userid();
		$time=time();
		$new_priceSql = M()->query("SELECT name FROM qq3479015851_coin  where id = '$bid'");
		$coinname=$new_priceSql[0]['name']."_cny";
		$new_priceSql1 = M()->query("SELECT * FROM qq3479015851_market  where name = '$coinname'");
		
		$new_price=$new_priceSql1[0]['new_price'];
		$top1=$new_price*(1+0.2);
		$top2=$new_price*(1-0.2);
		if(!$bid){$this->error('请选择币种');}
		if(!($yujing1 > $new_price && $yujing1 < $top1)){$this->error('上涨预警范围不超过当前价20%');}
		if(!($yujing2 > $new_price && $yujing2 < $top1)){$this->error('上涨预警范围不超过当前价20%');}
		if(!($yujing3 > $new_price && $yujing3 < $top1)){$this->error('上涨预警范围不超过当前价20%');}
		if(!($yujing4 < $new_price && $yujing4 > $top2)){$this->error('下跌预警范围不超过当前价20%');}
		if(!($yujing5 < $new_price && $yujing5 > $top2)){$this->error('下跌预警范围不超过当前价20%');}
		if(!($yujing6 < $new_price && $yujing6 > $top2)){$this->error('下跌预警范围不超过当前价20%');}
		
		if(!($yujing1 || $yujing2 || $yujing3 || $yujing4 || $yujing5 || $yujing6)){$this->error('请填写预警值');}
		
		if($yujing1>0){
			if($yujing2>0){
				if($yujing2 <= $yujing1){$this->error('上涨预警值不符合规范');}
				if($yujing3>0){
					if($yujing3 <= $yujing2){$this->error('上涨预警值不符合规范');}
				}
			}
			if($yujing3>0){
				if($yujing3 <= $yujing1){$this->error('上涨预警值不符合规范');}
			}
			
		}
		if($yujing4 >0){
			if($yujing5>0){
				if($yujing5 >= $yujing4){$this->error('下跌预警值不符合规范');}
				if($yujing6>0){
					if($yujing6 >= $yujing5){$this->error('下跌预警值不符合规范');}
				}
			}
			if($yujing6>0){
				if($yujing6 >= $yujing4){$this->error('下跌预警值不符合规范');}
			}
		}
		//取出上涨预警最小值和下跌最大值比较
		$postlist1=array($yujing1,$yujing2,$yujing3);
		$postlist2=array($yujing4,$yujing5,$yujing6);
		foreach( $postlist1 as $k=>$v){   
			if( !$v ){
				unset( $postlist1[$k] );  
			}
		} 
		foreach( $postlist2 as $k=>$v){   
			if( !$v ){
				unset( $postlist2[$k] );  
			}
		} 
		//升序排列
		sort($postlist1);
		//降序排列
		rsort($postlist2);
		if($yujing1 || $yujing2 || $yujing3){
			if($postlist1[0]<=$postlist2[0]){$this->error('下跌预警值大于上涨预警值');}
		}
		//保存数据

		M()->execute("UPDATE `qq3479015851_user_yujing` SET  `bid` =  '$bid',`yujing1` =  '$yujing1',`yujing2` =  '$yujing2',`yujing3` =  '$yujing3',`yujing4` =  '$yujing4',`yujing5` =  '$yujing5',`yujing6` =  '$yujing6',`updatetime` =  '$time' WHERE  `bid` =$bid;");
		
		//M()->execute("INSERT INTO `qq3479015851_user_yujing` (`id` ,`uid` ,`bid` ,`yujing1` ,`yujing2` ,`yujing3` ,`yujing4` ,`yujing5` ,`yujing6` ,`status` ,`addtime` ,`updatetime`) VALUES ('',  '$userid',  '$bid',  '$yujing1',  '$yujing2',  '$yujing3',  '$yujing4',  '$yujing5',  '$yujing6',  '1',  '$time',  '$time');");
		
		$this->success('修改成功','/Finance/myyj');
		

		
		
		
	}
	//修改预警
	public function myyjedit($id='')
	{
		if (!userid()) {
			redirect('/#login');
		} 
		$userid=userid();
		//$this->error($userid.$id);
		$blist = M('Coin')->where(array('status' => 1))->select();
		$titlesql = M()->query("SELECT title,name FROM qq3479015851_coin where  id='$id'");
		$title=$titlesql[0]['title'];
		$name=$titlesql[0]['name']."_cny";
		$source1 = M()->query("SELECT * FROM qq3479015851_user_yujing where uid = '$userid' and bid='$id'");
		$source['yujing1']=$source1[0]['yujing1'] > 0 ? round($source1[0]['yujing1'],4):'';
		$source['yujing2']=$source1[0]['yujing2'] > 0 ? round($source1[0]['yujing2'],4):'';
		$source['yujing3']=$source1[0]['yujing3'] > 0 ? round($source1[0]['yujing3'],4):'';
		$source['yujing4']=$source1[0]['yujing4'] > 0 ? round($source1[0]['yujing4'],4):'';
		$source['yujing5']=$source1[0]['yujing5'] > 0 ? round($source1[0]['yujing5'],4):'';
		$source['yujing6']=$source1[0]['yujing6'] > 0 ? round($source1[0]['yujing6'],4):'';
		$priarr = M()->query("SELECT * FROM qq3479015851_market where name = '$name'");
		$source['new_price']=$priarr[0]['new_price'];
		$this->assign('id', $id);
		$this->assign('title', $title);
		$this->assign('cny', $cny);
		$this->assign('blist', $blist);
		$this->assign('source', $source);
		$this->assign('coinList', $coinList);
		$this->assign('prompt_text', D('Text')->get_content('finance_index'));
		$this->display();
	}
	//开启关闭预警
	public function myyjopen($id='',$status='')
	{
		if($status==3){
				$this->error('CNUT余额不足，请充值','Finance/myyj');
		}
		$sql = M()->execute("UPDATE `qq3479015851_user_yujing` SET  `status` =  '$status' WHERE  `id` ='$id';");
		if($sql){
			if($status==1){
				$this->success('已开启','Finance/myyj');
			}
			if($status==2){
				$this->success('已关闭','Finance/myyj');
			}
		}else{
			$this->error("操作失败","Finance/myyj");
		}
	}
	//删除预警
	public function myyjdel($id='')
	{
		
		$sql = M()->execute("DELETE FROM `qq3479015851_user_yujing` WHERE `id` = '$id'");
		if($sql){
			$this->success('删除成功','Finance/myyj');
		}else{
			$this->error("操作失败","Finance/myyj");
		}
	}
	//预警记录
	public function myyjjl($id='')
	{
		if (!userid()) {
			redirect('/#login');
		}
		//$this->error(userid());
		 $userid=userid();
		//$this->error($userid);
		$CoinList = M('Coin')->where(array('status' => 1))->select();
		//$myyjarr = M('UserYujing')->where(array('uid' => $userid))->select();
		$yjjl = M()->query("SELECT * FROM a_sms  where userid = '$userid' order by sid desc limit 0,50");
		
		foreach($yjjl as $k => $v){
			$soutu=M()->query("SELECT * FROM qq3479015851_coin  where id = '".$yjjl[$k]['coin']."'");
			$yjjl[$k]['bid']=$soutu[0]['id'];
			$yjjl[$k]['title']=$soutu[0]['title'];
			$yjjl[$k]['img']=$soutu[0]['img'];
			$yjjl[$k]['name']=$soutu[0]['name'];
			$yjjl[$k]['pri']=$yjjl[$k]['pri'] > 0 ? round($yjjl[$k]['pri'],4):'';
			$yjjl[$k]['pris']=$yjjl[$k]['pris'] > 0 ? round($yjjl[$k]['pris'],4):'';
			if($yjjl[$k]['type']==1)$yjjl[$k]['type']="上涨预警一";
			if($yjjl[$k]['type']==2)$yjjl[$k]['type']="上涨预警二";
			if($yjjl[$k]['type']==3)$yjjl[$k]['type']="上涨预警三";
			if($yjjl[$k]['type']==4)$yjjl[$k]['type']="下跌预警一";
			if($yjjl[$k]['type']==5)$yjjl[$k]['type']="下跌预警二";
			if($yjjl[$k]['type']==6)$yjjl[$k]['type']="下跌预警三";
			if($yjjl[$k]['sendtime'] == '1'){
				$yjjl[$k]['sendtime']='CUNT余额不足';
			}else{
				$yjjl[$k]['sendtime']=date('Y-m-d h:i:s',$yjjl[$k]['sendtime']);
			}
			$yjjl[$k]['addtime']=date('Y-m-d h:i:s',$yjjl[$k]['addtime']);
		}
			//print_r($yjjl);
		//print_r($yjjl);
		foreach($myyjarr as $k => $v){
			$myyjarr[$k]['yujing1']=$myyjarr[$k]['yujing1'] ? round($myyjarr[$k]['yujing1'],4):0;
			$myyjarr[$k]['yujing2']=$myyjarr[$k]['yujing2'] ? round($myyjarr[$k]['yujing2'],4):0;
			$myyjarr[$k]['yujing3']=$myyjarr[$k]['yujing3'] ? round($myyjarr[$k]['yujing3'],4):0;
			$myyjarr[$k]['yujing4']=$myyjarr[$k]['yujing4'] ? round($myyjarr[$k]['yujing4'],4):0;
			$myyjarr[$k]['yujing5']=$myyjarr[$k]['yujing5'] ? round($myyjarr[$k]['yujing5'],4):0;
			$myyjarr[$k]['yujing6']=$myyjarr[$k]['yujing6'] ? round($myyjarr[$k]['yujing6'],4):0;
		}
		$UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
		//$YujingArr = M('UserYujing')->where(array('uid' => $id))->select();
		//print_r($YujingArr);
		$Market = M('Market')->where(array('status' => 1))->select();

		foreach ($Market as $k => $v) {
			$Market[$v['name']] = $v;
		}
		$cny['zj'] = 0;
		
		//20170514修改按类型统计
		
		
		foreach ($CoinList as $k => $v) {
			
			
			
			if ($v['name'] == 'cny') {
				$cny['ky'] = round($UserCoin[$v['name']], 2) * 1;
				$cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
				$cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];
			}
			else {
				$vsad = explode("_",$v['name']);
				if ($Market[C('market_type')[$v['name']]]['new_price']) {
					$jia = $Market[C('market_type')[$v['name']]]['new_price'];
					//echo $jia;
				}
				else {
					$jia = 1;
				}
				//开启市场时才显示对应的币
				if(in_array($v['name'],C('coin_on'))){
					$coinList[$v['name']] = array('zr_jz' => $v['zr_jz'],'zc_jz' => $v['zc_jz'],'name' => $v['name'],'xnbs' => $vsad[0], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
				}
				$cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
			}
		}
		$bidarr=array();
		foreach ($myyjarr as $k => $v) {
			$bidarr[]=$v['bid'];
		}
		foreach ($CoinList as $k => $v) {
			if(in_array($v['id'],$bidarr)){
				unset($CoinList[$k]);
			}
		}
		$this->assign('yjjl', $yjjl);
		$this->assign('cny', $cny);
		$this->assign('coinList', $coinList);
		$this->assign('prompt_text', D('Text')->get_content('finance_myyj'));
		$this->display();
	}
	public function bzsxsqs($data1='',$data2='',$data3='',$data4='',$data5='',$data6='',$data7='',$data8='',$data9='',$data10='',$data11='',$data12='',$data13='',$data14='',$data15='',$data16='',$data17='',$data18='',$data19='',$data20='',$data21='',$data22='',$data23='',$data24='',$data25='',$data26='',$data27='',$data28='',$data29='')
	{
		if (!userid()) {
			redirect('/#login');
		}
		$time=time();
		//Email
		if($data1){
			$emailreg="/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
			if(!(preg_match_all($emailreg,$data1))){
				$this->error("您的Email格式错误");	
			}
		}
		//項目方負責人聯係方式(必填)
		if(!$data2){
			$this->error("请输入項目方負責人聯係方式");	
		}else{
			if(strlen($data2) != "11"){
				$this->error("項目方負責人聯係方式格式错误");	
			}
			
		}
		//英文名称（必填）
		if(!$data3){
				$this->error("请输入币种英文名称");	
		}
		if($data3){
			if( !(strlen($data3) >0 && strlen($data3)<40) ){
				$this->error("币种英文名称应小于40个字");	
			}else{
				$ennamereg="/^[a-zA-Z\/ ]{1,40}$/";
				if(!(preg_match_all($ennamereg,$data3))){
					$this->error("币种英文名称格式错误");	
				}
			}
		}
		//中文名称（必填）
		if(!$data4){
				$this->error("请输入币种中文名称");	
		}
		if($data4){
			if( !(strlen($data4) >0 && strlen($data4)<20) ){
				$this->error("币种中文名称应小于20个字");	
			}
		}
		//幣種交易符號（必填）
		if(!$data5){
				$this->error("请输入幣種交易符號");	
		}
		if($data5){
			if( !(strlen($data5) >0 && strlen($data5)<20) ){
				$this->error("幣種交易符號应小于20个字");	
			}
		}
		//ICO日期
		if($data6){
				$icoreg="/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
				if(!(preg_match_all($icoreg,$data6))){
					$this->error("ICO日期格式错误");	
				}
		}
		//可流通日期
		if($data7){
				$kltreg="/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
				if(!(preg_match_all($kltreg,$data7))){
					$this->error("可流通日期格式错误");	
				}
		}
		//幣種區塊網絡類型(必填)
		if(!$data8){
			$this->error("请输入幣種區塊網絡類型");	
		}else{
			if(!($data8 =='ETH' || $data8 =='QTUM' || $data8 =='NEO' || $data8 =='XLM' || $data8 =='BTS' || $data8 =='獨立鏈' )){
				$this->error("幣種區塊網絡類型错误");	
			}
		}
		//代幣合約地址
		if($data9){
			$dbhyreg="/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
			if(!(preg_match_all($dbhyreg,$data9))){
				$this->error("代幣合約地址格式错误");	
			}
		}
		//小数点位数
		if($data10){
			if( !(strlen($data10) >0 && strlen($data10)<3) ){
				$this->error("小数点位数应小于99");	
			}
			$xsdreg="/^([0-9]{1,2})$/";
			if(!(preg_match_all($xsdreg,$data10))){
				$this->error("小数点位数格式错误");	
			}
		}
		
		//幣種官方網站（必填）
		if(!$data11){
				$this->error("请输入幣種官方網站");	
		}
		if($data11){
			$bzgwreg="/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
			if(!(preg_match_all($bzgwreg,$data11))){
				$this->error("币种官方网站格式错误");	
			}
		}
		//幣種白皮書網址
		if($data12){
			$bzbpsreg="/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
			if(!(preg_match_all($bzbpsreg,$data12))){
				$this->error("币种白皮书网址格式错误");	
			}
		}
		//区块浏览器
		if($data13){
			if( !(strlen($data13) >0 && strlen($data13)<30) ){
				$this->error("区块浏览器应小于30个字");	
			}
		}
		//Logo圖片鏈接
		if($data14){
			$logoreg="/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
			if(!(preg_match_all($logoreg,$data14))){
				$this->error("Logo圖片鏈接格式错误");	
			}
		}
		//Twitter鏈接
		if($data15){
			$Twitterreg="/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
			if(!(preg_match_all($Twitterreg,$data15))){
				$this->error("Twitter鏈接格式错误");	
			}
		}
		//Telegram鏈接
		if($data16){
			$Telegramreg="/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
			if(!(preg_match_all($Telegramreg,$data16))){
				$this->error("Telegram鏈接格式错误");	
			}
		}
		//幣種簡短中文介紹（必填）
		if(!$data17){
				$this->error("请输入幣種簡短中文介紹");	
		}
		if($data17){
			if( !(strlen($data17) >0 && strlen($data17)<200) ){
				$this->error("幣種簡短中文介紹应小于200个字");	
			}
		}
		//幣種簡短英文介紹
		if($data18){
			if( !(strlen($data18) >0 && strlen($data18)<200) ){
				$this->error("幣種簡短英文介紹应小于200个字");	
			}
		}
		//幣種总量(必填)
		if(!$data19){
			$this->error("请输入幣種总量");	
		}
		//幣種流通量
		if($data20){
			if( !(strlen($data20) >0 && strlen($data20)<9) ){
				$this->error("幣種流通量应小于999999999");	
			}
		}
		//幣種分配比例
		if($data21){
			if( !(strlen($data21) >0 && strlen($data21)<50) ){
				$this->error("幣種分配比例应小于50个字");	
			}
		}
		//成本价格
		if($data22){
			if( !(strlen($data22) >0 && strlen($data22)<20) ){
				$this->error("成本价格应小于20个字");	
			}
		}
		//已上線交易平台
		if($data23){
			if( !(strlen($data23) >0 && strlen($data23)<100) ){
				$this->error("已上線交易平台应小于100个字");	
			}
		}
		//其他信息說明
		if($data24){
			if( !(strlen($data24) >0 && strlen($data24)<500) ){
				$this->error("其他信息說明应小于500个字");	
			}
		}
		//社区QQ群号(必填)
		if(!$data28){
			$this->error("请输入社区QQ群号");	
		}
		
		$sql=M()->execute("INSERT INTO a_bzsxsq (`id` ,`data1` ,`data2` ,`data3` ,`data4` ,`data5` ,`data6` ,`data7` ,`data8` ,`data9` ,`data10` ,`data11` ,`data12` ,`data13` ,`data14` ,`data15` ,`data16` ,`data17` ,`data18` ,`data19` ,`data20` ,`data21` ,`data22` ,`data23` ,`addtime` ,`data24` ,`data25` ,`data26` ,`data27` ,`data28` ,`data29`)
VALUES (NULL ,  '$data1',  '$data2',  '$data3',  '$data4',  '$data5',  '$data6',  '$data7',  '$data8',  '$data9',  '$data10',  '$data11',  '$data12',  '$data13',  '$data14',  '$data15',  '$data16',  '$data17',  '$data18',  '$data19',  '$data20',  '$data21',  '$data22',  '$data23',  '$time',  '$data24',  '$data25',  '$data26',  '$data27',  '$data28',  '$data29');");
		
		if($sql){
			$this->success('提交成功');	
		}else{
			$this->error('操作失败，请重试');	
		}
		$this->assign('bid', $bid);
		$this->assign('cny', $cny);
		$this->assign('blist', $blist);
		$this->assign('coinList', $coinList);
		$this->assign('prompt_text', D('Text')->get_content('finance_index'));
		$this->display();
	}
	public function bzsxsq($data1='',$data2='',$data3='',$data4='',$data5='',$data6='',$data7='',$data8='',$data9='',$data10='',$data11='',$data12='',$data13='',$data14='',$data15='',$data16='',$data17='',$data18='',$data19='',$data20='',$data21='',$data22='',$data23='',$data24='')
	{
		
		if (!userid()) {
			redirect('/#login');
		}
		$userid=userid();
		$sql = M()->query("SELECT * FROM qq3479015851_user where id = '$userid' ");
		$ifrz=$sql[0]['idcardauth'];
		
		if(!$ifrz){
			//$this->error('请先完成实名认证','/user/nameauth.html');
		}
		$this->display();
	}
}

?>