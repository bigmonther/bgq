<?php

namespace App\Controller\Mobile;

use App\Controller\Mobile\AppController;
use Cake\I18n\Time;

/**
 * Home Controller  个人中心
 *
 * @property \App\Model\Table\HomeTable $Home
 * @property \App\Model\Table\UserTable $User
 * @property \App\Controller\Component\BusinessComponent $Business
 * @property \App\Controller\Component\WxComponent $Wx
 * @property \App\Controller\Component\WxpayComponent $Wxpay
 * @property \App\Controller\Component\SmsComponent $Sms
 */
class IndexController extends AppController {

    public function initialize() {
        parent::initialize();
    }


    /**
     * Index method  个人中心页
     *
     * @return \Cake\Network\Response|null
     */

    public function index() {
        $str = '/upload/user/avatar/2016-06-28/thu2mb_577286211f691.jpg';
        debug(getOriginAvatar($str));
        exit();
    }

    /**
     * 个人主页
     */
    public function homepage($id = null) {
        $user_id = isset($id) ? $id : $this->user->id;
        $user = $this->User->get($user_id, ['contain' => ['Industries' => function($q) {
                    return $q->hydrate(false)->select(['id', 'name']);
                }]]);
        $industries = $user->industries;
        $industry_arr = [];
        foreach ($industries as $industry) {
            $industry_arr[] = $industry['name'];
        }
        $this->set([
            'pageTitle' => '个人主页'
        ]);
        $this->set(compact('user', 'industry_arr'));
    }

    /**
     * ajax获取我的活动 报名
     */
    public function myActivityApply() {
        $applyTable = \Cake\ORM\TableRegistry::get('activityapply');
        $myActivity = $applyTable->find()->contain(['Activities'])->where(['activityapply.user_id'=>$this->user->id])->toArray();
        if($myActivity !== false)
        {
            return $this->Util->ajaxReturn(['status'=>true, 'data'=>$myActivity]);
        }
        else
        {
            return $this->Util->ajaxReturn(false, '系统错误');
        }
    }
    
    /**
     * ajax获取我的发布活动
     */
    public function getMyActivity(){
        $ActivityTable = \Cake\ORM\TableRegistry::get('activity');
        $activities = $ActivityTable->findByUserId($this->user->id)->toArray();
        if ($activities !== false) {
            return $this->Util->ajaxReturn(['status' => true, 'data' => $activities]);
        } else {
            return $this->Util->ajaxReturn(false, '系统错误');
        }
    }

    /**
     * 我的活动 发布
     */
    public function myActivitySubmit() {
        $ActivityTable = \Cake\ORM\TableRegistry::get('activity');
        $activities = $ActivityTable->findByUserId($this->user->id)->toArray();
        $this->set([
            'activities' => $activities,
            'pageTitle' => '我的活动'
        ]);
    }

    /**
     * 实名认证
     */
    public function realnameAuth() {
        $user_id = $this->user->id;
        $user = $this->User->get($user_id);
        if ($this->request->is('post')) {
            $user = $this->User->patchEntity($user, $this->request->data());
            $user->status = 1; //实名状态改为未审核
            if ($this->User->save($user)) {
                $this->loadComponent('Business');
                $this->Business->adminmsg(1, $user_id, '您有一条实名认证申请需处理');
                return $this->Util->ajaxReturn(true, '保存成功');
            } else {
                return $this->Util->ajaxReturn(false, '保存失败');
            }
        }
        $this->set([
            'pageTitle' => '实名认证'
        ]);
        $this->set(compact('user'));
    }

