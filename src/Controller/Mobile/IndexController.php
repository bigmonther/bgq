<?php

namespace App\Controller\Mobile;

use App\Controller\Mobile\AppController;
use App\Utils\umeng\Umeng;
use Cake\Utility\Security;

/**
 * Index Controller
 *
 * @property \App\Model\Table\IndexTable $Index
 * @property \App\Controller\Component\SmsComponent $Sms
 * @property \App\Controller\Component\WxComponent $Wx
 * @property \App\Controller\Component\EncryptComponent $Encrypt
 * @property \App\Controller\Component\PushComponent $Push
 */
class IndexController extends AppController {

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        //$umengObj = new Umeng($key, $secret);
        //var_dump($umengObj);
        $this->autoRender = false;
        phpinfo();
        //debug($this->request);
//        $key = 'wt1U5MACWJFTXGenFoZoiLwQGrLgdbHA';
//        debug(Security::hash(uniqid()),'sha1',true);
//        $result = Security::encrypt('123', $key);
//        $cipher = base64_encode($result);
//        debug(Security::decrypt(base64_decode($cipher), $key));
//        $this->loadComponent('Wx');
//        $access_token = $this->Wx->getAccessToken();
//        var_dump($access_token);exit();
//        $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
//        $res = $httpClient->post('http://bgq.dev/api/wxtoken');
        //debug($res);
        //$token = 'QZeqKxItfF2jXWdIWjePlhBEX3JK9JKtIJkCwYMisw8c8Raqg2iOIWufshlgswB04Mj0d8mnmu3uuDUtqsbP51W0AOsyLWx1lhkWPA0Svcy60eLZmTiHKWEA-BXiOdDaDKThAEANUD';
//        $token = 'MjQMDc3YWZkMTk0NzJmYTc3NjI1MWU2ZDA1NWI5ZmI4Y2VjMTYxNjcxYTA4MGY4NzFjNTU3ZWQ0YWIwNTkwNAQ5bXSGZ1tx/Q1EuasSyLB4rrFnzYlobxSDbeTJu0PPt3EPsv1FgvYet/jDx1ItuasQCBOMma7lG7ZskFHSBL7epml/ox0l5Gt0GqQ+3Ef21qvC1UzCHAWr0mB+E5f0wYY51pcY0H/gMe2BrY5C0XeX5jC+PnilQ/DfvcrsQ1ypVzCsnkRiVH3kkagRtFUyriYco7S3zjhiBHUQL0a3FVw=';
//        $this->loadComponent('Encrypt');
        //$en_str = $this->Encrypt->encrypt('123');
        //var_dump($en_str);
//        $key = 'fkc33fdsafasdfasdfasdfasdgasddklsjfasdklfjasdkljaskljgklasdjgaekljgkl';
//        $en_str = Security::encrypt('123', $key,'1');
//        debug(Security::decrypt($en_str, $key,'1'));
//        debug(Security::hash(uniqid(),'md5'));
//        $this->loadComponent('Push');
//        $this->Push->test();
//        
//        var_dump(1/100);
        //$en_token = $this->Encrypt->encrypt($token);
        //debug($en_token);
        //debug($this->Encrypt->decrypt($en_str));
//        $xml = \Cake\Utility\Xml::build([
//                    'return_code' => 'SUCCESS',
//                    'return_msg' => 'OK',
//        ]);
//        debug($xml);
         $arr = ['foo'=>'bar','you'=>'done'];
         $this->Util->dblog('order', '一个测试日志', $arr);
        $arr = 'testnkad';
        echo var_export($arr,true);
    }

    public function test() {
        $this->autoRender = false;
        $cipher = 'NDk3MzYyNmI3YzI0YjMwZDU4MTViZTliOTVhNGRlYzZhOTk3ZmZlZmQwNmNlOTI1NjExODU1ZDAwNTJiMzEwZaD0xBasVESkYgXn99ZSMnBRdwanx0YcQse1r6cbGC1Z';
        $this->loadComponent('Encrypt');
        $str = $this->Encrypt->decrypt($cipher);
        debug($str);
    }

    /**
     * View method
     *
     * @param string|null $id Index id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $index = $this->Index->get($id, [
            'contain' => []
        ]);

        $this->set('index', $index);
        $this->set('_serialize', ['index']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $index = $this->Index->newEntity();
        if ($this->request->is('post')) {
            $index = $this->Index->patchEntity($index, $this->request->data);
            if ($this->Index->save($index)) {
                $this->Flash->success(__('The index has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The index could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('index'));
        $this->set('_serialize', ['index']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Index id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $index = $this->Index->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $index = $this->Index->patchEntity($index, $this->request->data);
            if ($this->Index->save($index)) {
                $this->Flash->success(__('The index has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The index could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('index'));
        $this->set('_serialize', ['index']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Index id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $index = $this->Index->get($id);
        if ($this->Index->delete($index)) {
            $this->Flash->success(__('The index has been deleted.'));
        } else {
            $this->Flash->error(__('The index could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
