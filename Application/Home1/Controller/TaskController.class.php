<?php
namespace Home\Controller;

class TaskController extends HomeController
{
	public function test()
	{
		//echo strtotime("2018-04-01 00:00:00");
		//1522512000 and cny =0 and eosd >0
		$data = M()->query("SELECT uid,pri FROM  a_ctc where type = 1 $where order by cid desc ; ");
		foreach($data as $k =>$v){
			$userid='';
			$userid=$v['uid'];
			$sour1 = M()->query("SELECT username,truename FROM  qq3479015851_user where id='$userid'; ");
			$data[$k]['username']=$sour1[0]['username'];
			$data[$k]['truename']=$sour1[0]['truename'];
		}
		$data1 = M()->query("SELECT uid,pri FROM  a_ctc where type = 2 $where order by cid desc ; ");
		foreach($data1 as $k1 =>$v1){
			$userid='';
			$userid=$v1['uid'];
			$sour2 = M()->query("SELECT username,truename FROM  qq3479015851_user where id='$userid'; ");
			$data1[$k1]['username']=$sour2[0]['username'];
			$data1[$k1]['truename']=$sour2[0]['truename'];
		}
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		echo "<pre>";
		print_r($data1);
		echo "</pre>";
	}
	public function index()
	{
		$userid = userid();
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$data = M()->query("SELECT * FROM  `qq3479015851_a_sign_rule` where status = 1 order by id asc");
			foreach($data as $k=>$v){
				$data[$k]['allci'] = 1;
				$data[$k]['ylcs'] = $ylcs_1 =0;
		if($userid){
				if($v['id']==1){
					$isck = M()->query("SELECT * FROM  `qq3479015851_a_sign` where userid = '".$userid."' and hdid = '".$v['id']."' and addtime >$beginToday and addtime < $endToday and iftjr = 0 ");
					if($isck) $data[$k]['ylcs'] = 1;
				}
				if($v['id']==2){
					$isck = M()->query("SELECT * FROM  `qq3479015851_a_sign` where userid = '".$userid."' and hdid = '".$v['id']."'  ");
					if($isck) $data[$k]['ylcs'] = 1;
				}
				if($v['id']==3){
					$isck = M()->query("SELECT * FROM  `qq3479015851_a_sign` where userid = '".$userid."' and  hdid = '".$v['id']."'  ");
					if($isck) $data[$k]['ylcs'] = 1;
				}
				if($v['id']==5){
					$ylcs = M()->query("SELECT sum(ylcs) as sum FROM  `qq3479015851_a_sign` where  hdid = '".$v['id']."'  and addtime > $beginToday   ");
//					$sqljqr = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where (userid = '111908' or peerid = '111908' or peerid = 0 or userid = 0) and  addtime > '$beginToday' ");
//					$myedjqr=$sqljqr[0]['sum'];
//					$jqrnum = $myedjqr/50;
					//if(date("H",time()) <=10)  $jqrnum =  0;
					$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
					$jqr =0;
					
					$endti=strtotime("2018-04-24 15:00:00");
					if(time() > $endti){
						//$jqr = (time()-$endti)*3.0864;
					}
					$ylcs_1= 200000 -  $ylcs[0]['sum'] -$jqr;
					$ylcs_1 = ($ylcs_1>0) ? $ylcs_1:'0';
				}
				if($v['id']==6){
					$isnum1 = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 = '".$userid."' and idcardauth =1  ");
					$data[$k]['allci'] = $isnum1[0]['count'];
					$ylcs = M()->query("SELECT ylcs FROM  `qq3479015851_a_sign` where userid = '".$userid."' and hdid = '".$v['id']."' order by id desc LIMIT 1  ");
					$data[$k]['ylcs'] = $ylcs[0]['ylcs'] ? $ylcs[0]['ylcs'] : 0;
				}
				if($data[$k]['ylcs'] == $data[$k]['allci'] && $data[$k]['allci'] !=0){
					$data[$k]['islq'] = 1;
					$ynum ++;
				}
			}
				if($ylcs_1){
					$data[$k]['sci'] = floor($ylcs_1);
				}else{
					$data[$k]['sci'] = $data[$k]['allci'] -$data[$k]['ylcs'];
				}
				
		}
		//解冻1%
		$time_jd=strtotime("10:00:00");//每天解冻时间
		$time_now=time();
		if($userid && $userid==312113){
			if($time_now > $time_jd){
				//是否有冻结记录
				$sql_ifjd=M()->query("select * from `a_wakuang` WHERE  `userid` ='$userid'  order by wid desc limit 1");
				
				$hisjdtime=$sql_ifjd[0]['jdtime'];
				$ye_dj=$sql_ifjd[0]['cnut'];//冻结额度
				$wid=$sql_ifjd[0]['wid'];
				
				if($sql_ifjd && $hisjdtime < $beginToday){
					if($ye_dj>0){
						$num_jd=$ye_dj*0.01;//解冻额度
						M()->execute("UPDATE  `a_wakuang` SET  `cnut` =  `cnut`-$num_jd ,`jdtime` =  '$time_now' WHERE  `userid` ='$userid' and `wid` ='$wid';");
						M()->execute("INSERT INTO  `a_jd` (`id` ,`userid` ,`num` ,`addtime`) VALUES (NULL ,  '$userid',  '$num_jd',  '$time_now');");
						M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnutd` =  `cnutd`-$num_jd  WHERE  `userid` ='$userid';");
						M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` =  `cnut`+$num_jd WHERE  `userid` ='$userid';");
					}
				}
				
			}
		}
		$this->assign('data',$data);
		$this->display();
	}
	public function index_old()
	{
		$hdjd=array('hd1' => '0','hd2' => '0','hd3' => '0','hd4' => '0','hd5' => '0','hd6' => '0','hd_1' => '1','hd_2' => '1','hd_3' => '0','hd_4' => '1','hd_5' => '1','hd_6' => '1');
		$num_ywc='0';
		$num_wwc='6';
		$userid=userid();
		$sql5=M()->query("select * from `qq3479015851_a_sign_rule`;");
		
		$daystart=$sql5[0]['daystart'];
		$dayend=$sql5[0]['dayend'];
		//print_r($sql);
		
		$time_3=strtotime($daystart);
		$time_4=strtotime($dayend);
		$sql=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='1' and `iftjr`='0' and addtime > '$time_3' and addtime < '$time_4';");
		$sql_hd2=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='2' and `iftjr`='0'  ;");
		$sql_hd3=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='3' and `iftjr`='0'  ;");
		$sql_hd4=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='4' and `iftjr`='0'  order by id desc;");
		$sql_hd5=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='5' and `iftjr`='0'  order by id desc;");
		$sql_hd6=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='6' and `iftjr`='0'  order by id desc;");
		//活动3
		$hd3_rz_sql=M()->query("select * from `qq3479015851_user` WHERE  `id` ='$userid' ;");
		$shifourenzheng=$hd3_rz_sql[0]['idcardauth'];
		if($shifourenzheng){
			$hdjd['hd_3']='1';	
		}
		//活动4
		$lqcs_4=$sql_hd4[0]['lqcs'];
		//活动5
		$lqcs_5=$sql_hd5[0]['lqcs'];
		$sql5_ed = M('ASignRule')->where(array('id' => 5))->select();
		$hd5_ed=$sql5_ed[0]['num2'];
		$sql5_cs=M()->query("select SUM(mum) as sum from `qq3479015851_trade` WHERE  `userid` ='$userid' ;");
		$hd5_myed=$sql5_cs[0]['sum'];
		$beishu5=$hd5_myed/$hd5_ed;
		$hd5_lqcs=floor($beishu5);//可领次数
		$hdjd['hd_5']=$hd5_lqcs;//前台展示可领次数
		$hd5_ylcs_sql=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `iftjr`='0' and `hdid`='5' order by id desc ;");
		//print_r($hd5_ylcs_sql);
		$hd5_ylcs=$hd5_ylcs_sql[0]['ylcs'];//已领次数
		if($hd5_ylcs){
			$hdjd['hd5']=$hd5_ylcs;//前台展示已领次数
		}
		
		//活动6
		$lqcs_6=$sql_hd6[0]['lqcs'];
		
		$sql6_cs=M()->query("select count(*) as num from `qq3479015851_user` WHERE  `invit_1` ='$userid' and `idcardauth` ='1' ;");
		
		//print_r($sql6);
		$hd6_lqcs=$sql6_cs[0]['num'];//可领取次数
		$hdjd['hd_6']=$hd6_lqcs;
		$hd6_ylcs_sql=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid'  and `hdid`='6' order by id desc ;");
		$hd6_ylcs=$hd6_ylcs_sql[0]['ylcs'];//已领次数
		if($hd6_ylcs){
			$hdjd['hd6']=$hd6_ylcs;
		}
		
		
		$jiangli6=$sql5[5]['time'];
		$jiangli5=$sql5[4]['time'];
		$jiangli4=$sql5[3]['time'];
		$jiangli3=$sql5[2]['time'];
		$jiangli2=$sql5[1]['time'];
		$jiangli1=$sql5[0]['time'];
		$shuoming6=$sql5[5]['shuoming'];
		$shuoming5=$sql5[4]['shuoming'];
		$shuoming4=$sql5[3]['shuoming'];
		$shuoming3=$sql5[2]['shuoming'];
		$shuoming2=$sql5[1]['shuoming'];
		$shuoming=$sql5[0]['shuoming'];
		$time=$sql5[0]['time'];
		$ifsign='';
		$ifregist='';
		$ifrz='';
		$ifcjds='';
		$ifcjed='';
		$ifyq='';
		if($sql){
			$ifsign=1;
			$hdjd['hd1']='1';
			$num_ywc++;
			$num_wwc--;
		}
		if($sql_hd2){
			$ifregist=1;
			$hdjd['hd2']='1';
			$num_ywc++;
			$num_wwc--;
		}
		if($sql_hd3){
			$ifrz=1;
			$hdjd['hd3']='1';
			$num_ywc++;
			$num_wwc--;
		}
		if($sql_hd4){
			if(!$lqcs_4){
				$ifcjds=1;
				$num_ywc++;
				$num_wwc--;
			}
		}
		if($sql_hd5){
			if(!$lqcs_5){
				$ifcjed=1;
				$num_ywc++;
				$num_wwc--;
			}
		}
		if($sql_hd6){
			if(!$lqcs_6){
				$ifyq=1;
				$num_ywc++;//已完成计划
				$num_wwc--;//未完成计划
			}
		}
		$this->assign('jiangli6',$jiangli6);
		$this->assign('jiangli4',$jiangli4);
		$this->assign('jiangli3',$jiangli3);
		$this->assign('jiangli2',$jiangli2);
		$this->assign('jiangli5',$jiangli5);
		$this->assign('jiangli1',$jiangli1);
		$this->assign('shuoming6',$shuoming6);
		$this->assign('shuoming5',$shuoming5);
		$this->assign('shuoming4',$shuoming4);
		$this->assign('shuoming3',$shuoming3);
		$this->assign('shuoming2',$shuoming2);
		$this->assign('shuoming',$shuoming);
		$this->assign('time',$time);
		$this->assign('ifyq',$ifyq);
		$this->assign('ifcjed',$ifcjed);
		$this->assign('ifcjds',$ifcjds);
		$this->assign('ifrz',$ifrz);
		$this->assign('ifregist',$ifregist);
		$this->assign('ifsign',$ifsign);
		$this->assign('hdjd',$hdjd);
		$this->assign('num_wwc',$num_wwc);
		$this->assign('num_ywc',$num_ywc);
		$this->assign('userid',$userid);
		$this->display();
	}
	//签到（奖励CNUT k*n%）//不限额
	public function sign()
	{
		if(userid() !='111908'){
			//$this->error('该计划调试中');
		}
		$userid=userid();
		if(!$userid){
			$this->success('请先登录',"/");
		}
		$sql2 = M('ASignRule')->where(array('id' => 1))->select();
		$yscoin=$sql2[0]['coinname'];//币种
		$ysnum=$sql2[0]['num'];//基数
		$ysnum3=$sql2[0]['num3'];//上级基数
		$ysnum4=$sql2[0]['num4'];//上上级基数
		//活动是否开启
		$time=time();
		$time_1=strtotime($sql2[0]['starttime']);
		$time_2=strtotime($sql2[0]['endtime']);
		$time_3=strtotime($sql2[0]['daystart']);
		$time_4=strtotime($sql2[0]['dayend']);
		if($time < $time_1 )$this->error('活动暂未开启');
		if($time > $time_2 )$this->error('活动已结束');
		if($time < $time_3 )$this->error('今日活动暂未开启');
		if($time > $time_4 )$this->error('今日活动已结束');
		$this->error('111');
		//账号通过实名认证才可以
		$sql_ifsmrz = M()->query("select id from `qq3479015851_user` WHERE  `id` ='$userid' and `idcardauth` ='1' ");
		if(!$sql_ifsmrz){
			$this->error('通过实名认证才能参与活动');
		}
		
		//判断余额是否0
		$sql_myye = M()->query("select cnut from `a_cnut` WHERE  `userid` ='$userid' limit 1 ");
		$myye=$sql_myye[0]['cnut'];//我的余额
		if(!$myye){
			$this->error('您没有CNUT无法参与活动');
		}
		//每天只领取一次
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$sql_hd1=M()->query("select id from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='1' and addtime > '$beginToday' and addtime < '$endToday' and iftjr = 0 limit 1;");
		if($sql_hd1){
			$this->error('您今日已领取奖励');
		}

		$money=$myye;
		$getmoney=$ysnum * $money;
		$getmoney=round($getmoney, 2);//本人应得多少钱
		$getmoney = $getmoney ? $getmoney : 0;
		$sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid` ,`coinname` ,`num` ,`addtime`) VALUES (NULL ,  '$userid',  '1',  '$yscoin',  '$getmoney',  '$time');");
		
		$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` = `$yscoin`+$getmoney WHERE  `userid` ='$userid';");
		
		$sql_tjr=M()->query("select `invit_1`,`invit_2` from `qq3479015851_user` WHERE  `id` ='$userid' limit 1");
		$tjr=$sql_tjr[0]['invit_1'];
		$tjr2=$sql_tjr[0]['invit_2'];
		//如果有推荐人
		if($tjr){
			$tjr_getmoney=$getmoney * $ysnum3;//推荐人能得多少
			$tjr_getmoney = $tjr_getmoney ? $tjr_getmoney : 0;
			$sql11=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid` ,`coinname` ,`num` ,`iftjr` ,`addtime`) VALUES (NULL ,  '$tjr',  '1',  '$yscoin',  '$tjr_getmoney', '1',  '$time');");
			
			$sql12=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$tjr_getmoney WHERE  `userid` ='$tjr';");
		}
		
		//如果有推荐人2
		if($tjr2){
			$tjr2_getmoney=$getmoney * $ysnum4;//推荐人2能得多少
			$tjr2_getmoney = $tjr2_getmoney ? $tjr2_getmoney : 0;
			$sql21=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid` ,`coinname` ,`num` ,`iftjr` ,`addtime`) VALUES (NULL ,  '$tjr2',  '1',  '$yscoin',  '$tjr2_getmoney', '2',  '$time');");
			
			$sql22=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$tjr2_getmoney WHERE  `userid` ='$tjr2';");
		}
		