    /**
     * 专家认证
     */
    public function savantAuth() {
        $user_id = $this->user->id;
        $user = $this->User->get($user_id,['contain'=>['Savants']]);
        $UserTable = \Cake\ORM\TableRegistry::get('user');
        if ($this->request->is('post')) {
            $SavantTable = \Cake\ORM\TableRegistry::get('savant');
            $repeat = $SavantTable->find()->where(['user_id' => $user_id])->first();
            if($repeat){
                $savant = $SavantTable->get($repeat->id);
                $savant = $SavantTable->patchEntity($savant, $this->request->data());
            } else {
                $savant = $SavantTable->newEntity();
                $savant->user_id = $user_id;
                $savant = $SavantTable->patchEntity($savant, $this->request->data());
            }
            $user->savant_status = 2;
            $ckRes = $this->User->connection()->transactional(function()use($SavantTable, $savant, $user, $UserTable) {
                //开启事务
                return $SavantTable->save($savant)&&$UserTable->save($user);
            });
            if ($ckRes) {
                $this->loadComponent('Business');
                $this->Business->adminmsg(1, $user_id, '您有一条专家认证申请需处理');
                return $this->Util->ajaxReturn(true, '保存成功');
            } else {
                return $this->Util->ajaxReturn(false,  errorMsg($savant,'保存失败'));
            }
        }
        $this->set([
            'pageTitle' => '专家认证'
        ]);
        $this->set(compact('user'));
    }

    /**
     * 我的关注
     */
    public function myFollowing() {
        $user_id = $this->user->id;
        $FansTable = \Cake\ORM\TableRegistry::get('user_fans');
        $followings = $FansTable->find()->contain(['Followings' => function($q) {
                        return $q->select(['id', 'truename', 'company', 'position', 'avatar', 'fans'])
                                ->where('enabled = 1')->contain(['Subjects']);
                    }])->hydrate(false)
                        ->where(['user_id' => $user_id])
                        ->toArray();
        $this->set([
            'followings' => $followings,
            'pageTitle'=>'我的关注'
        ]);
    }

    /**
     * 我的粉丝
     */
    public function myFans() {
        $user_id = $this->user->id;
        $FansTable = \Cake\ORM\TableRegistry::get('user_fans');
        $fans = $FansTable->find()->contain(['Users' => function($q) {
                        return $q->select(['id', 'truename', 'company', 'position', 'avatar', 'fans'])
                                ->where('enabled = 1')->contain(['Subjects']);
        }])->hydrate(false)
            ->where(['following_id' => $user_id])
            ->toArray();
        $this->set([
            'fans' => $fans,
            'pageTitle'=>'我的粉丝'
        ]);
    }

    /**
     * 我的关注消息
     */
    public function myMessageFans() {
        //查找type 为1 的消息
        $user_id = $this->user->id;
        $UsermsgTable = \Cake\ORM\TableRegistry::get('usermsg');
        $unReadFollowCount = $UsermsgTable->find()->where(['user_id' => $user_id, 'status' => 0,'type'=>1])->count(); //未读关注消息
        $unReadSysCount = $UsermsgTable->find()->where(['user_id' => $user_id, 'status' => 0,'type !='=>1])->count(); //未读系统消息
        $fans = $UsermsgTable->find()
                        ->hydrate(false)
                        ->select(['u.truename', 'u.avatar', 'u.id', 'create_time',
                            'u.company', 'u.position', 'u.fans', 'uf.type'])
                        ->join([
                               'uf' => [
                                'table' => 'user_fans',
                                'type' => 'inner',
                                'conditions' => 'uf.id = usermsg.table_id',
                            ],
                            'u' => [
                                'table' => 'user',
                                'type' => 'inner',
                                'conditions' => 'u.id = uf.user_id',
                            ]
                        ])
                        ->where("usermsg.`user_id` = '$user_id'")
                        ->orderDesc('usermsg.create_time')->toArray();
        //看了之后 就更改状态了为已读
        $UsermsgTable->updateAll(['status' => 1], ['user_id' => $user_id, 'status' => 0]);
        $this->set([
            'pageTitle' => '关注消息',
            'fans'=>$fans,
            'unReadSysCount'=>$unReadSysCount,
            'unReadFollowCount'=>$unReadFollowCount
        ]);
    }

