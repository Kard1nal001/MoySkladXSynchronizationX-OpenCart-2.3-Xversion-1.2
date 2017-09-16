<?php
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

class ControllerextensionmoduleMoyskladOC23Synch12 extends Controller {
    
    public $login;
    public $pass;
    
    //обращаемся к продукту
    public $urlProduct = "entity/product";

    //методы  по которым совершаем запросы к API (что запрос должен делать получить, создать, изменить,  удалить)
    public $get = "GET";
    public $post = "POST";
    public $put = "PUT";
    public $delete = "DELETE";
 
    public function index() {
        
        $this->load->language('extension/module/moyskladOC23Synch12');
        
        $this->document->addStyle('view/stylesheet/moyskladOC2_3Synch.css');
        $this->document->addScript('view/javascript/jquery/tabs.js');
        
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module');

        $this->load->model('setting/setting');
         
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('moyskladOC23Synch12', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)); 
            
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_username'] = $this->language->get('entry_username');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['text_tab_setting'] = $this->language->get('text_tab_setting');
        $data['entry_save'] = $this->language->get('entry_save');
        
        
        $data['text_tab_import'] = $this->language->get('text_tab_import');
        $data['entry_import'] = $this->language->get('entry_import');
        
        $data['text_tab_lot'] = $this->language->get('text_tab_lot');
        
        $data['text_tab_author'] = $this->language->get('text_tab_author');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        
        $data['import_text'] = $this->language->get('import_text');
        $data['import_button'] = $this->language->get('import_button');
 
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }
        else {
            $data['error_warning'] = '';
        }

         

        if (isset($this->error['moyskladOC23Synch12_username'])) {
            $data['error_moyskladOC23Synch12_username'] = $this->error['moyskladOC23Synch12_username'];
        }
        else {
            $data['error_moyskladOC23Synch12_username'] = '';
        }

        if (isset($this->error['moyskladOC23Synch12_password'])) {
            $data['error_moyskladOC23Synch12_password'] = $this->error['moyskladOC23Synch12_password'];
        }
        else {
            $data['error_moyskladOC23Synch12_password'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/moyskladOC23Synch12', 'token=' . $this->session->data['token'], true)
        );
        $data['token'] = $this->session->data['token'];

        $data['action'] = $this->url->link('extension/module/moyskladOC23Synch12', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        if (isset($this->request->post['moyskladOC23Synch12_username'])) {
            $data['moyskladOC23Synch12_username'] = $this->request->post['moyskladOC23Synch12_username'];
        }
        else {
            $data['moyskladOC23Synch12_username'] = $this->config->get('moyskladOC23Synch12_username');
        }

        if (isset($this->request->post['moyskladOC23Synch12_password'])) {
            $data['moyskladOC23Synch12_password'] = $this->request->post['moyskladOC23Synch12_password'];
        }
        else {
            $data['moyskladOC23Synch12_password'] = $this->config->get('moyskladOC23Synch12_password');
        }
 
        
        // Группы
        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
 
        $this->template = 'extension/module/moyskladOC23Synch12.tpl';
        $this->children = array(
            'common/header',
            'common/footer' 
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/moyskladOC23Synch12.tpl', $data));

        //$this->response->setOutput($this->render(), $this->config->get('config_compression'));
    }

    private function validate() {

        if (!$this->user->hasPermission('modify', 'extension/module/moyskladOC23Synch12')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;

    }
    
    #TODO где то натупил с рекурсией, товар только  1 получаю, а мне нужно вывести весь, что есть
    
    
    //получаем весь товар, что есть (рекурсия)
    public function getAllProduct($counts = 0){
 
         //$urlProduct = "entity/product?offset=$counts&limit=100";
         $urlProduct = "entity/product?offset=$counts&limit=1";
        $product = $this->getNeedInfo($urlProduct,$this->get);

        //for($i=0; $i<100; $i++){
        for($i=0; $i<2; $i++){    
        //если дошли до конца списка то выходим из рекурсии
            if(empty($product["rows"][$i]["id"])){
                break;
            }

             var_dump($product["rows"][$i]["name"]);

            
        } 

        //вызов рекурсии  
        $this->getAllProduct($counts+$i);
   
    }
    
    
    //получаем нужную информацию меняя поля URL
   public function getNeedInfo($url,$method){


    //делаем проверку если в базе хранятся доступы то заносим в переменную
    if(!empty($this->config->get('moyskladOC23Synch12_username'))){
       $this->login = $this->config->get('moyskladOC23Synch12_username');
            
        }else{
            $this->login = "";
        }
        
        
        if(!empty($this->config->get('moyskladOC23Synch12_password'))){
            $this->pass = $this->config->get('moyskladOC23Synch12_password');
            
        }else{
            $this->pass = "";
    }
 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, "https://online.moysklad.ru/api/remap/1.1/".$url);    
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
    curl_setopt($ch, CURLOPT_USERPWD, $this->login.":".$this->pass);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
        'Accept: application/json',
        'Content-Type: application/json')                                                           
    );             

    if(curl_exec($ch) === false)
    {
        echo 'Curl error: ' . curl_error($ch);
    }                                                                                                      
    $errors = curl_error($ch);                                                                                                            
    $result = curl_exec($ch);
    $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);  

     return json_decode($result, true);
    }
 
   

}
?>