		$this->success('领取成功');
		
	}
	
	
	//注册领奖（奖励CNUT200，doge100）
	public function regist()
	{
		$sql2 = M('ASignRule')->where(array('id' => 2))->select();
		$ysnum1=$sql2[0]['num'];
		$ysnum2=$sql2[0]['num2'];
		$yscoin1=$sql2[0]['coinname'];
		$yscoin2=$sql2[0]['coinname2'];
		$userid=userid();
		if(!$userid){
			$this->success('请先注册','/login/register.html');
		}
		$time=time();
		if($userid <320264 && $userid !=312113){
			$this->error('您已领取过奖励');
		}
		$sql_hd2=M()->query("select * from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid` ='2' ;");
		if($sql_hd2){
			$this->error('您已领取过奖励');
		}
		$sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`coinname` ,`num` ,`addtime`)
VALUES (NULL ,  '$userid',  '2',  '$yscoin1','$ysnum1', '$time');");
		//$sql5=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`coinname` ,`num` ,`addtime`)
//VALUES (NULL ,  '$userid',  '2',  '$yscoin2','$ysnum2', '$time');");
		
		$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin1` =  `$yscoin1`+$ysnum1 WHERE  `userid` ='$userid';");
		
		$this->success('领取成功');
		
	}

	//认证领奖（奖EOS 1个）
	public function renzheng()
	{
		$xets=100000;
		$time=time();
		$timestr1=strtotime("00:00:00");
		$timestr2=strtotime("23:59:59");
		$sql2 = M('ASignRule')->where(array('id' => 3))->select();
		$yscoin=$sql2[0]['coinname'];
		$ysnum=$sql2[0]['num'];
		$userid=userid();
		if(!$userid){
			$this->success('请先登录','/login');
		}
		if($userid <320264 && $userid !=312113){
			$this->error('本活动仅新会员可参与');
		}
		$sql6=M()->query("select * from `qq3479015851_user` WHERE  `id` ='$userid' ;");
		//print_r($sql6);
		$ifrz=$sql6[0]['idcardauth'];
		if(!$ifrz){
			$this->success('请先完成实名认证','/user/nameauth/');	
		}
		$sql_xe=M()->query("select count(*) as num from `qq3479015851_a_sign` WHERE  `hdid` ='3' and addtime > '$timestr1' and addtime <'$timestr2' ;");
		$num_xe=$sql_xe[0]['num'];//每日已领取多少条数据
		$dayyxnum=$xets/$ysnum;//每日允许领多少条数据
		if($num_xe >= $dayyxnum){
			$this->error('今日奖励已被领完');	
		}
		$sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`coinname` ,`num` ,`addtime`)
VALUES (NULL ,  '$userid',  '3',  '$yscoin','$ysnum', '$time');");
		
		$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$ysnum WHERE  `userid` ='$userid';");
		
		$this->success("领取成功");
		
	}
	//成交单数（CNUT，单数和cunt待定）
	public function cjds()
	{
		$this->error('暂未开启，敬请期待');	
		$sql2 = M('ASignRule')->where(array('id' => 4))->select();
		$yscoin=$sql2[0]['coinname'];
		$coinnum=$sql2[0]['num'];
		$ds=$sql2[0]['num2'];//后台设置的单数条件
		$userid=userid();
		if(!$userid){
			$this->success('请先登录','/login');
		}
		$time=time();
		//$this->error(1);
		$sql6=M()->query("select count(*) as num from `qq3479015851_trade` WHERE  `userid` ='$userid' ;");
		//print_r($sql6);
		$mynum=$sql6[0]['num'];//已交易单数
		$beishu=$mynum/$ds;
		$yxnumber=floor($beishu);//可领次数
		$sql_num4=M()->query("select COUNT(*) as num from `qq3479015851_a_sign` WHERE  `userid` ='$userid' and `hdid`='4' ;");
		$selectnum=$sql_num4[0]['num'];//已领次数
		$lqcs=$yxnumber - $selectnum;//剩余可领次数
		if($selectnum >=$yxnumber){
			
			$this->error('您还未达到领取条件');	
		}
		if($mynum < $ds){
			$this->error('您还未达到领取条件');	
		}
		$sql = M('UserCoin')->where(array('userid' => $userid))->select();
		$cnut=$sql[0][$yscoin];
		$newcnut=$cnut+$coinnum;
		$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  '$newcnut' WHERE  `userid` ='$userid';");
		if($sql3){
			$lqcs--;
		}
		$sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`num`,`lqcs`,`coinname` ,`addtime`)
VALUES (NULL ,  '$userid',  '4',   '$coinnum','$lqcs',  '$yscoin', '$time');");
		
		
		$this->success('领取成功');
		
	}
	//成交额度（CNUT，额度和cunt数量待定）
	public function cjed()
	{
		session_start();
		if(isset($_SESSION['sesstime'])) {  
  
			if($_SESSION['sesstime'] < time()) {  
		  
				unset($_SESSION['sesstime']);  
				$this->error("1");  
		  
			} else {  
				$this->error("2");  
				$this->error("请勿连续点击"); 
		  
			}  
		  
		}else{
				$_SESSION['sesstime'] = time() + 10; 
				$this->error("3");  
		}
		$userid=userid();
		if(userid() !='111908'){
			//$this->error('该计划调试中');
		}
		//if(date("H",time()) <=10) $this->error('该计划每日10点开始领取！');
		if(!userid()){
			$this->success('请先登录','/');
		}
		//账号通过实名认证才可以
		$sql_ifsmrz = M()->query("select id from `qq3479015851_user` WHERE  `id` ='$userid' and `idcardauth` ='1' ");
		if(!$sql_ifsmrz){
			$this->error('通过实名认证才能参与活动');
		}

		
		//限额(今日总额度)
		$xets=200000;
		$usernum = 12000;//账户冻结上限
		$user_d = M()->query("select cnut from `a_wakuang` WHERE  `userid` ='$userid' order by wid desc limit 1 ");
		if($user_d){
			if($user_d[0]['cunt'] >=$usernum){
				$this->error('冻结挖矿余额已达到上限');
			}else{
				if($user_d[0]['cunt']<0){$user_d[0]['cunt']=0;}
				$user_max_num = $usernum - $user_d[0]['cunt'];//会员总计最大领取次数
			}
		}
		$timestr1=strtotime("00:00:00");
		$timestr2=strtotime("23:59:59");
		$time=time();
//		$sqljqr = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where (userid = '111908' or peerid = '111908' or peerid = 0 or userid = 0) and  addtime > '$timestr1' ");
//		$myedjqr=$sqljqr[0]['sum'];
//		$jqrnum = $myedjqr/50;
		$jqr =0;

		$endti=strtotime("2018-04-24 15:00:00");
		if(time() > $endti){
			//$jqr = (time()-$endti)*3.0864;
		}
		
		$sql_xe=M()->query("select SUM(ylcs) as sum from `qq3479015851_a_sign` WHERE  `hdid` ='5' and addtime > '$timestr1' and addtime <'$timestr2'  ;");
		$num_xe=$sql_xe[0]['sum'];//每日已领取多少
		if($num_xe >= $xets){
			$this->error('今日奖励已发放完');	
		}
		
		$sql2 = M('ASignRule')->where(array('id' => 5))->select();
		$yscoin=$sql2[0]['coinname'];//币种
		$coinnum=$sql2[0]['num'];//送几个
		$ed=$sql2[0]['num2'];//满多少钱送
		$ysnum3=$sql2[0]['num3'];//上级基数
		$ysnum4=$sql2[0]['num4'];//上上级基数

		//是否有奖励可领取
		$sql6_1 = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where userid = '$userid'  and  addtime > '$timestr1' ");
		$sql6_2 = M()->query("SELECT sum(mum) as sum FROM  `qq3479015851_trade_log` where  peerid = '$userid' and  addtime > '$timestr1' ");
		$myed=$sql6_1[0]['sum'] + $sql6_2[0]['sum'];
		$beishu=$myed/$ed;
		$yxnumber=floor($beishu);//可领次数
		//if($userid==111908) $yxnumber = 6000;
		$yxnumber = ($yxnumber > 3500) ? 3500:$yxnumber;
		$sql_num=M()->query("select SUM(ylcs) as sum from `qq3479015851_a_sign` WHERE   `userid` ='$userid' and  `hdid` ='5' and addtime > '$timestr1'  and iftjr = 0  ;");
		$selectnum=$sql_num[0]['sum'];//已领次数
		$lqcs=$yxnumber - $selectnum;//剩余领取次数
		if($lqcs <=0) $this->error('您还没有可领取的奖励');	
		//改本人账户余额
		$hd5_getmoney=$coinnum*$lqcs;//自己获得多少(可用与冻结之和)
		$maxnum=500;//每个账户每天可用资金限额(500个币)
		//若冻结超过12000，每天可用资金限额降为300个币
		$sql_300 = M()->query("SELECT `cnut` FROM  `a_wakuang` where userid = '$userid' order by wid desc limit 1 ");
		if($sql_300[0]['cnut'] >= $usernum){
			$maxnum=300;//每个账户每天可用资金限额(300个币)
		}
		//每天领取了多少个
		if($selectnum >= $maxnum){
			$keyong = 0;
			$dongjie = $hd5_getmoney;
		}else{
			if($selectnum +  $lqcs >$maxnum){
				$keyong = $maxnum - $selectnum;
				$dongjie = $lqcs - $keyong;
			}else{
				$keyong = $lqcs;
				$dongjie = 0;
			}
			
		}
		//发放奖励
		$allpri1 = $allpri = $keyong + $dongjie;
		//用户冻结最大限制
		if($allpri > $user_max_num){
			$dongjie = 0;
		}
		if($keyong){
			$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$keyong WHERE  `userid` ='$userid';");
			
		}
		if($dongjie){
			$yscoind = $yscoin."d";
			$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoind` =  `$yscoind`+$dongjie WHERE  `userid` ='$userid';");
			$isck=M()->query("select userid from `a_wakuang` WHERE  `userid` ='$userid'");
			if($isck){
				M()->execute("UPDATE  `a_wakuang` SET  `cnut` =  cnut+$dongjie WHERE  `userid` ='$userid';");
			}else{
				M()->execute("INSERT INTO `a_wakuang` (`wid`, `userid`, `cnut`) VALUES (NULL, '$userid', '$dongjie');");
			}
		}
		if($keyong || $dongjie) $sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`lqcs` ,`ylcs` ,`coinname`,`num` ,`addtime`) VALUES (NULL ,  '$userid',  '5',  '0','$allpri','$yscoin','$allpri1', '$time');");

		//查是否有上两级推荐人
		$sql_tjr=M()->query("select `invit_1`,`invit_2` from `qq3479015851_user` WHERE  `id` ='$userid'");
		$tjr=$sql_tjr[0]['invit_1'];
		$tjr2=$sql_tjr[0]['invit_2'];
		//如果有推荐人1
		if($tjr){
			$tjr_num=$allpri1 * $ysnum3;//推荐人获得多少
			$tjr_num = $tjr_num ? $tjr_num : 0;
			
			$sql_num_1=M()->query("select SUM(ylcs) as sum from `qq3479015851_a_sign` WHERE   `userid` ='$tjr' and  `hdid` ='5' and addtime > '$timestr1' and addtime <'$timestr2' ;");
			$selectnum_1=$sql_num_1[0]['sum'];//已领次数
			if($selectnum_1 <3500){
				if($selectnum_1 >= $maxnum){
					$keyong =  $dongjie = 0;
					$dongjie = $tjr_num;
				}else{
					if($selectnum_1 +  $tjr_num >$maxnum){
						$keyong = $maxnum - $selectnum_1;
						$dongjie = $tjr_num - $keyong;
					}else{
						$keyong = $tjr_num;
						$dongjie = 0;
					}
	
				}
			}else{
				$keyong = 0;
				$dongjie = 0;
			}
			$allpri = 0;
			//发放奖励
			$allpri = $keyong + $dongjie;
			$allpri1 = $allpri * $coinnum;
			if($keyong){
				$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$keyong WHERE  `userid` ='$tjr';");
			}
			if($dongjie){
				$yscoind = $yscoin."d";
				$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoind` =  `$yscoind`+$dongjie WHERE  `userid` ='$tjr';");
				$isck=M()->query("select userid from `a_wakuang` WHERE  `userid` ='$userid'");
				if($isck){
					M()->execute("UPDATE  `a_wakuang` SET  `cnut` =  cnut+$dongjie WHERE  `userid` ='$tjr';");
				}else{
					M()->execute("INSERT INTO `a_wakuang` (`wid`, `userid`, `cnut`) VALUES (NULL, '$tjr', '$dongjie');");
				}
				
			}
			if($keyong || $dongjie) $sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`lqcs` ,`ylcs` ,`coinname`,`num` ,`addtime`,`iftjr`) VALUES (NULL ,  '$tjr',  '5',  '0','$allpri','$yscoin','$allpri1', '$time', '1');");
			
			
			
			
		}
		
		//如果有推荐人2
		if($tjr2){
			$tjr_num=$allpri1 * $ysnum4;//推荐人获得多少
			$tjr_num = $tjr_num ? $tjr_num : 0;
			
			$sql_num_1=M()->query("select SUM(ylcs) as sum from `qq3479015851_a_sign` WHERE   `userid` ='$tjr2' and  `hdid` ='5' and addtime > '$timestr1' and addtime <'$timestr2' ;");
			$selectnum_1=$sql_num_1[0]['sum'];//已领次数
			if($selectnum_1 <3500){
				if($selectnum_1 >= $maxnum){
					$keyong =  $dongjie = 0;
					$dongjie = $tjr_num;
				}else{
					if($selectnum_1 +  $tjr_num >$maxnum){
						$keyong = $maxnum - $selectnum_1;
						$dongjie = $tjr_num - $keyong;
					}else{
						$keyong = $tjr_num;
						$dongjie = 0;
					}
	
				}
			}else{
						$keyong = 0;
						$dongjie = 0;
			}
			//发放奖励 
			$allpri = $keyong + $dongjie;
			$allpri1 = $allpri * $coinnum;
			if($keyong){
				$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$keyong WHERE  `userid` ='$tjr2';");
			}
			if($dongjie){
				$yscoind = $yscoin."d";
				$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoind` =  `$yscoind`+$dongjie WHERE  `userid` ='$tjr2';");
				$isck=M()->query("select userid from `a_wakuang` WHERE  `userid` ='$userid'");
				if($isck){
					M()->execute("UPDATE  `a_wakuang` SET  `cnut` =  cnut+$dongjie WHERE  `userid` ='$tjr2';");
				}else{
					M()->execute("INSERT INTO `a_wakuang` (`wid`, `userid`, `cnut`) VALUES (NULL, '$tjr2', '$dongjie');");
				}
				
			}
			if($keyong || $dongjie) $sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`lqcs` ,`ylcs` ,`coinname`,`num` ,`addtime`,`iftjr`) VALUES (NULL ,  '$tjr2',  '5',  '0','$allpri','$yscoin','$allpri1', '$time', '2');");
			
			
		}
		
		

		$this->success('领取成功');
		
	}
	//邀请达人（奖cnut 1个）
	public function yaoqing()
	{
		$userid=userid();
		if(!$userid){
			$this->success('请先登录','/');
		}
		if($userid <320264){
			$rzstarttime=strtotime("2018-04-15 00:00:00");
			$rzendtime=strtotime("2018-04-20 23:59:59");
			$sql_rztime=M()->query("select id from `qq3479015851_user` WHERE  `invit_1` ='$userid' and `idcardauth` ='1' and addtime > $rzstarttime and addtime < $rzendtime;");//查该用户是否是15号至20号通过认证
			if(!$sql_rztime){
				$this->error('您不符合参与活动的条件');
			}
		}
		$sql6=M()->query("select count(id) as num from `qq3479015851_user` WHERE  `invit_1` ='$userid' and `idcardauth` ='1' ;");
		$klcs=$sql6[0]['num'];//可领取次数
		$sql_num=M()->query("select ylcs from `qq3479015851_a_sign` WHERE  `userid` ='$userid'  and `hdid`='6' order by id desc limit 1 ;");
		$selectnum=$sql_num[0]['ylcs'];//已领次数
		if(!$selectnum) $selectnum=0;
		$lqcs=$klcs - $selectnum;//剩余领取次数
		if($lqcs <=0){
			$this->error('您暂未有可领取的奖励');	
		}
		$time=time();
		$sql2 = M('ASignRule')->where(array('id' => 6))->select();
		$yscoin=$sql2[0]['coinname'];
		$ysnum=$sql2[0]['num'];
		$hd6_getmoney=$ysnum*$lqcs;
		$hd6_getmoney = $hd6_getmoney ? $hd6_getmoney : 0;
		$sql3=M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$yscoin` =  `$yscoin`+$hd6_getmoney WHERE  `userid` ='$userid';");
		if($sql3){$lqcs=0;}
		$sql4=M()->execute("INSERT INTO  `qq3479015851_a_sign` (`id` ,`userid` ,`hdid`,`coinname` ,`num` ,`lqcs` ,`ylcs` ,`addtime`) VALUES (NULL ,  '$userid',  '6',  '$yscoin','$hd6_getmoney', '$lqcs','$klcs', '$time');");
		
		$this->success("领取成功");
	}
	
	
	public function lists()
	{
		$userid=userid();
		if (!userid()) {
			redirect('/#login');
		}
		$sql=M()->query("select *  from `qq3479015851_a_sign` WHERE  `userid` ='$userid' order by id desc;");
		foreach($sql as $k => $v){
			$coinname=$v['coinname'];
			$cointitle_sql = M('Coin')->where(array('name' => $coinname))->select();
			$cointitle=$cointitle_sql[0]['title'];
			if($cointitle=='人民币')$cointitle='CNYT';
			$sql[$k]['coinname']=$cointitle;
			if($v['hdid']==1) $sql[$k]['hdid']="分红";	
			if($v['hdid']==2) $sql[$k]['hdid']="注册";	
			if($v['hdid']==3) $sql[$k]['hdid']="认证";	
			if($v['hdid']==4) $sql[$k]['hdid']="排行";	
			if($v['hdid']==5) $sql[$k]['hdid']="交易";	
			if($v['hdid']==6) $sql[$k]['hdid']="邀请";	
			if($v['iftjr']==0) $sql[$k]['iftjr']="自己";	
			if($v['iftjr']==1) $sql[$k]['iftjr']="一级";	
			if($v['iftjr']==2) $sql[$k]['iftjr']="二级";	
		}
		//print_r($sql);
		$this->assign('list', $sql);
		$this->display();
	}
}

?>