    /**
     * 我的系统消息
     */
    public function myMessageSys() {
        $user_id = $this->user->id;
        $UsermsgTable = \Cake\ORM\TableRegistry::get('usermsg');
        $unReadFollowCount = $UsermsgTable->find()->where(['user_id' => $user_id, 'status' => 0,'type'=>1])->count(); //未读关注消息
        $unReadSysCount = $UsermsgTable->find()->where(['user_id' => $user_id, 'status' => 0,'type !='=>1])->count(); //未读系统消息
        $msgs = $UsermsgTable->find()->where(['user_id' => $user_id, 'type !=' => 1])->orderDesc('create_time')->toArray();
        $this->set([
            'pageTitle' => '系统消息',
            'unReadFollowCount'=>$unReadFollowCount,
            'unReadSysCount'=>$unReadSysCount
        ]);
        $this->set(compact('msgs', 'unReadCount'));
    }

    /**
     * 小秘书
     */
    public function myXiaomi() {
        if ($this->request->is('post')) {
            $user_id = $this->user->id;
            $NeedTable = \Cake\ORM\TableRegistry::get('need');
            $content = $this->request->data('content');
            $need = $NeedTable->newEntity(['user_id' => $user_id, 'msg' => $content]);
            if ($NeedTable->save($need)) {
                return $this->Util->ajaxReturn(true, '提交成功');
            } else {
                
                return $this->Util->ajaxReturn(false, '提交失败');
            }
        }
        $this->set([
            'pageTitle' => '小秘书'
        ]);
    }

    /**
     * 小秘书历史记录
     */
    public function myHistoryNeed() {
        $NeedTable = \Cake\ORM\TableRegistry::get('need');
        $user_id = $this->user->id;
        $needs = $NeedTable->find()->where(['user_id' => $user_id])->orWhere(['reply_id' => $user_id])->orderDesc('create_time')->toArray();
        $this->set([
            'pageTitle' => '小秘书历史记录'
        ]);
        $this->set(compact('needs'));
    }

    /**
     * 活动收藏记录
     */
    public function myCollectActivity() {
        $collectTable = \Cake\ORM\TableRegistry::get('collect');
        $activity = $collectTable->find()->where(['type'=>0, 'is_delete'=>0, 'collect.user_id'=>$this->user->id])->contain(['Activities'])->toArray();
        $this->set(['pageTitle'=>'活动收藏', 'activityjson'=>  json_encode($activity)]);
    }

    /**
     * 资讯收藏
     */
    public function myCollectNews() {
        $user_id = $this->user->id;
        $CollectTable = \Cake\ORM\TableRegistry::get('Collect');
        $collects = $CollectTable->find()->hydrate(false)
                ->contain(['News'])
                ->where(['is_delete' => 0, 'Collect.user_id' => $user_id])
                ->orderDesc('Collect.create_time')
                ->formatResults(function($items) {
                    return $items->map(function($item) {
                                //时间语义化转换
                                $item['create_str'] = $item['create_time']->timeAgoInWords(
                                        [ 'accuracy' => [
                                                'year' => 'year',
                                                'month' => 'month',
                                                'hour' => 'hour'
                                            ], 'end' => '+10 year']
                                );
                                return $item;
                            });
                })
                ->toArray();
        $this->set(compact('collects'));
        $this->set('pageTitle', '资讯收藏');
    }

    /**
    * 我的约见 （我是顾客）
    */
    public function myBook() {
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $type = $this->request->query('type');
//        $where['SubjectBook.status'] = in_array($type, ['0', '1', '3']) ? $type : 0;
        $where['SubjectBook.status !='] = 2;
        $where['SubjectBook.user_id'] = $this->user->id;
        $books = $BookTable->find()->contain(['Subjects', 'Subjects.User' => function($q) {
                        return $q->select(['truename', 'avatar', 'id', 'company', 'position','meet_nums']);
                    }])->where($where)->orderDesc('SubjectBook.update_time')->toArray();
        $savant_books = $BookTable->find()->contain(['Subjects', 'Users' => function($q) {
                        return $q->select(['truename', 'avatar', 'id', 'company', 'position','meet_nums']);
                    }])->where([
                       'SubjectBook.status !='=>2, 
                       'SubjectBook.savant_id ='=>$this->user->id, 
                    ])->orderDesc('SubjectBook.update_time')->toArray();            
        $this->set([
            'pageTitle'=>'我的约见',
            'books'=>$books,
            'savant_books'=>$savant_books   //我是专家
        ]);
        $this->set(compact('books', 'type'));
    }

