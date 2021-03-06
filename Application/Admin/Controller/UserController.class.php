<?php
namespace Admin\Controller;

use Think\Exception;

class UserController extends AdminController
{
	public function index($name = NULL, $field = NULL, $status = NULL)
	{
		//$this->checkUpdata();
		//$where = array();
		$where=" 1 ";
		if ($field && $name) {
			//$where[$field] = $name;
			if($field=="awardid" &&($name==7 || $name==9)){
				$where = " (`awardid`=7 or `awardid`=9) ";
			}else{
				$where = "`".$field."`='".$name."'";
			}

		}

		if ($status) {
			if($status>2){
				switch($status){
					case "3":
						$where = $where." and `awardstatus`=1 ";
						break;
					case "4":
						$where = $where." and `awardstatus`=0 ";
						break;
					case "5":
						$where = $where." and `idcardauth`=1 ";
						break;
					case "6":
						$where = $where." and `idcardauth`=0 ";
						break;
					case "9":
						$where = $where." and `idcardauth`=0 and idcardimg1 !='' and status !=0 ";
						break;
				}

			}else{

				$where = $where." and `status`=".($status-1);
			}
		}

		$count = M('User')->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = M('User')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$chong = $ti = $tui = $tuir =$wall =$wd = $rmb = $fen = $fens = 0;
			$list[$k]['invit_1'] = M('User')->where(array('id' => $v['invit_1']))->getField('username');
			$chong = M()->query("SELECT sum(num) as sum FROM  `a_ctc` where uid ='".$v['id']."' and type = 1 and stu = 2 ");
			$list[$k]['chong'] = $chong[0]['sum'];
			$ti = M()->query("SELECT sum(num) as sum FROM  `a_ctc` where uid ='".$v['id']."' and type = 2 and stu = 2 ");
			$list[$k]['ti'] = $ti[0]['sum'];
			$tui = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 ='".$v['id']."'  ");
			$list[$k]['tui'] = $tui[0]['count'];
			$tuir = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 ='".$v['id']."' and idcardauth =1  ");
			$list[$k]['tuir'] = $tuir[0]['count'];
			$wall = M()->query("SELECT sum(ylcs) as sum FROM  `qq3479015851_a_sign` where userid ='".$v['id']."' and hdid = 5 ");
			$list[$k]['wall'] = round($wall[0]['sum'],2);
			$wd = M()->query("SELECT cnut FROM  `a_wakuang` where userid ='".$v['id']."' ");
			$list[$k]['wd'] = $wd[0]['cnut'];
			$rmb = M()->query("SELECT cny,cnyd FROM  `qq3479015851_user_coin` where userid ='".$v['id']."' ");
			$list[$k]['cny'] = round($rmb[0]['cny'],2);
			$list[$k]['cnyd'] = round($rmb[0]['cnyd'],2);
			$fen = M()->query("SELECT sum(num) as sum FROM  `qq3479015851_a_sign` where userid ='".$v['id']."' and  hdid = 1 ");
			$list[$k]['fen'] = round($fen[0]['sum'],2);
			$ktime = strtotime("00:00:00");
			$fens = M()->query("SELECT sum(num) as sum FROM  `qq3479015851_a_sign` where userid ='".$v['id']."' and  hdid = 1 and addtime > $ktime ");
			$list[$k]['fens'] = round($fens[0]['sum'],2);
			
			
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function sell($uid)
	{
		$mobile = M('User')->where(array('id' => $uid))->getField('moble');
		if (!$mobile) {
			$this->error('未手机认证！');
		}
		$idcardauth = M('User')->where(array('id' => $uid))->getField('idcardauth');
		if (!$idcardauth) {
			$this->error('您还未通过实名认证！');
		}
		$eos = M('UserCoin')->where(array('userid' => $uid))->getField('eosd');
		if ($eos > 0) {
			$this->error('当前账户已经冻结'.$eos." 本次未发放奖励");
		}
		$bi = 1;
		if($uid > 312312 && $uid < 315863 ) $bi = 2;
		$rs = M('UserCoin')->where(array('userid' => $uid))->save(array("eosd" => $bi));
		if($rs){

			$this->error('恭喜您，成功领取'.$bi.'枚EOS！');
		}else{
			$this->error('领取失败，请重试！');
		}
	} 
	
	
	public function edit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$user = M('User')->where(array('id' => trim($id)))->find();

				if($user['backstage_vip'] != 0){
                    $user['vip'] = $user['backstage_vip'];
                    $user['fee_discount'] = $user['backstage_fee_discount'];
                }
				$this->data = $user;
			}
			
			$imgstr = $imgstr1 = "";
			if($user['idcardimg1']){
				$img_arr = array();
				$img_arr = explode("_",$user['idcardimg1']);

				foreach($img_arr as $k=>$v){
					$imgstr = $imgstr.'<img src="'.PC_URL.'/Upload/idcard/'.$v.'"  style="width:450px;height:220px;" />';
				}
				foreach($img_arr as $k1=>$v1){
					$imgstr1 = $imgstr1.'<img src="'.WAP_URL.'/Upload/idcard/'.$v1.'"  style="width:450px;height:220px;" />';
				}

				unset($img_arr);
			}
			
