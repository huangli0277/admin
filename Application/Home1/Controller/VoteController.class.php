<?php
namespace Home\Controller;

class VoteController extends HomeController
{
	public function index($invit = NULL)
	{

		
		//if (!userid()) {
			//redirect('/#login');
		//}
		
		$coin_list = M('VoteType')->order("zhichi desc")->select();
		if ($coin_list) {
			$boms['addtime'] = strtotime($coin_list[0]['addtime']);
			$boms['endtime'] = strtotime($coin_list[0]['endtime']);
			 $boms['stime'] = $boms['endtime'] - time();
			$boms['addtimes'] = date("Y年m月d日 H:i:s",$boms['addtime']);
			$boms['endtimes'] = date("Y年m月d日 H:i:s",$boms['endtime']);
			if($boms['addtime'] > time()){
				$boms['show'] = "开始投票";
			}
			foreach ($coin_list as $k => $v) {
				$vv = $v;
				
				//$v = M('Coin')->where(array('name'=>$v['coinname']))->find();
				//$voteC = M('Coin')->where(array('name'=>$vv['votecoin']))->find();
				//$voteC = C('coin')[$vv['votecoin']];
				//$list[$v['name']]['img'] = $v['img'];
				//$list[$v['name']]['name'] = $v['name'];
				
				$list[$vv['coinname']]['name'] = $vv['coinname'];
				$list[$vv['coinname']]['title'] = $vv['title'];
				$vs = M()->query('select sum(num) as sum from qq3479015851_vote where coinname="'.$vv['coinname'].'" and type = 1 ;');
				$list[$vv['coinname']]['zhichi'] = $vs[0]['sum'] +$vv['zhichi'];
				$vs1 = M()->query('select count(distinct(userid)) as count from qq3479015851_vote where coinname="'.$vv['coinname'].'" and type = 1 ;');
				$list[$vv['coinname']]['rens'] = $vs1[0]['count'] + $vv['rens'];
				$list[$vv['coinname']]['votecoin'] =  C('coin')[$vv['votecoin']]['title'];
				$list[$vv['coinname']]['knum'] = $vv['knum']; 
				$list[$vv['coinname']]['id'] = $vv['id'];
				$list[$vv['coinname']]['show1'] = $vv['show1'];
				$list[$vv['coinname']]['web'] = $vv['web'];
				$list[$vv['coinname']]['bps'] = $vv['bps'];
			} 
			 
 			$sort = array(  
					'direction' => 'SORT_DESC',
					'field'     => 'zhichi', 
			);  
			$arrSort = array();  
			foreach($list AS $uniqid => $row){  
				foreach($row AS $key=>$value){  
					$arrSort[$key][$uniqid] = $value;  
				}  
			} 
			
			
			if($sort['direction']){  
				array_multisort($arrSort[$sort['field']], constant($sort['direction']), $list);  
			}   		
			
			
			$this->assign('list', $list);
			$this->assign('boms', $boms);
		}else{
			$dasdas = 1;
			$this->assign('lists', $dasdas);
		}
		$userid = userid();
		$fx_url = "Vote/index/";
		$fx_url1 = "Vote/show/";
		if(userid()){
			$invits = M()->query('select invit from qq3479015851_user where id="'.$userid.'" ;');

			$invits = $invits[0]['invit'];
			if($invits){
				$fx_url = $fx_url."invit/".$invits."/";
				$fx_url1 = $fx_url1."invit/".$invits."/";
			}
		}
		$wap_url = WAP_URL.$fx_url1."id/";
		$fx_url = PC_URL.$fx_url;
		$this->assign('fx_url', $fx_url);
		$this->assign('wap_url', $wap_url);
		$this->assign('prompt_text', D('Text')->get_content('game_vote'));
		$this->display();
	}


	public function up($type = NULL,$coinname = NULL,$votecoin = NULL,$id = 0,$piao = 0)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}
		$coin_list = M('VoteType')->order("id desc")->select();
		$addtime = strtotime($coin_list[0]['addtime']);
		$endtime = strtotime($coin_list[0]['endtime']);
		if(time() < $addtime){
			$this->error('投票还未开始！');
		}
		if(time() > $endtime){
			$this->error('本期投票已结束！');
		}
		
		if (($type != 1) && ($type != 2)) {
			$this->error('参数错误！');
		}

		if (!is_array(D('Coin')->get_all_name_list())) {
			$this->error('参数错误2！');
		}
		$piao = intval($piao);
		if (!$piao) {
			$this->error('请先输入投票数量！');
		}
		$curVote = M('VoteType')->where(array('coinname'=>$coinname,'id'=>$id))->find();

		if($curVote){
			$curUserB = M('UserCoin')->where(array('userid' =>userid()))->getField($curVote['votecoin']);
			//var_dump($curUserB);
			$kou  = floatval($piao * $curVote['knum']);
			if(floatval($curUserB)<$kou){
				$this->error('投票所需要的'.$votecoin.'数量不足');
			}
			if($curVote['knum']<=0){
				//免费票
				$adtime = time() - 24 * 60 *60;
				$piaos = M()->query('select * from qq3479015851_vote where userid="'.userid().'" and addtime > "$adtime" ;');
				if ($piaos) {
					$this->error('此项目，24小时内仅能投1票！');
				}
				$kou = 0;
				$piao = 1;
			}
		}else{
			$this->error('不存在的投票类型');
		}
		//$this->error('测试中');
		//if (M('Vote')->where(array('userid' => userid(), 'coinname' => $coinname))->find()) {
			//$this->error('您已经投票过，不能再次操作！');
		//}
		if (1>3) {
			//$this->error('您已经投票过，不能再次操作！');
		}
		//else if(1==1) {
		else if(M('Vote')->add(array('userid' => userid(), 'coinname' => $coinname,'num' => $piao,'title' => $curVote['title'], 'type' => $type, 'addtime' => time(), 'status' => 1))) {
//            $zhichi = M('Vote')->where(array('coinname' => $coinname, 'type' => 1))->count();
//            $fandui = M('Vote')->where(array('coinname' => $coinname, 'type' => 2))->count();
//            $meta = array(
//                'zhichi' => $zhichi,
//                'fandui' => $fandui,
//                'zongji' => $zhichi + $fandui,
//                'bili' => round(($zhichi / $fandui) * 100, 2),
//            );
//            M('VoteType')->where(array('coinname' => $coinname))->save($meta);

			if($kou) M('UserCoin')->where(array('userid' =>userid()))->setDec($curVote['votecoin'],$kou);


			$this->success('您已经成功投'.$piao.'票！');
		}
		else {
			$this->error('投票失败！');
		}
	}

	public function uninstall()
	{

	}
}

?>