    /**
     * 我的约见 我是顾客的详情
     */
    public function myBookDetail($id = null) {
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $book = $BookTable->get($id, [
            'contain' => ['Users' => function($q) {
                    return $q->select(['truename', 'id', 'avatar', 'company', 'position']);
                }, 'Subjects', 'Lmorder']
                ]);
                $subject = $book->subject;
                $this->set(compact('subject', 'book'));
        $this->set('pageTitle', '我是顾客');
    }

    /**
     * 我的约见 (我是专家)
     */
    public function myBookSavant() {
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $type = $this->request->query('type');
        $where['SubjectBook.status'] = in_array($type, ['0', '1', '3']) ? $type : 0;
        $where['SubjectBook.savant_id'] = $this->user->id;
        $books = $BookTable->find()->contain(['Subjects', 'Users' => function($q) {
                        return $q->select(['truename', 'avatar', 'id', 'company', 'position']);
                    }])->where($where)->orderDesc('SubjectBook.update_time')->toArray();
                $this->set(compact('books', 'type'));
        $this->set('pageTitle', '我是专家');
    }

    
    
    /**
     * 我的约见 我是专家详情
     * @param type $id
     */
    public function myBookSavantDetail($id = null) {
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
    
        $book = $BookTable->get($id, [
            'contain' => ['Users' => function($q) {
                    return $q->select(['truename', 'id', 'avatar', 'company', 'position', 'phone', 'email']);
                }, 'Subjects', 'Users.Industries']
                ]);
        $subject = $book->subject;
        $industries = $book->user->industries;
        $industries_arr = [];
        foreach($industries as $industry){
            $industries_arr[] = $industry->name;
        }
        if(!empty($book->user->ext_industry)){
            $industries_arr[] = $book->user->ext_industry;
        }
        $industries_str = implode('、',$industries_arr );
        $this->set([
            'pageTitle'=>'预约详情',
            'industries_str'=>$industries_str
        ]);
        $this->set(compact('subject', 'book'));
    }

    /**
    * 同意约见 约见状态更改->生成一条订单(目前对前台用户暂时没作用)
    */
    public function bookOk() {
        if ($this->request->is('post')) {
            $id = $this->request->data('id'); //book id
            $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
            $book = $BookTable->get($id, [
                'contain' => [
                    'Subjects',
                    'Users'
                ]
        ]);
        $book->status = 1; //更改
        $OrderTable = \Cake\ORM\TableRegistry::get('order');
        $order = $OrderTable->newEntity([
            'type' => 1,
            'relate_id' => $id, //预定表的id
            'user_id' => $book->user_id,
            'seller_id' => $book->savant_id,
            'order_no' => time() . $book->user_id . $id . createRandomCode(2, 2),
            'fee'=>0,    // 实际支付的默认值
            'price' => $book->subject->price,
            'remark' => '预约话题' . $book->subject->title
        ]);
        $transRes = $BookTable->connection()->transactional(function()use($book, $BookTable, $order, $OrderTable) {
            return $BookTable->save($book) && $OrderTable->save($order);
        });
        if ($transRes) {
            //短信和消息通知
            $this->loadComponent('Sms');
            $msg = "您预约的话题：《" . $book->subject->title . "》已确认通过，请及时登录平台支付预约款。";
            $this->Sms->sendByQf106($book->user->phone, $msg);
            $this->loadComponent('Business');
            $this->Business->usermsg($book->user_id, '预约通知', $msg, 4, $id);
            return $this->Util->ajaxReturn(true, '处理成功!');
        } else {
            return $this->Util->ajaxReturn(false, '服务器出错!');
            }
        }
    }
    
    /***
     * 取笑预约
     */
    public function bookNo($id){
        if($this->request->is('post')){
            $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
            $book = $BookTable->get($id, [
                'contain' => [
                    'Subjects',
                    'Users'
                ]
            ]);
            $book->status = 2;
            if($BookTable->save($book)){
                $this->loadComponent('Business');
                $this->Business->usermsg($book->user_id, '预约通知', '您的预约未被通过', 4, $id);
                return $this->Util->ajaxReturn(true, '操作成功');
            }else{
                return $this->Util->ajaxReturn(false,'操作失败');
            }
        }
        
    }