			$this->assign('userimg', $imgstr);
			$this->assign('userimg1', $imgstr1);

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			switch($_POST['awardid']){
				case 0:
					$_POST['awardname']="无奖品";
					break;
				case 1:
					$_POST['awardname']="苹果电脑";
					break;
				case 2:
					$_POST['awardname']="华为手机";
					break;
				case 3:
					$_POST['awardname']="1000元现金";
					break;
				case 4:
					$_POST['awardname']="小米手环";
					break;
				case 5:
					$_POST['awardname']="100元现金";
					break;
				case 6:
					$_POST['awardname']="10元现金";
					break;
				case 7:
					$_POST['awardname']="1元现金";
					break;
				case 8:
					$_POST['awardname']="无奖品";
					break;
				case 9:
					$_POST['awardname']="1元现金";
					break;
				case 10:
					$_POST['awardname']="无奖品";
					break;
				default:
					$_POST['awardid']=0;
					$_POST['awardname']="无奖品";
			}
			
			
			
			if ($_POST['password']) {
				$_POST['password'] = md5($_POST['password']);
			}
			else {
				unset($_POST['password']);
			}

			if ($_POST['paypassword']) {
				$_POST['paypassword'] = md5($_POST['paypassword']);
			}
			else {
				unset($_POST['paypassword']);
			}

			$_POST['mobletime'] = strtotime($_POST['mobletime']);

            $flag = false;
            if (isset($_POST['id'])) {
				if($_POST['del']) $_POST['idcardimg1']="";
				if(IS_ROOT !== 1){
				    unset($_POST['fee_discount']);
				    unset($_POST['backstage_fee_discount']);
                }
                $rs = M('User')->save($_POST);
            } else {
                $mo = M();
                $mo->execute('set autocommit=0');
                //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_coin write ');
                $rs[] = $mo->table('qq3479015851_user')->add($_POST);
                $rs[] = $mo->table('qq3479015851_user_coin')->add(array('userid' => $rs[0]));
                $flag = true;
            }

