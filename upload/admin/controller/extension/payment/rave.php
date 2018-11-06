<?php 
class ControllerExtensionPaymentRave extends Controller
{
    private $error = array();

    public function index() 
    {
        $this->load->language('extension/payment/rave');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_rave', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        
        }

        $data['heading_title'] = $this->language->get('heading_title');
 
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_pay'] = $this->language->get('text_pay');
        $data['text_disable_payment'] = $this->language->get('text_disable_payment');
        
        $data['entry_test_public_key'] = $this->language->get('entry_test_public_key');
        $data['entry_test_secret_key'] = $this->language->get('entry_test_secret_key');
        $data['entry_live_public_key'] = $this->language->get('entry_live_public_key');
        $data['entry_live_secret_key'] = $this->language->get('entry_live_secret_key');
        
        $data['entry_live'] = $this->language->get('entry_live');
        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['entry_modal_logo'] = $this->language->get('entry_modal_logo');
        $data['entry_modal_title'] = $this->language->get('entry_modal_title');
        $data['entry_modal_desc'] = $this->language->get('entry_modal_desc');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_approved_status'] = $this->language->get('entry_approved_status');
        $data['entry_declined_status'] = $this->language->get('entry_declined_status');
        $data['entry_error_status'] = $this->language->get('entry_error_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['help_live'] = $this->language->get('help_live');
        $data['help_debug'] = $this->language->get('help_debug');
        $data['help_total'] = $this->language->get('help_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_order_status'] = $this->language->get('tab_order_status');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['keys'])) {
            $data['error_keys'] = $this->error['keys'];
        } else {
            $data['error_keys'] = '';
        }

        $data['breadcrumbs'] = array();

       $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/rave', 'user_token=' . $this->session->data['user_token'], true)
        );


        $data['action'] = $this->url->link('extension/payment/rave', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=payment', true);
        
         $parameters  = array(
                'payment_rave_test_public_key',
                'payment_rave_test_secret_key',
                'payment_rave_live_public_key',
                'payment_rave_live_secret_key',
                'payment_rave_live',
                'payment_rave_modal_logo',
                'payment_rave_modal_title',
                'payment_rave_modal_desc',
                'payment_rave_total',
                'payment_rave_approved_status_id',
                'payment_rave_declined_status_id',
                'payment_rave_error_status_id',
                'payment_rave_geo_zone_id',
                'payment_rave_status',
                'payment_rave_sort_order',


        );

        foreach ($parameters as $key => $param) {
           if (isset($this->request->post[$param])) {
                $data[$param] = $this->request->post[$param];
            } else {
                $data[$param] = $this->config->get($param);
            }
        }
    
        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
         $this->response->setOutput($this->load->view('extension/payment/rave', $data));
    }
    
    private function validate() 
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/rave')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        $live_secret_key = $this->request->post['payment_rave_live_secret_key'];
        $live_public_key = $this->request->post['payment_rave_live_public_key'];
        $test_secret_key = $this->request->post['payment_rave_test_secret_key'];
        $test_public_key = $this->request->post['payment_rave_test_public_key'];
        
       
        if ($this->request->post['payment_rave_live'] && (($live_secret_key == NULL ) || ($live_secret_key == ""))) {
            $this->error['keys'] = $this->language->get('error_live_secret_key');
        }
        if ($this->request->post['payment_rave_live'] && (($live_public_key == NULL ) || ($live_public_key == ""))) {
            $this->error['keys'] = $this->language->get('error_live_public_key');
        }
        if (!$this->request->post['payment_rave_live'] && (($test_secret_key == NULL ) || ($test_secret_key == ""))) {
            $this->error['keys'] = $this->language->get('error_test_secret_key');
        }
        if (!$this->request->post['payment_rave_live'] && (($test_public_key == NULL ) || ($test_public_key == ""))) {
            $this->error['keys'] = $this->language->get('error_test_public_key');
        }

        return !$this->error;
    }
}