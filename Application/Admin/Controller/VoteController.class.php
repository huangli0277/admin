<?php
namespace Admin\Controller;

class VoteController extends AdminController
{
	public function index($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '')
	{
		//$this->checkUpdata();
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
			if ($field == 'username') {
				$map['userid'] = userid($name);
			}
			else {
				$map[$field] = $name;
			}
		}
 
		$data = M('Vote')->where($map)->order($order_set)->page($p, $r)->select();
		$count = M('Vote')->where($map)->count();
		$parameter['p'] = $p;
		$parameter['status'] = $status;
		$parameter['order'] = $order;
		$parameter['num'] = $num;
		$parameter['name'] = $name;
		$builder = new BuilderList();
		$builder->title('投票记录');
		$builder->titleList('投票列表', U('Vote/index'));
		$builder->button('delete', '删 除', U('Vote/status', array('model' => 'Vote', 'status' => -1)));
		$builder->setSearchPostUrl(U('Vote/index'));
		//$builder->search('order', 'select', array('id_desc' => 'ID降序', 'id_asc' => 'ID升序'));
		//$builder->search('status', 'select', array('全部状态', '禁用', '启用'));
		$builder->search('field', 'select', array('username' => '用户名'));
		$builder->search('name', 'text', '请输入查询内容');
		$builder->keyId();
		$builder->keyUserid();
		$builder->keyText('coinname', '币种');
		$builder->keyText('title', '名称');
		$builder->keyText('num', '票数');
		$builder->keyTime('addtime', '投票时间');
		$builder->keyStatus();
		$builder->data($data);
		$builder->pagination($count, $r, $parameter);
		$builder->display();
	}

	public function type($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '')
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
			if ($field == 'username') {
				$map['userid'] = userid($name);
			}
			else {
				$map[$field] = $name;
			}
		}

		$data = M('VoteType')->where($map)->order($order_set)->page($p, $r)->select();
		$count = M('VoteType')->where($map)->count();

        foreach ($data as $k => $vv) {
				$vs = M()->query('select sum(num) as sum from qq3479015851_vote where coinname="'.$vv['coinname'].'" and type = 1 ;');
				$data[$k]['zhichi'] = $vs[0]['sum'] +$vv['zhichi'];
				$vs1 = M()->query('select count(distinct(userid)) as count from qq3479015851_vote where coinname="'.$vv['coinname'].'" and type = 1 ;');
				$data[$k]['rens'] = $vs1[0]['count'] + $vv['rens'];
			
			
			
        }

		$parameter['p'] = $p;
		$parameter['status'] = $status;
		$parameter['order'] = $order;
		$parameter['type'] = $type;
		$parameter['name'] = $name;
		$builder = new BuilderList();
		$builder->title('投票类型');
		$builder->titleList('投票类型', U('Vote/type'));
		$builder->button('add', '添 加', U('Vote/edit'));
		$builder->button('delete', '删 除', U('Vote/status', array('model' => 'VoteType', 'status' => -1)));
		$builder->setSearchPostUrl(U('Vote/index'));
		//$builder->search('order', 'select', array('id_desc' => 'ID降序', 'id_asc' => 'ID升序'));
		//$builder->search('status', 'select', array('全部状态', '禁用', '启用'));
		$builder->search('field', 'select', array('coinname' => '币种'));
		$builder->search('name', 'text', '请输入查询内容');
		$builder->keyId();
		$builder->keyText('coinname', '币种');
		$builder->keyText('title', '名称');
		$builder->keyText('votecoin', '投票币种');
		$builder->keyText('zhichi', '支持票');
		$builder->keyText('rens', '人数');
		$builder->keyDoAction('Vote/edit?id=###', '编辑', '操作');
		$builder->data($data);
		$builder->pagination($count, $r, $parameter);
		$builder->display();
	}

	public function edit($id = NULL)
	{
		if (!empty($_POST)) {
			if (check($_POST['id'], 'd')) {
                $_POST['status'] = 1;
				$rs = M('VoteType')->save($_POST); 
			} 
			else { 
				if (M('VoteType')->where(array('coinname' => $_POST['coinname']))->find()) { 
					$this->error('已经存在');
				}

                $array = array(
                    'coinname' => $_POST['coinname'],
                    'title' => $_POST['title'],
					'votecoin' => $_POST['votecoin'],
					'zhichi' => $_POST['zhichi'],
					'rens' => $_POST['rens'],
					'show1' => $_POST['show1'],
					'web' => $_POST['web'],
					'knum' => $_POST['knum'],
					'addtime' => $_POST['addtime'],
					'endtime' => $_POST['endtime'],
					'bps' => $_POST['bps'],
                    'status' => 1,
                );
				$rs = M('VoteType')->add($array);
			}

			if ($rs) {
				$this->success('操作成功');
			}
			else {
				$this->error('操作失败');
			}
		}
		else {
			$builder = new BuilderEdit();
			$builder->title('投票类型管理');
			$builder->titleList('投票类型列表', U('Vote/type'));

			if ($id) {
				$builder->keyReadOnly('id', '类型id');
				$builder->keyHidden('id', '类型id');
				$data = M('VoteType')->where(array('id' => $id))->find();
				$builder->data($data);
			}

			$coin_list = D('Coin')->get_all_name_list();
//			$builder->keySelect('coinname', '币种', '币种', $coin_list);
			$builder->keyText('coinname', '币种','英文名称');
			$builder->keyText('title', '币种名称','中文名称');
			$builder->keyText('zhichi', '增加票数', '整数');
			$builder->keyText('rens', '增加人数', '整数');
			$builder->keyTextarea('show1', '介绍', '介绍');
			$builder->keyText('web', '官网地址', 'http://');
			$builder->keyText('bps', '白皮书地址', 'http://');
			
			$builder->keySelect('votecoin', '投票币种', '投票需要扣除的币种', $coin_list);
			$builder->keyText('knum', '扣币数量', '每票扣币数量/免费投票24小时内只能1票','1');
			$builder->keyTime('addtime', '开始时间', '开始时间');
			$builder->keyTime('endtime', '结束时间', '结束时间');
			
			
			
			$builder->savePostUrl(U('Vote/edit'));
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
		die();
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
		die();
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
		die();
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