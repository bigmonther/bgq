<?php
namespace App\Controller\Admin;

use Wpadmin\Controller\AppController;

/**
 * Urlmap Controller
 *
 * @property \App\Model\Table\UrlmapTable $Urlmap
 */
class UrlmapController extends AppController
{

/**
* Index method
*
* @return void
*/
public function index()
{
$this->set('urlmap', $this->Urlmap);
}

    /**
     * View method
     *
     * @param string|null $id Urlmap id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->autoLayout(false);
        $urlmap = $this->Urlmap->get($id, [
            'contain' => []
        ]);
        $this->set('urlmap', $urlmap);
        $this->set('_serialize', ['urlmap']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $urlmap = $this->Urlmap->newEntity();
        if ($this->request->is('post')) {
            $urlmap = $this->Urlmap->patchEntity($urlmap, $this->request->data);
            if ($this->Urlmap->save($urlmap)) {
                 $this->Util->ajaxReturn(true,'添加成功');
            } else {
                 $errors = $urlmap->errors();
                 $this->Util->ajaxReturn(['status'=>false, 'msg'=>getMessage($errors),'errors'=>$errors]);
            }
        }
                $this->set(compact('urlmap'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Urlmap id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
         $urlmap = $this->Urlmap->get($id,[
            'contain' => []
        ]);
        if ($this->request->is(['post','put'])) {
            $urlmap = $this->Urlmap->patchEntity($urlmap, $this->request->data);
            if ($this->Urlmap->save($urlmap)) {
                  $this->Util->ajaxReturn(true,'修改成功');
            } else {
                 $errors = $urlmap->errors();
               $this->Util->ajaxReturn(false,getMessage($errors));
            }
        }
                  $this->set(compact('urlmap'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Urlmap id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod('post');
         $id = $this->request->data('id');
                if ($this->request->is('post')) {
                $urlmap = $this->Urlmap->get($id);
                 if ($this->Urlmap->delete($urlmap)) {
                     $this->Util->ajaxReturn(true,'删除成功');
                } else {
                    $errors = $urlmap->errors();
                    $this->Util->ajaxReturn(true,getMessage($errors));
                }
          }
    }

/**
* get jqgrid data 
*
* @return json
*/
public function getDataList()
{
        $this->request->allowMethod('ajax');
        $page = $this->request->data('page');
        $rows = $this->request->data('rows');
        $sort = 'Urlmap.'.$this->request->data('sidx');
        $order = $this->request->data('sord');
        $keywords = $this->request->data('keywords');
        $begin_time = $this->request->data('begin_time');
        $end_time = $this->request->data('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where[' username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`create_time`) >' => $begin_time], ['date(`create_time`) <' => $end_time]];
        }
                $data = $this->getJsonForJqrid($page, $rows, '', $sort, $order,$where);
                $this->autoRender = false;
        $this->response->type('json');
        echo json_encode($data);
}

/**
* export csv
*
* @return csv 
*/
public function exportExcel()
{
        $sort = $this->request->query('sidx');
        $order = $this->request->query('sord');
        $keywords = $this->request->query('keywords');
        $begin_time = $this->request->query('begin_time');
        $end_time = $this->request->query('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where['username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`create_time`) >' => $begin_time], ['date(`create_time`) <' => $end_time]];
        }
        $Table =  $this->Urlmap;
        $column = ['url','映射','描述'];
        $query = $Table->find();
        $query->hydrate(false);
        $query->select(['url','map','descb']);
         if (!empty($where)) {
            $query->where($where);
        }
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }
        $res = $query->toArray();
        $this->autoRender = false;
        $filename = 'Urlmap_'.date('Y-m-d').'.csv';
        \Wpadmin\Utils\Export::exportCsv($column,$res,$filename);

}
}