			if ($rs) {
                if ($flag) {
                    $mo->execute('commit');
                    //$mo->execute('unlock tables');
                }
                session('reguserId', $rs);
				$this->success('编辑成功！');
			}
			else {
                if ($flag) {
                    $mo->execute('rollback');
                }
				$this->error('编辑失败！');
			}
		}
	}

	public function status($id = NULL, $type = NULL, $moble = 'User',$awardid=0)
	{
		if (APP_DEMO) {
			//$this->error('测试站暂时不能修改！');
		}
		if (empty($id)) {
			$this->error('请选择会员！');
		}

		if (empty($type)) {
			$this->error('参数错误！');
		}
		$ds = $id;
		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}
		$where['id'] = array('in', $id);
		
		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
                $_where = array(
                    'userid' => $where['id'],
                );
                M('UserCoin')->where($_where)->delete();
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;
			
		case 'idauth': 
			$data = array('idcardauth' => 1, 'addtime' => time());
			//新增新注册用户赠送100 推荐人赠送50。  总量300万送万停止
			if(!is_array($ds)){
				$dss[0] = $ds;
			}else{
				$dss = $ds;
			}
				
		foreach($dss as $k=>$v){
				//自己认证查询自己推荐
//				$istjinfo = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 = '".$v."' and idcardauth = 1 ");
//				$tjnum = $istjinfo[0]['count'];
//				//查询已发放数量
//				$tjinfo = M()->query("SELECT * FROM  `qq3479015851_user` where id = '".$v."'  ");
//				$num = $tjnum - $tjinfo[0]['isnrts'];
//				$pri = $num *100;
//				if($num){
//					//推荐人已认证
//					M()->execute("UPDATE `qq3479015851_user` SET  `isnrts` =  isnrts+$num WHERE id ='".$v."';");
//					M()->execute("UPDATE `qq3479015851_user_coin` SET  `cnut` =  cnut+$pri,`doge` =  doge+$pri,`cnutd` =  cnutd-$pri,`doged` =  doged-$pri  WHERE userid ='".$v."';");
//				}
//				
//				
//				
//				//查询上级id
//				$isinfo = M()->query("SELECT invit_1 FROM  `qq3479015851_user` where id = '".$v."' ");
//				$tjid = $isinfo[0]['invit_1'];
//				//上级推荐认证数量
//				$istjinfo = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 = '".$tjid."' and idcardauth = 1 ");
//				$tjnum = $istjinfo[0]['count'] +1;
//				//查询已发放数量
//				$tjinfo = M()->query("SELECT * FROM  `qq3479015851_user` where id = '".$tjid."'  ");
//				$num = $tjnum - $tjinfo[0]['isnrts'];
//				$pri = $num *100;
//				if($num){
//					if($tjinfo[0]['idcardauth']==1){
//						//推荐人已认证
//						M()->execute("UPDATE `qq3479015851_user` SET  `isnrts` =  isnrts+$num WHERE id ='".$tjid."';");
//						M()->execute("UPDATE `qq3479015851_user_coin` SET  `cnut` =  cnut+$pri,`doge` =  doge+$pri,`cnutd` =  cnutd-$pri,`doged` =  doged-$pri  WHERE userid ='".$tjid."';");
//					}
//				}
				
				
				//当前用户赠送100狗狗币/网红链；
//				$isnrt = M()->query("SELECT isnrt,isnrts,invit_1 FROM  `qq3479015851_user` where id = '".$v."'");
//				if(!$isnrt[0]['isnrt']){
//					M()->execute("UPDATE `qq3479015851_user` SET  `isnrt` =  1 WHERE id ='".$v."';");
//					M()->execute("UPDATE `qq3479015851_user_coin` SET  `doge` =  doge+100,`nrc` =  nrc+100 WHERE userid ='".$v."';");
//				}
				//自己推荐赠送50狗狗币/网红链
//				$istj = M()->query("SELECT count(id) as coun FROM  `qq3479015851_user` where invit_1 = '".$v."' and idcardauth = 1");
//				if($istj[0]['coun'] > $isnrt[0]['isnrts']){
//					$num = $istj[0]['coun'] - $isnrt[0]['isnrts'];
//					$pri = $num * 100;
//					if($num){
//						M()->execute("UPDATE `qq3479015851_user` SET  `isnrts` =  isnrts+$num WHERE id ='".$v."';");
//						M()->execute("UPDATE `qq3479015851_user_coin` SET  `cnut` =  cnut+$pri,`doge` =  doge+$pri  WHERE userid ='".$v."';");
//					}
//				}
//			//更新推荐人的赠送50狗狗币/网红链
//				if($isnrt[0]['invit_1']){
//					$tjid = $isnrt[0]['invit_1'];
//					$isnrt_tj = M()->query("SELECT id,isnrts FROM  `qq3479015851_user` where id = '".$tjid."' and idcardauth = 1");
//					if($isnrt_tj){
//						$istj = M()->query("SELECT count(id) as coun FROM  `qq3479015851_user` where invit_1 = '".$tjid."' and idcardauth = 1");
//						if($istj[0]['coun'] > $isnrt_tj[0]['isnrts']){
//							$num = $istj[0]['coun'] - $isnrt_tj[0]['isnrts'];
//							$pri = $num * 100;
//							if($num){
//								M()->execute("UPDATE `qq3479015851_user` SET  `isnrts` =  isnrts+$num WHERE id ='".$tjid."';");
//								M()->execute("UPDATE `qq3479015851_user_coin` SET  `cnut` =  cnut+$pri,`doge` =  doge+$pri WHERE userid ='".$tjid."';");
//							}
//						}
//					}
//				}
				
				
			}
			break;
			
		case 'notidauth': 
			$data = array('idcardauth' => 0);
			break;
			
		case 'award';
		
			switch($awardid){
				case 0:
					$awardname="无奖品";
					break;
				case 1:
					$awardname="苹果电脑";
					break;
				case 2:
					$awardname="华为手机";
					break;
				case 3:
					$awardname="1000元现金";
					break;
				case 4:
					$awardname="小米手环";
					break;
				case 5:
					$awardname="100元现金";
					break;
				case 6:
					$awardname="10元现金";
					break;
				case 7:
					$awardname="1元现金";
					break;
				case 8:
					$awardname="无奖品";
					break;
				case 9:
					$awardname="1元现金";
					break;
				case 10:
					$awardname="无奖品";
					break;
				default:
					$awardid=0;
					$awardname="无奖品";
			}
			$data = array('awardstatus' => 0, 'awardid' => $awardid,'awardname'=>$awardname);
			
			break;
		
		default:
			$this->error('操作失败！');
		}
		
		
		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function admin($name = NULL, $field = NULL, $status = NULL)
	{
		//$this->error('禁止修改！');
		$DbFields = M('Admin')->getDbFields();

		if (!in_array('email', $DbFields)) {
			M()->execute('ALTER TABLE `qq3479015851_admin` ADD COLUMN `email` VARCHAR(200)  NOT NULL   COMMENT \'\' AFTER `id`;');
		}

		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('Admin')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('Admin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function adminEdit()
	{
		//$this->error('禁止修改！');
		if (empty($_POST)) {
			if (empty($_GET['id'])) {
				$this->data = null;
			}
			else {
				$this->data = M('Admin')->where(array('id' => trim($_GET['id'])))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$input = I('post.');

			if (!check($input['username'], 'username')) {
				$this->error('用户名格式错误！');
			}

			if ($input['nickname'] && !check($input['nickname'], 'A')) {
				$this->error('昵称格式错误！');
			}

			if ($input['password'] && !check($input['password'], 'password')) {
				$this->error('登录密码格式错误！');
			}

			if ($input['moble'] && !check($input['moble'], 'moble')) {
				$this->error('手机号码格式错误！');
			}

			if ($input['email'] && !check($input['email'], 'email')) {
				$this->error('邮箱格式错误！');
			}

			if ($input['password']) {
				$input['password'] = md5($input['password']);
			}
			else {
				unset($input['password']);
			}

			if ($_POST['id']) {
				$rs = M('Admin')->save($input);
			}
			else {
				$_POST['addtime'] = time();
				$rs = M('Admin')->add($input);
			}

			if ($rs) {
				$this->success('编辑成功！');
			}
			else {
				$this->error('编辑失败！');
			}
		}
	}

	public function adminStatus($id = NULL, $type = NULL, $moble = 'Admin')
	{
		//$this->error('禁止修改！');
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}

		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function auth()
	{
		//$list = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
		$authGroup = M('AuthGroup');
		$condition['module'] = 'admin';
		$list = $authGroup->order('id asc')->where($condition)->select();
		$list = int_to_string($list);
		$this->assign('_list', $list);
		$this->assign('_use_tip', true);
		$this->meta_title = '权限管理';
		$this->display();
	}

	public function authEdit()
	{
		if (empty($_POST)) {
			if (empty($_GET['id'])) {
				$this->data = null;
			}
			else {
				$this->data = M('AuthGroup')->where(array(
                    'module' => 'admin',
                    'type' => 1,//Common\Model\AuthGroupModel::TYPE_ADMIN
                ))->find((int) $_GET['id']);
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			if (isset($_POST['rules'])) {
				sort($_POST['rules']);
				$_POST['rules'] = implode(',', array_unique($_POST['rules']));
			}

			$_POST['module'] = 'admin';
			$_POST['type'] = 1;//Common\Model\AuthGroupModel::TYPE_ADMIN;
			$AuthGroup = D('AuthGroup');
			$data = $AuthGroup->create();

			if ($data) {
				if (empty($data['id'])) {
					$r = $AuthGroup->add();
				}
				else {
					$r = $AuthGroup->save();
				}

				if ($r === false) {
					$this->error('操作失败' . $AuthGroup->getError());
				}
				else {
					$this->success('操作成功!');
				}
			}
			else {
				$this->error('操作失败' . $AuthGroup->getError());
			}
		}
	}

	public function authStatus($id = NULL, $type = NULL, $moble = 'AuthGroup')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}

		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function authStart()
	{
		if (M('AuthRule')->where(array('status' => 1))->delete()) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function authAccess()
	{
		$this->updateRules();
		$auth_group = M('AuthGroup')->where(array(
			'status' => array('egt', '0'),
			'module' => 'admin',
			'type'   => 1,//Common\Model\AuthGroupModel::TYPE_ADMIN
			))->getfield('id,id,title,rules');
		$node_list = $this->returnNodes();
		$map = array(
            'module' => 'admin',
            'type' => 2,//Common\Model\AuthRuleModel::RULE_MAIN,
            'status' => 1
        );
		$main_rules = M('AuthRule')->where($map)->getField('name,id');
		$map = array(
            'module' => 'admin',
            'type' => 1,//Common\Model\AuthRuleModel::RULE_URL,
            'status' => 1
        );
		$child_rules = M('AuthRule')->where($map)->getField('name,id');
		$this->assign('main_rules', $main_rules);
		$this->assign('auth_rules', $child_rules);
		$this->assign('node_list', $node_list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->meta_title = '访问授权';
		$this->display();
	}

	protected function updateRules()
	{
		$nodes = $this->returnNodes(false);
		$AuthRule = M('AuthRule');
		$map = array(
			'module' => 'admin',
			'type'   => array('in', '1,2')
			);
		$rules = $AuthRule->where($map)->order('name')->select();
		$data = array();

		foreach ($nodes as $value) {
			$temp['name'] = $value['url'];
			$temp['title'] = $value['title'];
			$temp['module'] = 'admin';

			if (0 < $value['pid']) {
				$temp['type'] = 1;//Common\Model\AuthRuleModel::RULE_URL;
			}
			else {
				$temp['type'] = 2;//Common\Model\AuthRuleModel::RULE_MAIN;
			}

			$temp['status'] = 1;
			$data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;
		}

		$update = array();
		$ids = array();

		foreach ($rules as $index => $rule) {
			$key = strtolower($rule['name'] . $rule['module'] . $rule['type']);

			if (isset($data[$key])) {
				$data[$key]['id'] = $rule['id'];
				$update[] = $data[$key];
				unset($data[$key]);
				unset($rules[$index]);
				unset($rule['condition']);
				$diff[$rule['id']] = $rule;
			}
			else if ($rule['status'] == 1) {
				$ids[] = $rule['id'];
			}
		}

		if (count($update)) {
			foreach ($update as $k => $row) {
				if ($row != $diff[$row['id']]) {
					$AuthRule->where(array('id' => $row['id']))->save($row);
				}
			}
		}

		if (count($ids)) {
			$AuthRule->where(array(
				'id' => array('IN', implode(',', $ids))
				))->save(array('status' => -1));
		}

		if (count($data)) {
			$AuthRule->addAll(array_values($data));
		}

		if ($AuthRule->getDbError()) {
			trace('[' . 'Admin\\Controller\\UserController::updateRules' . ']:' . $AuthRule->getDbError());
			return false;
		}
		else {
			return true;
		}
	}

	public function authAccessUp()
	{
		if (isset($_POST['rules'])) {
			sort($_POST['rules']);
			$_POST['rules'] = implode(',', array_unique($_POST['rules']));
		}

		$_POST['module'] = 'admin';
		$_POST['type'] = 1;//Common\Model\AuthGroupModel::TYPE_ADMIN;
		$AuthGroup = D('AuthGroup');
		$data = $AuthGroup->create();

		if ($data) {
			if (empty($data['id'])) {
				$r = $AuthGroup->add();
			}
			else {
				$r = $AuthGroup->save();
			}

			if ($r === false) {
				$this->error('操作失败' . $AuthGroup->getError());
			}
			else {
				$this->success('操作成功!');
			}
		}
		else {
			$this->error('操作失败' . $AuthGroup->getError());
		}
	}

	public function authUser($group_id)
	{
		if (empty($group_id)) {
			$this->error('参数错误');
		}

		$auth_group = M('AuthGroup')->where(array(
			'status' => array('egt', '0'),
			'module' => 'admin',
			'type'   => 1,//Common\Model\AuthGroupModel::TYPE_ADMIN
			))->getfield('id,id,title,rules');
		$prefix = C('DB_PREFIX');
/* 		$l_table = $prefix . 'ucenter_member';//Common\Model\AuthGroupModel::MEMBER;
		$r_table = $prefix . 'auth_group_access';//Common\Model\AuthGroupModel::AUTH_GROUP_ACCESS;
		$model = M()->table($l_table . ' m')->join($r_table . ' a ON m.id=a.uid');
		$_REQUEST = array();
		$list = $this->lists($model, array(
			'a.group_id' => $group_id,
			'm.status'   => array('egt', 0)
			), 'm.id asc', null, 'm.id,m.username,m.nickname,m.last_login_time,m.last_login_ip,m.status'); */

			
		$l_table = $prefix . 'auth_group_access';//Common\Model\AuthGroupModel::MEMBER;
		$r_table = $prefix . 'admin';//Common\Model\AuthGroupModel::AUTH_GROUP_ACCESS;
		$model = M()->table($l_table . ' a')->join($r_table . ' m ON m.id=a.uid');
		$_REQUEST = array();
		$list = $this->lists($model, array(
			'a.group_id' => $group_id,
			//'m.status'   => array('egt', 0)
			), 'a.uid desc', null, 'm.id,m.username,m.nickname,m.last_login_time,m.last_login_ip,m.status');
			
			
        int_to_string($list);
		
		//var_dump($list);
		
		$this->assign('_list', $list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->meta_title = '成员授权';
		$this->display();
	}

	public function authUserAdd()
	{
		$uid = I('uid');

		if (empty($uid)) {
			$this->error('请输入后台成员信息');
		}

		if (!check($uid, 'd')) {
			$user = M('Admin')->where(array('username' => $uid))->find();

			if (!$user) {
				$user = M('Admin')->where(array('nickname' => $uid))->find();
			}

			if (!$user) {
				$user = M('Admin')->where(array('moble' => $uid))->find();
			}

			if (!$user) {
				$this->error('用户不存在(id 用户名 昵称 手机号均可)');
			}

			$uid = $user['id'];
		}

		$gid = I('group_id');

		if ($res = M('AuthGroupAccess')->where(array('uid' => $uid))->find()) {
			if ($res['group_id'] == $gid) {
				$this->error('已经存在,请勿重复添加');
			} else {
				$res = M('AuthGroup')->where(array('id' => $gid))->find();

				if (!$res) {
					$this->error('当前组不存在');
				}

				$this->error('已经存在[' . $res['title'] . ']组,不可重复添加');
			}
		}

		$AuthGroup = D('AuthGroup');

		if (is_numeric($uid)) {
			if (is_administrator($uid)) {
				$this->error('该用户为超级管理员');
			}

			if (!M('Admin')->where(array('id' => $uid))->find()) {
				$this->error('管理员用户不存在');
			}
		}

		if ($gid && !$AuthGroup->checkGroupId($gid)) {
			$this->error($AuthGroup->error);
		}

		if ($AuthGroup->addToGroup($uid, $gid)) {
			$this->success('操作成功');
		}
		else {
			$this->error($AuthGroup->getError());
		}
	}

	public function authUserRemove()
	{
		$uid = I('uid');
		$gid = I('group_id');

		if ($uid == UID) {
			$this->error('不允许解除自身授权');
		}

		if (empty($uid) || empty($gid)) {
			$this->error('参数有误');
		}

		$AuthGroup = D('AuthGroup');

		if (!$AuthGroup->find($gid)) {
			$this->error('用户组不存在');
		}

		if ($AuthGroup->removeFromGroup($uid, $gid)) {
			$this->success('操作成功');
		}
		else {
			$this->error('操作失败');
		}
	}

	public function log($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserLog')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function logEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserLog')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                unset($_POST['id']);
                if (M()->table('qq3479015851_user_log')->where(array('id'=>$id))->save($_POST)) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                if (M()->table('qq3479015851_user_log')->add($_POST)) {
                    $this->success('添加成功！');
                } else {
                    $this->error('添加失败！');
                }
            }

		}
	}

	public function logStatus($id = NULL, $type = NULL, $moble = 'UserLog')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}

		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function qianbao($name = NULL, $field = NULL, $coinname = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		if ($coinname) {
			$where['coinname'] = trim($coinname);
		}

		$count = M('UserQianbao')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserQianbao')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function qianbaoEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserQianbao')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                unset($_POST['id']);
                if (M()->table('qq3479015851_user_qianbao')->where(array('id' => $id))->save($_POST)) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                if (M()->table('qq3479015851_user_qianbao')->add($_POST)) {
                    $this->success('添加成功！');
                } else {
                    $this->error('添加失败！');
                }
            }
		}
	}

	public function qianbaoStatus($id = NULL, $type = NULL, $moble = 'UserQianbao')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}

		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function bank($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserBank')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserBank')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function bankEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserBank')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                if (M()->table('qq3479015851_user_bank')->where(array('id' => $id))->save($_POST)) {
                    $this->success('编辑成功！');
                }
                else {
                    $this->error('编辑失败！');
                }
            } else {
                if (M()->table('qq3479015851_user_bank')->add($_POST)) {
                    $this->success('添加成功！');
                }
                else {
                    $this->error('添加失败！');
                }
            }
		}
	}

	public function bankStatus($id = NULL, $type = NULL, $moble = 'UserBank')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}

		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}
	
	public function coins($coin = NULL, $mobile = NULL, $num = NULL)
	{
		if(!$coin) $this->error('编辑失败1！');
		$coinnamed = $coin."d";
		if(!$mobile) $this->error('编辑失败2！');
		if($num > 0){
			$ds = M('UserCoin')->where(array('userid' => $mobile))->getField($coinnamed);
			if($ds < $num)  $this->error('解冻数量错误，最大：'.$ds);
		}elseif($num < 0){
			$ds = M('UserCoin')->where(array('userid' => $mobile))->getField($coin);
			if($ds < -$num)  $this->error('冻结数量错误，最大：'.$ds);
		}else{
			$this->error('编辑失败3！');
		}
		M()->execute("UPDATE  `qq3479015851_user_coin` SET  `$coin` = $coin + $num, `$coinnamed` = $coinnamed - $num WHERE userid = '$mobile';");
		$this->success('操作成功，请核对！');
		
	}
	public function coin($name = NULL, $field = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}elseif ($field == 'moble') {
				$where['userid'] = M('User')->where(array('moble' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		$count = M('UserCoin')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserCoin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
			$list[$k]['moble'] = M('User')->where(array('id' => $v['userid']))->getField('moble');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function coinEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserCoin')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}
			
            if ($id) {
                if (M('UserCoin')->save($_POST)) {
                    $this->success('编辑成功！');
                }
                else {
                    $this->error('编辑失败！');
                }
            } else {
                if (M()->table('qq3479015851_user_coin')->add($_POST)) {
                    $this->success('添加成功！');
                }
                else {
                    $this->error('添加失败！');
                }
            }
		}
	}

	public function coinLog($userid = NULL, $coinname = NULL)
	{
		$data['userid'] = $userid;
		$data['username'] = M('User')->where(array('id' => $userid))->getField('username');
		$data['coinname'] = $coinname;
		$data['zhengcheng'] = M('UserCoin')->where(array('userid' => $userid))->getField($coinname);
		$data['dongjie'] = M('UserCoin')->where(array('userid' => $userid))->getField($coinname . 'd');
		$data['zongji'] = $data['zhengcheng'] + $data['dongjie'];
		$data['chongzhicny'] = M('Mycz')->where(array(
			'userid' => $userid,
			'status' => array('neq', '0')
			))->sum('num');
		$data['tixiancny'] = M('Mytx')->where(array('userid' => $userid, 'status' => 1))->sum('num');
		$data['tixiancnyd'] = M('Mytx')->where(array('userid' => $userid, 'status' => 0))->sum('num');

		if ($coinname != 'cny') {
			$data['chongzhi'] = M('Myzr')->where(array(
				'userid' => $userid,
				'status' => array('neq', '0')
				))->sum('num');
			$data['tixian'] = M('Myzc')->where(array('userid' => $userid, 'status' => 1))->sum('num');
		}

		$this->assign('data', $data);
		$this->display();
	}

	public function goods($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserGoods')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserGoods')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function goodsEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserGoods')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                unset($_POST['id']);
                if (M()->table('qq3479015851_user_goods')->where(array('id'=>$id))->save($_POST)) {
                    $this->success('编辑成功！');
                }
                else {
                    $this->error('编辑失败！');
                }
            } else {
                if (M()->table('qq3479015851_user_goods')->add($_POST)) {
                    $this->success('添加成功！');
                }
                else {
                    $this->error('添加失败！');
                }
            }
		}
	}

	public function goodsStatus($id = NULL, $type = NULL, $moble = 'UserGoods')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}

		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function setpwd()
	{
		//$this->error('禁止修改！');
		if (IS_POST) {
			defined('APP_DEMO') || define('APP_DEMO', 0);

			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$oldpassword = $_POST['oldpassword'];
			$newpassword = $_POST['newpassword'];
			$repassword = $_POST['repassword'];

			if (!check($oldpassword, 'password')) {
				$this->error('旧密码格式错误！');
			}

			if (md5($oldpassword) != session('admin_password')) {
				$this->error('旧密码错误！');
			}

			if (!check($newpassword, 'password')) {
				$this->error('新密码格式错误！');
			}

			if ($newpassword != $repassword) {
				$this->error('确认密码错误！');
			}

			if (D('Admin')->where(array('id' => session('admin_id')))->save(array('password' => md5($newpassword)))) {
				$this->success('登陆密码修改成功！', U('Login/loginout'));
			}
			else {
				$this->error('登陆密码修改失败！');
			}
		}

		$this->display();
	}

	public function handleAward($id, $status)
    {
        //$this->ajaxReturn(json_encode(array('count' => '1')), 'JSON');

        if(!isset($id))
            $this->error("审核失败");

        if(intval($status) !== 0)
            $this->error("不能审核已经发放的或未知状态的奖品");

        $mo = M();
        $condition['id'] = array('eq', $id);
        $condition['status'] = array('eq', 0);
        $log = $mo->table("award_log")->where($condition)->find();

        $aiid = $log['aiid'];
        $uid = $log['uid'];
        $type = '';
        $score = 0;
        // 4 -> 0.5EOS
        // 7 -> 10000 Doge
        // 10 -> 50 PSBC
        // 12 -> 10 CNUT
        // 11 -> 5 PSBC
        switch ($aiid) {
            case 4:
                $type = 'eos';
                $score = 0.5;
                break;
            case 7:
                $type = 'doge';
                $score = 1000;
                break;
            case 10:
                $type = 'psbc';
                $score = 50;
                break;
            case 12:
                $type = 'cnut';
                $score = 10;
                break;
            case 11:
                $type = 'psbc';
                $score = 5;
                break;
            default:
                $this->error("实物类或高价值奖品不能在此审核");
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables award_log write, qq3479015851_user_coin write');

        $rs = array();      
		$sql = "update award_log set status=1, modify_time=\"".date("Y-m-d H:i:s",time())."\" where id=".$id;

        $rs[] = $mo->execute($sql);

		$sql_uc = "update qq3479015851_user_coin set ".$type."=".$type."+".$score." where userid=".$uid;
		$rs[] = $mo->execute($sql_uc);//$mo->table('qq3479015851_user_coin')->where("userid=".$uid)->setInc($type, $score);
		
		
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success('审核发放成功！');
        }
        else {
            $mo->execute('rollback');
            $this->error(APP_DEBUG ? implode('|', $rs) : '审核发放成功!');
        }
    }
	
	public function award($name = NULL, $field = NULL, $status = NULL)
	{
		$where="";

		if ($field && $name) {
			//$where[$field] = $name;
            switch ($field){
                case "truename":
                    $where = "user.".$field."='".$name."'";
                    break;
                case "username":
                    $where = "user.".$field."='".$name."'";
                    break;
                case "moble":
                    $where = "user.".$field."='".$name."'";
                    break;
                case "name":
                    $where = "item.".$field."='".$name."'";
                    break;
                default:
                    break;
            }
		}

		if($status){
			$where = $where." and log.status=".($status);
		}
				
		
		$mo = M();
		$count = $mo->table('award_log')->table('award_log as log')
            ->join('qq3479015851_user as user on log.uid = user.id')->join('award_activity_item as item on log.aiid = item.id')
            ->where($where)->count();
        $page = new \Think\Page($count, 10);
        $show = $page->show();

        $list = $mo->table('award_log as log')
            ->join('qq3479015851_user as user on log.uid = user.id')->join('award_activity_item as item on log.aiid = item.id')
            ->field('log.id as id, log.aiid as aiid, log.create_time as create_time, log.modify_time as modify_time, log.status as status, user.username as username, user.moble as moble, user.truename as truename, item.name as itemname, case when log.modify_time is not null then log.modify_time else \'\' end as issued_time ')
            ->where($where)->order('log.create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function awardStatus($id = NULL, $type = NULL,$status=NUll, $moble = 'UserAward')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('请选择要操作的记录！');
		}

		if (empty($type)) {
			$this->error('参数错误！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'dealaward':
			if(empty($status)){
				$this->error("参数错误！");
			}
			$data=array('status' => $status,'dealtime'=>time());
			break;
		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败');
		}
		
		if (M($moble)->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
		
		
	}
	
	public function checkUpdata()
	{
		if (!S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata')) {
			$list = M('Menu')->where(array(
				'url' => 'User/index',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/index', 'title' => '用户管理', 'pid' => 3, 'sort' => 1, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/index',
					'pid' => array('neq', 0)
					))->save(array('title' => '用户管理', 'pid' => 3, 'sort' => 1, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/admin',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/admin', 'title' => '管理员管理', 'pid' => 3, 'sort' => 2, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/admin',
					'pid' => array('neq', 0)
					))->save(array('title' => '管理员管理', 'pid' => 3, 'sort' => 2, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/auth',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/auth', 'title' => '权限列表', 'pid' => 3, 'sort' => 3, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->save(array('title' => '权限列表', 'pid' => 3, 'sort' => 3, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/log',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/log', 'title' => '登陆日志', 'pid' => 3, 'sort' => 4, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/log',
					'pid' => array('neq', 0)
					))->save(array('title' => '登陆日志', 'pid' => 3, 'sort' => 4, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/qianbao',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/qianbao', 'title' => '用户钱包', 'pid' => 3, 'sort' => 5, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/qianbao',
					'pid' => array('neq', 0)
					))->save(array('title' => '用户钱包', 'pid' => 3, 'sort' => 5, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/bank',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/bank', 'title' => '提现地址', 'pid' => 3, 'sort' => 6, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/bank',
					'pid' => array('neq', 0)
					))->save(array('title' => '提现地址', 'pid' => 3, 'sort' => 6, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/coin',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/coin', 'title' => '用户财产', 'pid' => 3, 'sort' => 7, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/coin',
					'pid' => array('neq', 0)
					))->save(array('title' => '用户财产', 'pid' => 3, 'sort' => 7, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/goods',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/goods', 'title' => '联系地址', 'pid' => 3, 'sort' => 8, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/goods',
					'pid' => array('neq', 0)
					))->save(array('title' => '联系地址', 'pid' => 3, 'sort' => 8, 'hide' => 0, 'group' => '用户', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/edit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/index',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/edit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/edit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/status',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/index',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/status', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/status',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/adminEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/admin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/adminEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/adminEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/adminStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/admin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/adminStatus', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/adminStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authStatus', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authStart',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authStart', 'title' => '重新初始化权限', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authStart',
						'pid' => array('neq', 0)
						))->save(array('title' => '重新初始化权限', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authAccess',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authAccess', 'title' => '访问授权', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authAccess',
						'pid' => array('neq', 0)
						))->save(array('title' => '访问授权', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authAccessUp',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authAccessUp', 'title' => '访问授权修改', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authAccessUp',
						'pid' => array('neq', 0)
						))->save(array('title' => '访问授权修改', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authUser',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authUser', 'title' => '成员授权', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authUser',
						'pid' => array('neq', 0)
						))->save(array('title' => '成员授权', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authUserAdd',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authUserAdd', 'title' => '成员授权增加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authUserAdd',
						'pid' => array('neq', 0)
						))->save(array('title' => '成员授权增加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authUserRemove',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authUserRemove', 'title' => '成员授权解除', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authUserRemove',
						'pid' => array('neq', 0)
						))->save(array('title' => '成员授权解除', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/logEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/log',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/logEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/logEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/logStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/log',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/logStatus', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/logStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/qianbaoEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/qianbao',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/qianbaoEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/qianbaoEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/qianbaoStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/qianbao',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/qianbaoStatus', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/qianbaoStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/bankEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/bank',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/bankEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/bankEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/bankStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/bank',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/bankStatus', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/bankStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/coinEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/coin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/coinEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/coinEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/coinLog',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/coin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/coinLog', 'title' => '财产统计', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/coinLog',
						'pid' => array('neq', 0)
						))->save(array('title' => '财产统计', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/goodsEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/goods',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/goodsEdit', 'title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/goodsEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => '编辑添加', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/goodsStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/goods',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/goodsStatus', 'title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/goodsStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => '修改状态', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/setpwd',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/setpwd', 'title' => '修改管理员密码', 'pid' => 3, 'sort' => 0, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/setpwd',
					'pid' => array('neq', 0)
					))->save(array('title' => '修改管理员密码', 'pid' => 3, 'sort' => 0, 'hide' => 1, 'group' => '用户', 'ico_name' => 'home'));
			}

			if (M('Menu')->where(array('url' => 'AuthManager/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'User/adminUser'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'AdminUser/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Userlog/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Userqianbao/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Userbank/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Usercoin/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Usergoods/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata', 1);
		}
	}
	//预警
	public function myyj($name = NULL, $field = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'uid') {
				$where['uid'] = $name;
			}elseif ($field == 'mobile') {
				$telephoneid = M('User')->where(array('mobile' => $name))->getField('id');
				$where['uid'] = $telephoneid;
			}elseif ($field == 'bid') {
				$bsid1 = M('Coin')->where(array('title' => $name))->getField('id');
				$where['bid'] = $bsid1;
			}
			else {
				$where[$field] = $name;
			}
		}
		$sql=M('UserYujing')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		foreach($sql as $k=>$v){
			$sqltel=M('User')->where(array('id' => $v['uid']))->find();
			$sqlbz=M('Coin')->where(array('id' => $v['bid']))->find();
			$sql[$k]['mobile']=$sqltel['moble'];
			$sql[$k]['bz']=$sqlbz['title'];
		}
		$count = M('UserYujing')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserCoin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
			$list[$k]['moble'] = M('User')->where(array('id' => $v['userid']))->getField('moble');
		}

		$this->assign('sql', $sql);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	//预警记录
	public function yjjl($name = NULL, $field = NULL)
	{
		$where = "";
		//$this->error($name.";".$field);
		if ($field && $name) {
			if ($field == 'uid') {
				$where= "where userid = ".$name;
			}elseif ($field == 'mobile') {
				$where= "where mobile = ".$name;
			}elseif ($field == 'bid') {
				$bsid1 = M('Coin')->where(array('title' => $name))->getField('id');
				$where= "where coin = ".$bsid1;
			}
			else {
				$where[$field] = $name;
				$where= "where ".$field." = ".$bsid1;
			}
		}
		//echo "SELECT * FROM a_sms ".$where."  order by sid desc limit 0,50 ";
		$sql= M()->query("SELECT * FROM a_sms ".$where."  order by sid desc limit 0,50 ");
		//print_r($sql);
		//$sql=Db::table('a_sms')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		//print_r($sql);
		foreach($sql as $k=>$v){
			$sqlbz=M('Coin')->where(array('id' => $v['coin']))->find();
			$sql[$k]['bz']=$sqlbz['title'];
			if($v['type']==1)$sql[$k]['type1']="上涨预警一";
			if($v['type']==2)$sql[$k]['type1']="上涨预警二";
			if($v['type']==3)$sql[$k]['type1']="上涨预警三";
			if($v['type']==4)$sql[$k]['type1']="下跌预警一";
			if($v['type']==5)$sql[$k]['type1']="下跌预警二";
			if($v['type']==6)$sql[$k]['type1']="下跌预警三";
		}
		//$count = M('UserYujing')->where($where)->count();
		//$Page = new \Think\Page($count, 15);
		//$show = $Page->show();
		//$list = M('UserCoin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		//foreach ($list as $k => $v) {
		//	$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		//	$list[$k]['moble'] = M('User')->where(array('id' => $v['userid']))->getField('moble');
		//}

		$this->assign('sql', $sql);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	//上币申请
	public function bzsxsq($name = NULL, $field = NULL)
	{
/*		$where = "";
		//$this->error($name.";".$field);
		if ($field && $name) {
			if ($field == 'uid') {
				$where= "where userid = ".$name;
			}elseif ($field == 'mobile') {
				$where= "where mobile = ".$name;
			}elseif ($field == 'bid') {
				$bsid1 = M('Coin')->where(array('title' => $name))->getField('id');
				$where= "where coin = ".$bsid1;
			}
			else {
				$where[$field] = $name;
				$where= "where ".$field." = ".$bsid1;
			}
		}
*/		//echo "SELECT * FROM a_sms ".$where."  order by sid desc limit 0,50 ";
		$sql= M()->query("SELECT * FROM a_bzsxsq ".$where."  order by id desc limit 0,50 ");
		//print_r($sql);
		//$sql=Db::table('a_sms')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		//print_r($sql);
		foreach($sql as $k=>$v){
			$sqlbz=M('Coin')->where(array('id' => $v['coin']))->find();
			$sql[$k]['bz']=$sqlbz['title'];
			if($v['type']==1)$sql[$k]['type1']="上涨预警一";
			if($v['type']==2)$sql[$k]['type1']="上涨预警二";
			if($v['type']==3)$sql[$k]['type1']="上涨预警三";
			if($v['type']==4)$sql[$k]['type1']="下跌预警一";
			if($v['type']==5)$sql[$k]['type1']="下跌预警二";
			if($v['type']==6)$sql[$k]['type1']="下跌预警三";
		}
		//$count = M('UserYujing')->where($where)->count();
		//$Page = new \Think\Page($count, 15);
		//$show = $Page->show();
		//$list = M('UserCoin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		//foreach ($list as $k => $v) {
		//	$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		//	$list[$k]['moble'] = M('User')->where(array('id' => $v['userid']))->getField('moble');
		//}

		$this->assign('sql', $sql);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	public function bzsxsqexcel($excel = NULL){
		$sql= M()->query("SELECT * FROM a_bzsxsq   order by id desc  ");
		print_r($sql);
			echo "<br/>";
			echo "ID号\t姓名\t分数\t\n";
			foreach($sql as $k=>$v){
				foreach($v as $k1=>$vo){
					echo $vo['id']."\t".$vo['data1'];	
				}
			}
	}

}

?>