    /*                                                             * *
     * 我的钱包
     */

    public function myPurse() {
        $user_id = $this->user->id;
        $userInfo = $this->User->get($user_id);
        $FlowTable = \Cake\ORM\TableRegistry::get('Flow');
        $flows = $FlowTable->find()->where(['user_id' => $user_id, 'status' => '1'])->orderDesc('create_time')->toArray();
        $this->set(array(
            'userInfo' => $userInfo,
            'flows' => $flows,
            'pageTitle'=>'我的钱包'
        ));
    }

    /**
     * 提现
     */
    public function withdraw() {
        $user_id = $this->user->id;
        $userInfo = $this->User->get($user_id);
        if ($this->request->isAjax()) {
            $amount = $this->request->data('amount');
            $bank = $this->request->data('bank');
            $cardno = $this->request->data('cardno');
            if ($amount > $userInfo->money) {
                return $this->Util->ajaxReturn(false, '提现金额不能大于钱包余额');
            }
            $WithdrawTable = \Cake\ORM\TableRegistry::get('Withdraw');
            $withdraw = $WithdrawTable->newEntity([
                'user_id' => $user_id,
                'amount' => $amount,
                'cardno' => $cardno,
                'truename' => $userInfo->truename,
                'bank' => $bank,
            ]);
            if ($WithdrawTable->save($withdraw)) {
                return $this->Util->ajaxReturn(true, '提现申请成功');
            } else {
                \Cake\Log\Log::error($withdraw->errors());
                return $this->Util->ajaxReturn(false, '提现申请失败');
            }
        }
        $this->set([
            'pageTitle'=>'提现'
        ]);
        $this->set(compact('userInfo'));
    }
    
    /**
     * 提现成功页
     */
    public function withdrawSuccess(){
        
    }

    /**
     * 隐私设置
     */
    public function mySecret() {
        $ScrectTable = \Cake\ORM\TableRegistry::get('Secret');
        $user_id = $this->user->id;
        $secret = $ScrectTable->findOrCreate(['user_id'=>$user_id]);
        if($this->request->is('post')){
            $ScrectTable->patchEntity($secret,  $this->request->data());
            if($ScrectTable->save($secret)){
                return $this->Util->ajaxReturn(true, '修改成功');
            }else{
                return $this->Util->ajaxReturn(false,'修改失败');
            }
        }
        $secretType = \Cake\Core\Configure::read('secretType');
        $this->set([
            'pageTitle' => '隐私策略',
            'secret' => $secret,
            'secretType'=>$secretType
        ]);
    }

    /**
     * 设置
     */
    public function myInstall() {
        $this->set([
            'pageTitle' => '设置'
        ]);
    }

    /**
     * 修改个人信息
     */
    public function editUserinfo() {
        $user_id = $this->user->id;
        $user = $this->User->get($user_id);
        if ($this->request->is('post')) {
            $user = $this->User->patchEntity($user, $this->request->data());
            if ($this->User->save($user)) {
                return $this->Util->ajaxReturn(true, '保存成功');
            } else {
                return $this->Util->ajaxReturn(false, '保存失败');
            }
        }
        $this->set([
            'user' => $user,
            'pageTitle' => '编辑个人主页'
        ]);
    }

    /**
     * 擅长业务
     */
    public function myBusiness() {
        $user = $this->User->get($this->user->id);
        if($this->request->is('post')){
            $user = $this->User->patchEntity($user,  $this->request->data());
            if($this->User->save($user)){
                return $this->Util->ajaxReturn(true,'修改成功');
            }else{
                return $this->Util->ajaxReturn(false,'保存失败');
            }
        }
        $this->set([
            'pageTitle' => '擅长业务',
            'user'=>$user
        ]);
    }
    
    
    /**
     * 公司业务
     * @return type
     */
    public function editCompanyBusiness(){
        $user = $this->User->get($this->user->id);
        if($this->request->is('post')){
            $user = $this->User->patchEntity($user,  $this->request->data());
            if($this->User->save($user)){
                return $this->Util->ajaxReturn(true,'修改成功');
            }else{
                return $this->Util->ajaxReturn(false,'保存失败');
            }
        }
        $this->set([
            'pageTitle' => '公司业务',
            'user'=>$user
        ]);
    }
    
    
    /**
     * 编辑教育经历
     */
    public function editEducation(){
        $EducationTable = \Cake\ORM\TableRegistry::get('Education');
        $educations = $EducationTable->find()->where(['user_id'=>  $this->user->id])->toArray();
        $educationType = \Cake\Core\Configure::read('educationType');
        $this->set([
            'pageTitle' =>'编辑教育经历',
            'educations'=>$educations,
            'educationType'=>$educationType
        ]);
    }
    
