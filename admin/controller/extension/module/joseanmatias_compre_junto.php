<?php

class ControllerExtensionModuleJoseanMatiasCompreJunto extends Controller {

    public function index() {
        $this->load->language('extension/module/joseanmatias_compre_junto');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_setting_setting->editSetting('module_joseanmatias_compre_junto', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_inner'),
            'href' => $this->url->link('extension/module/joseanmatias_compre_junto', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/joseanmatias_compre_junto', 'user_token=' . $this->session->data['user_token'], true);
        $data['products'] = $this->url->link('extension/module/joseanmatias_compre_junto/products', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $this->document->setTitle($this->language->get('heading_title_inner'));
        $data['heading_title'] = $this->language->get('heading_title_inner');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_image_thumb'] = $this->language->get('entry_image_thumb');
        $data['entry_box_height'] = $this->language->get('entry_box_height');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_products'] = $this->language->get('button_products');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['module_joseanmatias_compre_junto_status'])) {
            $data['module_joseanmatias_compre_junto_status'] = $this->request->post['module_joseanmatias_compre_junto_status'];
        } else {
            $data['module_joseanmatias_compre_junto_status'] = $this->config->get('module_joseanmatias_compre_junto_status');
        }

        if (isset($this->request->post['module_joseanmatias_compre_junto_thumb_width'])) {
            $data['module_joseanmatias_compre_junto_thumb_width'] = $this->request->post['module_joseanmatias_compre_junto_thumb_width'];
        } elseif ($this->config->get('module_joseanmatias_compre_junto_thumb_width')) {
            $data['module_joseanmatias_compre_junto_thumb_width'] = $this->config->get('module_joseanmatias_compre_junto_thumb_width');
        } else {
            $data['module_joseanmatias_compre_junto_thumb_width'] = 100;
        }

        if (isset($this->request->post['module_joseanmatias_compre_junto_thumb_height'])) {
            $data['module_joseanmatias_compre_junto_thumb_height'] = $this->request->post['module_joseanmatias_compre_junto_thumb_height'];
        } elseif ($this->config->get('module_joseanmatias_compre_junto_thumb_height')) {
            $data['module_joseanmatias_compre_junto_thumb_height'] = $this->config->get('module_joseanmatias_compre_junto_thumb_height');
        } else {
            $data['module_joseanmatias_compre_junto_thumb_height'] = 100;
        }

        if (isset($this->request->post['module_joseanmatias_compre_junto_box_height'])) {
            $data['module_joseanmatias_compre_junto_box_height'] = $this->request->post['module_joseanmatias_compre_junto_box_height'];
        } elseif ($this->config->get('module_joseanmatias_compre_junto_box_height')) {
            $data['module_joseanmatias_compre_junto_box_height'] = $this->config->get('module_joseanmatias_compre_junto_box_height');
        } else {
            $data['module_joseanmatias_compre_junto_box_height'] = 350;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/joseanmatias_compre_junto', $data));
    }

    public function products_delete() {
        $json = array();

        if (isset($this->request->post['compre_junto_id']) && $this->request->post['compre_junto_id']) {            
            $this->load->model('extension/module/joseanmatias_compre_junto');
            
            $this->model_extension_module_joseanmatias_compre_junto->deleteCompreJunto($this->request->post['compre_junto_id']);

            $json['success'] = true;
        }
        
        $this->response->setOutput(json_encode($json));
    }

    public function products() {

        $this->load->language('extension/module/joseanmatias_compre_junto');

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/joseanmatias_compre_junto', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_products'),
            'href' => $this->url->link('extension/module/joseanmatias_compre_junto/products', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['cancel'] = $this->url->link('extension/module/joseanmatias_compre_junto', 'user_token=' . $this->session->data['user_token'], true);
        $data['delete'] = str_replace('&amp;', '&', $this->url->link('extension/module/joseanmatias_compre_junto/products_delete', 'user_token=' . $this->session->data['user_token'], true));
           
        $data['products'] = array();

        $filter_data = array(
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $this->load->model('extension/module/joseanmatias_compre_junto');

        $products_total = $this->model_extension_module_joseanmatias_compre_junto->getTotalProductsHasCompreJunto();

        $results = $this->model_extension_module_joseanmatias_compre_junto->getProductsHasCompreJunto($filter_data);

        foreach ($results as $result) {
            
            $compre_junto_results = $this->model_extension_module_joseanmatias_compre_junto->getCompreJunto($result['product_id']);
            
            $compre_junto_products = array();
            
            if($compre_junto_results) {
                foreach($compre_junto_results as $compre_junto_result) {
                    $compre_junto_products[] = array(
                        'compre_junto_id' => $compre_junto_result['compre_junto_id'],
                        'compre_junto_name' => $compre_junto_result['compre_junto_name'],
                        'date_start' => ($compre_junto_result['date_start'] != '0000-00-00' ? $compre_junto_result['date_start'] : ''),
                        'date_end' => ($compre_junto_result['date_end'] != '0000-00-00' ? $compre_junto_result['date_end'] : ''),
                        'price' => $this->currency->format($compre_junto_result['price'], $this->config->get('config_currency'))                        
                    );
                }
            }
            
            $data['products'][] = array(
                'name' => $result['name'],
                'href' => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'], true),
                'compre_junto_products' => $compre_junto_products,
                'delete' => $this->url->link('extension/module/joseanmatias_compre_junto/products', 'user_token=' . $this->session->data['user_token'] . '&joseanmatias_compre_junto_id=' . $result['joseanmatias_compre_junto_id'] . $url, true)
            );
        }

        $this->document->setTitle($this->language->get('heading_title_products'));
        $data['heading_title'] = $this->language->get('heading_title_products');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['column_product'] = $this->language->get('column_product');
        $data['column_compre_junto'] = $this->language->get('column_compre_junto');

        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_config'] = $this->language->get('button_config');
        $data['button_delete'] = $this->language->get('button_delete');

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('extension/module/joseanmatias_compre_junto/products', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $url = '';

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $products_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/joseanmatias_compre_junto/products', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($products_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($products_total - $this->config->get('config_limit_admin'))) ? $products_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $products_total, ceil($products_total / $this->config->get('config_limit_admin')));

        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/joseanmatias_compre_junto_products', $data));
    }

    public function product_tab() {
        $data = array();

        if ($this->config->get('module_joseanmatias_compre_junto_status')) {
            $this->load->language('extension/module/joseanmatias_compre_junto');
            $this->load->model('extension/module/joseanmatias_compre_junto');

            if (isset($this->request->get['product_id'])) {
                $data['product_id'] = $this->request->get['product_id'];
            } else {
                $data['product_id'] = 0;
            }
            $data['user_token'] = $this->session->data['user_token'];

            $data['entry_name'] = $this->language->get('entry_name');
            $data['entry_date_start'] = $this->language->get('entry_date_start');
            $data['entry_date_end'] = $this->language->get('entry_date_end');
            $data['entry_sort_order'] = $this->language->get('entry_sort_order');
            $data['entry_price'] = $this->language->get('entry_price');
            $data['button_add'] = $this->language->get('button_add');
            $data['button_remove'] = $this->language->get('button_remove');

            $data['error_product_main'] = $this->language->get('error_product_main');

            $data['products'] = array();

            if (isset($this->request->post['joseanmatias_compre_junto'])) {

                foreach ($this->request->post['joseanmatias_compre_junto'] as $joseanmatias_compre_junto) {
                    $data['products'][] = array(
                        'compre_junto_id' => $joseanmatias_compre_junto['compre_junto_id'],
                        'compre_junto_name' => $joseanmatias_compre_junto['compre_junto_name'],
                        'price' => $joseanmatias_compre_junto['price'],
                        'date_start' => ($joseanmatias_compre_junto['date_start'] != '0000-00-00' ? $joseanmatias_compre_junto['date_start'] : ''),
                        'date_end' => ($joseanmatias_compre_junto['date_end'] != '0000-00-00' ? $joseanmatias_compre_junto['date_end'] : ''),
                        'sort_order' => $joseanmatias_compre_junto['sort_order']
                    );
                }
            } elseif (isset($this->request->get['product_id'])) {
                $data['products'] = $this->model_extension_module_joseanmatias_compre_junto->getCompreJunto($this->request->get['product_id']);
            }
        }

        return $this->load->view('extension/module/joseanmatias_compre_junto_tab', $data);
    }

    public function install() {
        $this->load->model('extension/module/joseanmatias_compre_junto');
        $this->model_extension_module_joseanmatias_compre_junto->createTable();
    }

    public function uninstall() {
        $this->load->model('extension/module/joseanmatias_compre_junto');
        $this->model_extension_module_joseanmatias_compre_junto->dropTable();
    }

}

?>