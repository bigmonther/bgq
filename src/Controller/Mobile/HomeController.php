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
class HomeController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->loadModel('User');
        $this->set('pageTitle','个人中心');
    }

    /**
     * Index method  个人中心页
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $this->loadComponent('Wx');
        $wxConfig = $this->Wx->wxconfig(['onMenuShareTimeline','onMenuShareAppMessage']);
        $user_id = $this->user->id;
        $user = $this->User->get($user_id);
        $this->set(compact('user'));
        $this->set(array(
            'user'=>$user,
            'wxConfig'=>$wxConfig
        ));
    }

    /**
     * 个人主页
     */
    public function myHomePage($id=null) {
        $user_id = isset($id)?$id:$this->user->id;
        $user = $this->User->get($user_id,['contain'=>['Industries'=>function($q){
            return $q->hydrate(false)->select(['id','name']);
        }]]);
        $industries = $user->industries;
        $industry_arr = [];
        foreach($industries as $industry){
            $industry_arr[] = $industry['name'];
        }
        $this->set(compact('user','industry_arr'));
    }

    /**
     * 我的活动 报名
     */
    public function myActivityApply() {
        
    }

    /**
     * 我的活动 发布
     */
    public function myActivitySubmit() {
        $ActivityTable = \Cake\ORM\TableRegistry::get('activity');
        $activities = $ActivityTable->findByUserId($this->user->id)->toArray();
        $this->set([
            'activities' => $activities
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
                $this->Util->ajaxReturn(true, '保存成功');
            } else {
                $this->Util->ajaxReturn(false, '保存失败');
            }
        }
        $this->set(compact('user'));
    }

    /**
     * 专家认证
     */
    public function savantAuth() {
        $user_id = $this->user->id;
        $user = $this->User->get($user_id);
        $UserTable = \Cake\ORM\TableRegistry::get('user');
        if ($this->request->is('post')) {
            $SavantTable = \Cake\ORM\TableRegistry::get('savant');
            $savant = $SavantTable->newEntity();
            $savant->user_id = $user_id;
            $savant = $SavantTable->patchEntity($savant, $this->request->data());
            $user->savant_status = 2;
            $errorFlag = [];
            $this->User->connection()->transactional(function()use($SavantTable, $savant, $user, $UserTable, $errorFlag) {
                //开启事务
                $errorFlag[] = $SavantTable->save($savant);
                $errorFlag[] = $UserTable->save($user);
            });
            if (!in_array(false, $errorFlag)) {
                $this->loadComponent('Business');
                $this->Business->adminmsg(1, $user_id, '您有一条专家认证申请需处理');
                $this->Util->ajaxReturn(true, '保存成功');
            } else {
                $this->Util->ajaxReturn(false, '保存失败');
            }
        }
        $this->set(compact('user'));
    }
    
    /**
     * 我的关注
     */
    public function myFollowing(){
        $user_id = $this->user->id;
        $FansTable = \Cake\ORM\TableRegistry::get('user_fans');
        $followings = $FansTable->find()->contain(['Followings'=>function($q){
            return $q->select(['id','truename','company','position','avatar','fans'])
                      ->where('enabled = 1');
        }])->hydrate(false)
                ->where(['user_id'=>$user_id])
                ->toArray();
        $this->set(compact('followings'));
    }
    
    
    /**
     * 我的粉丝
     */
    public function myFans(){
        $user_id = $this->user->id;
        $FansTable = \Cake\ORM\TableRegistry::get('user_fans');
        $fans = $FansTable->find()->contain(['Users'=>function($q){
            return $q->select(['id','truename','company','position','avatar','fans'])
                      ->where('enabled = 1');
        }])->hydrate(false)
                ->where(['following_id'=>$user_id])
                ->toArray();
        $this->set(compact('fans'));
    }
    
    /**
     * 我的关注消息
     */
    public function myMessageFans(){
        //查找type 为1 的消息
        $user_id = $this->user->id;
        
        $UsermsgTable = \Cake\ORM\TableRegistry::get('usermsg');
        $unReadCount = $UsermsgTable->find()->where(['user_id'=>$user_id,'status'=>0])->count();
        
        $fans = $UsermsgTable->find()
                 ->hydrate(false)
                 ->select(['u.truename','u.avatar','u.id','create_time',
                     'u.company','u.position','u.fans','uf.type'])
                 ->join([
                     'u'=>[
                         'table'=>'user',
                         'type'=>'inner',
                         'conditions' => 'u.id = usermsg.user_id',
                     ],
                     'uf'=>[
                         'table'=>'user_fans',
                         'type'=>'inner',
                         'conditions' => 'uf.id = usermsg.table_id',
                     ]
                 ])
                ->where("usermsg.`user_id` = '$user_id'")
                ->orderDesc('usermsg.create_time')->toArray();
        //看了之后 就更改状态了为已读
        $UsermsgTable->updateAll(['status'=>1],['user_id'=>$user_id,'status'=>0]);
        $this->set(compact('unReadCount','fans'));
    }
    
    /**
     * 小秘书
     */
    public function myXiaomi(){
        if($this->request->is('post')){
            $user_id = $this->user->id;
            $NeedTable = \Cake\ORM\TableRegistry::get('need');
            $content = $this->request->data('content');
            $need = $NeedTable->newEntity(['user_id'=>$user_id,'msg'=>$content]);
            if($NeedTable->save($need)){
                $this->Util->ajaxReturn(true,'提交成功');
            }else{
//                $error = getMessage($need->errors());
                $this->Util->ajaxReturn(false, '提交失败');
            }
        }
    }
    
    /**
     * 小秘书历史记录
     */
    public function myHistoryNeed(){
        $NeedTable = \Cake\ORM\TableRegistry::get('need');
        $user_id = $this->user->id;
        $needs = $NeedTable->find()->where(['user_id'=>$user_id])->orderDesc('create_time')->toArray();
        $this->set(compact('needs'));
    }
    
    
    /**
     * 活动收藏记录
     */
    public function myCollectActivity(){
        
    }
    
    
    /**
     * 资讯收藏
     */
    public function myCollectNews(){
        $user_id = $this->user->id;
        $CollectTable = \Cake\ORM\TableRegistry::get('Collect');
        $collects = $CollectTable->find()->hydrate(false)
                                 ->contain(['News'])
                                 ->where(['is_delete'=>0,'user_id'=>$user_id])
                                 ->orderDesc('Collect.create_time')
                                 ->formatResults(function($items){
                                     return $items->map(function($item){
                                         //时间语义化转换
                                        $item['create_str'] =$item['create_time']->timeAgoInWords(
                                           [ 'accuracy' => [
                                                     'year' => 'year',
                                                     'month' => 'month',
                                                     'hour' => 'hour'
                                                 ],'end' => '+10 year']
                                        );
                                         return $item;
                                     });
                                 })
                                 ->toArray();
        $this->set(compact('collects'));
    }
    
    /**
     * 我的约见 （我是顾客）
     */
    public function myBook(){
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $type = $this->request->query('type');
        $where['SubjectBook.status'] =in_array($type, ['0','1','3'])?$type:0;
        $where['SubjectBook.user_id'] = $this->user->id;
        $books = $BookTable->find()->contain(['Subjects','Subjects.User'=>function($q){
            return $q->select(['truename','avatar','id','company','position']);
        }])->where($where)->orderDesc('SubjectBook.update_time')->toArray();
        
        $this->set(compact('books','type'));
    }
    
    
    /**
     * 我的约见 我是顾客的详情
     */
    public function myBookDetail($id=null){
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $book = $BookTable->get($id,[
            'contain'=>['Users'=>function($q){
                 return $q->select(['truename','id','avatar','company','position']);
            },'Subjects','Lmorder']
        ]);
        $subject = $book->subject;
        $this->set(compact('subject','book'));
    }
 

    /**
     * 我的约见 (我是专家)
     */
    public function myBookSavant(){
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $type = $this->request->query('type');
        $where['SubjectBook.status'] =in_array($type, ['0','1','3'])?$type:0;
        $where['SubjectBook.savant_id'] = $this->user->id;
        $books = $BookTable->find()->contain(['Subjects','Users'=>function($q){
            return $q->select(['truename','avatar','id','company','position']);
        }])->where($where)->orderDesc('SubjectBook.update_time')->toArray();
        $this->set(compact('books','type'));
    }
    
    
       
    /**
     * 我的约见 我是专家详情
     * @param type $id
     */
    public function myBookSavantDetail($id=null){
        $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
        $book = $BookTable->get($id,[
            'contain'=>['Users'=>function($q){
                 return $q->select(['truename','id','avatar','company','position','phone','email']);
            },'Subjects','Users.Industries']
        ]);
        $subject = $book->subject;
        $this->set(compact('subject','book'));
    }
    
    
    /**
     * 同意约见 约见状态更改->生成一条订单(目前对前台用户暂时没作用)
     */
    public function bookOk(){
        if($this->request->is('post')){
            $id = $this->request->data('id'); //book id
            $BookTable = \Cake\ORM\TableRegistry::get('SubjectBook');
            $book = $BookTable->get($id,[
                'contain'=>[
                    'Subjects',
                    'Users'
                ]
            ]);
            $book->status = 1; //更改
            $OrderTable = \Cake\ORM\TableRegistry::get('order');
            $order = $OrderTable->newEntity([
                'type'=>1,
                'relate_id'=>$id,   //预定表的id
                'user_id'=>  $book->user_id,
                'seller_id'=> $book->savant_id,
                'order_no'=>  time().$book->user_id.$id.  createRandomCode(2,2),
                'price'=>  $book->subject->price,
                'remark'=>'预约话题'.$book->subject->title
            ]);
            $transRes = $BookTable->connection()->transactional(function()use($book, $BookTable, $order, $OrderTable) {
                return $BookTable->save($book)&&$OrderTable->save($order);
            });
            if ($transRes) {
                //短信和消息通知
                $this->loadComponent('Sms');
                $msg = "您预约的话题：《".$book->subject->title."》已确认通过，请及时登录平台支付预约款。";
                $this->Sms->sendByQf106($book->user->phone, $msg);
                $this->loadComponent('Business');
                $this->Business->usermsg($book->user_id,'预约通知',$msg, 4, $id);
                $this->Util->ajaxReturn(true,'处理成功!');
            }else{
                $this->Util->ajaxReturn(false,'服务器出错!');
            }
        }
    }



    /***
     * 我的钱包
     */
    public function myPurse(){
        
    }
}