    /**
     * 保存教育经历
     */
    public function saveEducation(){
        $EducationTable = \Cake\ORM\TableRegistry::get('Education');
        if($this->request->is('post')){
            $data = $this->request->data();
            if(!empty($data['id'])){
                $education = $EducationTable->get($data['id']);
                if($education){
                    $education = $EducationTable->patchEntity($education, $data);
                    $res = $EducationTable->save($education);
                } else {
                    return $this->Util->ajaxReturn(false, '不存在的id');
                }
            } else {
                $data['user_id'] = $this->user->id;
                $education = $EducationTable->newEntity();
                $education = $EducationTable->patchEntity($education, $data);
                $res = $EducationTable->save($education);
            }
            if($res){
                return $this->Util->ajaxReturn(['status'=>true, 'msg'=>'保存成功', 'id'=>$res->id]);
            }else{
                return $this->Util->ajaxReturn(false, '保存失败');
            }
        }
    }
    
    
    /**
     * 编辑工作经历
     */
    public function editWork(){
        $CareerTable = \Cake\ORM\TableRegistry::get('Career');
        $careers = $CareerTable->find()->where(['user_id'=>  $this->user->id])->toArray();
        if($this->request->is('post')){
            $data = $this->request->data();
            
            $data['user_id'] = $this->user->id;
            $career = $CareerTable->newEntity($data);
            if($CareerTable->save($career)){
                return $this->Util->ajaxReturn(true,'保存成功!');
            }else{
                return $this->Util->ajaxReturn(false,  errorMsg($career,'保存失败'));
            }
        }
        $this->set([
           'pageTitle'=>'编辑工作经历 ' ,
            'careers'=>$careers
        ]);
    }
    
    /**
     * 保存工作经历
     */
    public function saveWork(){
        $CareerTable = \Cake\ORM\TableRegistry::get('Career');
        if($this->request->is('post')){
            $data = $this->request->data();
            if(!empty($data['id'])){
                $career = $CareerTable->get($data['id']);
                if($career){
                    $career = $CareerTable->patchEntity($career, $data);
                    $res = $CareerTable->save($career);
                } else {
                    return $this->Util->ajaxReturn(false, '不存在的id');
                }
            } else {
                $data['user_id'] = $this->user->id;
                $career = $CareerTable->newEntity();
                $career = $CareerTable->patchEntity($career, $data);
                $res = $CareerTable->save($career);
            }
            if($res){
               return $this->Util->ajaxReturn(['status'=>true, 'msg'=>'保存成功', 'id'=>$res->id]);
            }else{
                return $this->Util->ajaxReturn(false,  errorMsg($career,'保存失败'));
            }
        }
    }
    
    
    
    /**
     * 删除教育
     * @param type $id
     * @return type
     */
    public function delEducation($id){
        $EducationTable = \Cake\ORM\TableRegistry::get('Education');
        $education = $EducationTable->find()->where(['user_id'=>  $this->user->id,'id'=>$id])->first();
        if($education){
            if($EducationTable->delete($education)){
                return $this->Util->ajaxReturn(true, '删除成功');
            }else{
                return $this->Util->ajaxReturn(false, '删除失败');
            }
        }else{
            return $this->Util->ajaxReturn(false, '记录不存在');
        }
        
    }


    /**
     * 删除工作经历
     * @param type $id
     * @return type
     */
    public function delWork($id){
        $CareerTable = \Cake\ORM\TableRegistry::get('Career');
        $career = $CareerTable->find()->where(['user_id'=>  $this->user->id,'id'=>$id])->first();
        if($career){
            if($CareerTable->delete($career)){
                return $this->Util->ajaxReturn(true, '删除成功');
            }else{
                return $this->Util->ajaxReturn(false, '删除失败');
            }
        }else{
            return $this->Util->ajaxReturn(false, '记录不存在');
        }
    }
    




    /**
     * 编辑名片
     */
    public function editCard(){
         $user_id = $this->user->id;
        $userInfo = $this->User->get($user_id);
        if($this->request->is('post')){
            $userInfo->card_path = $this->request->data('card_path');
            if($this->User->save($userInfo)){
                return $this->Util->ajaxReturn(true, '更改成功');
            }else{
                return $this->Util->ajaxReturn(false,'服务器开小差了');
            }
        }
        $this->set([
            'pageTitle'=>'名片修改',
             'user'=>$userInfo
        ]);
    }
    
    
    /**
     * 编辑标签
     */
    public function editMark(){
           $user_id = $this->user->id;
           $userInfo = $this->User->get($user_id);
           if($this->request->is('post')){
               $userInfo ->grbq = serialize($this->request->data('tags'));
               if($this->User->save($userInfo)){
                   return $this->Util->ajaxReturn(true, '更新成功');
               }else{
                   return $this->Util->ajaxReturn(false,'服务器开小差了');
               }
           }
           $ProfiletagTable = \Cake\ORM\TableRegistry::get('Profiletag');
           $profiletags = $ProfiletagTable->find('list')->select(['name'])->toArray();
           $mark_ser = $userInfo->grbq;  //个人标签
           $mark_arr = [];
           $extra_arr = [];
           if(is_array(unserialize($mark_ser))){
               $mark_arr = unserialize($mark_ser);
               if($mark_arr){
                   foreach ($mark_arr as $mark){
                        if(!in_array($mark, $profiletags)){
                            $extra_arr[] = $mark;
                        }
                   }
               }
           }
          $this->set([
            'pageTitle'=>'个人标签',
             'user'=>$userInfo,
              'mark_arr'=>$mark_arr,
              'profiletags'=>$profiletags,
              'extra_mark'=>$extra_arr
        ]);
    }

    /**
     * 名片夹
     */
    public function cardcase() {
        $this->handCheckLogin();
        $card = $this
                ->User
                ->CardBoxes
                ->find()
                ->contain(['OtherCard'])
                ->where(['ownerid' => $this->user->id, 'resend' => '2'])
                ->orderDesc('CardBoxes.`create_time`')
                ->limit($this->limit)
                ->toArray();
        $this->set('cardjson', json_encode($card));
        $this->set('pageTitle' ,'名片夹');
    }

    /**
     * 名片夹列表
     * @param int $resend
     */
    public function getCrad($resend) {
        $card = $this
                ->User
                ->CardBoxes
                ->find()
                ->contain(['OtherCard'])
                ->where(['ownerid' => $this->user->id, 'resend' => $resend])
                ->orderDesc('CardBoxes.`create_time`')
                ->limit($this->limit)
                ->toArray();
        if ($card !== false) {
            return $this->Util->ajaxReturn(['status' => true, 'data' => $card]);
        } else {
            return $this->Util->ajaxReturn(false, '系统错误');
        }
    }

    /**
     * 回赠动作
     * @param int $id
     */
    public function sendBack($id) {
        $sendMe = $this
                ->User
                ->CardBoxes
                ->find()
                ->where(['ownerid' => $this->user->id, 'uid' => $id])
                ->first();
        $sendMe = $this->User->CardBoxes->get($sendMe->id);
        $sendMe->resend = 1;
        $res = $this->User->CardBoxes->save($sendMe);
        if ($res) {
            return $this->Util->ajaxReturn(true, '回赠成功');
        } else {
            return $this->Util->ajaxReturn(false, '回赠失败');
        }
    }
    
    /**
     * 编辑行业标签
     */
    public function editindustries(){
        $userIndustryTable = \Cake\ORM\TableRegistry::get('user_industry');
        $userindustry = $userIndustryTable->find()->where(['user_id' => $this->user->id])->hydrate(false)->toArray();
        foreach ($userindustry as $k=>$v){
            $industry_id[] = $v['industry_id'];
        }
        
        $IndustryTable = \Cake\ORM\TableRegistry::get('industry');
        $industries = $IndustryTable->find('threaded', [
                    'keyField' => 'id',
                    'parentField' => 'pid'
                ])->where("`id` != '3'")->hydrate(false)->toArray();
        $this->set(array(
            'userIndustry' => $industry_id,
            'industries' => $industries,
            'pageTitle'=>'选择行业标签'
        ));
    }
    
    /**
     * 保存行业标签
     */
    public function saveIndustries(){
        if ($this->request->is('post')) {
            $data = $this->request->data();
            $user = $this->User->get($this->user->id);
            $user = $this->User->patchEntity($user, $data);
            if ($this->User->save($user)) {
                return $this->Util->ajaxReturn(true, '保存成功');
            } else {
                return $this->Util->ajaxReturn(false, '保存失败');
            }
        }
    }
    
    /**
     * 搜索我的关注
     */
    public function searchFollowing(){
        if($this->request->is('post')){
            $data = $this->request->data;
            $keyword = $data['keyword'];
            $user_id = $this->user->id;
            $FansTable = \Cake\ORM\TableRegistry::get('user_fans');
            $followings = $FansTable->find()->contain(['Followings' => function($q)use($keyword) {
                            return $q->select(['id', 'truename', 'company', 'position', 'avatar', 'fans'])
                                    ->where(['truename like'=>"%$keyword%", 'enabled' => 1])->contain(['Subjects']);
                        }])->hydrate(false)
                            ->where(['user_id' => $user_id])
                            ->toArray();
            if($followings !== false){
                if($followings){
                    return $this->Util->ajaxReturn(['status'=>true, 'data'=>$followings]);
                } else {
                    return $this->Util->ajaxReturn(false, '您的关注里无这个人');
                }
            } else {
                return $this->Util->ajaxReturn(false, '系统错误');
            }
        }
    }
    
    /**
     * 搜索我的粉丝
     */
    public function searchFans(){
        if($this->request->is('post')){
            $data = $this->request->data;
            $keyword = $data['keyword'];
            $user_id = $this->user->id;
            $FansTable = \Cake\ORM\TableRegistry::get('user_fans');
            $fans = $FansTable->find()->contain(['Users' => function($q)use($keyword) {
                            return $q->select(['id', 'truename', 'company', 'position', 'avatar', 'fans'])
                                    ->where(['enabled'=>1, 'truename like'=>"%$keyword%"]   )->contain(['Subjects']);
            }])->hydrate(false)
                ->where(['following_id' => $user_id])
                ->toArray();
            if($fans !== false){
                if($fans){
                    return $this->Util->ajaxReturn(['status'=>true, 'data'=>$fans]);
                } else {
                    return $this->Util->ajaxReturn(false, '您的粉丝里无这个人');
                }
            } else {
                return $this->Util->ajaxReturn(false, '系统错误');
            }
        }
    }
    
    public function searchcard(){
        if($this->request->is('post')){
            $data = $this->request->data;
            $keyword = $data['keyword'];
            $resend = $data['resend'];
            $card = $this
                    ->User
                    ->CardBoxes
                    ->find()
                    ->contain(['OtherCard'=>function($q)use($keyword){
                        return $q->where(['OtherCard.truename like' => "%$keyword%"]);
                    }])
                    ->where(['ownerid' => $this->user->id, 'resend' => $resend])
                    ->orderDesc('CardBoxes.`create_time`')
                    ->limit($this->limit)
                    ->toArray();
            if($card !== false){
                if($card){
                    return $this->Util->ajaxReturn(['status'=>true, 'data'=>$card]);
                } else {
                    return $this->Util->ajaxReturn(false, '您的名片夹里无这个人');
                }
            } else {
                return $this->Util->ajaxReturn(false, '系统错误');
            }
        }
    }

}
                                                        