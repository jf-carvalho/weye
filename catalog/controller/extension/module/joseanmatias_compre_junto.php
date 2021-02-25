<?php

class ControllerExtensionModuleJoseanMatiasCompreJunto extends Controller {

    public function index($product_current_info) {

        if ($this->config->get('module_joseanmatias_compre_junto_status') && $product_current_info) {
            if (isset($product_current_info['product_id'])) {
                $product_id = (int) $product_current_info['product_id'];
            } else {
                $product_id = 0;
            }

            $this->load->model('catalog/product');
            $this->load->model('extension/module/joseanmatias_compre_junto');
            $this->load->model('tool/image');

            $product_thumb_width = ((int) $this->config->get('module_joseanmatias_compre_junto_thumb_width') ? (int) $this->config->get('module_joseanmatias_compre_junto_thumb_width') : 100);
            $product_thumb_height = ((int) $this->config->get('module_joseanmatias_compre_junto_thumb_height') ? (int) $this->config->get('module_joseanmatias_compre_junto_thumb_height') : 100);

            $data['box_height'] = ((int) $this->config->get('module_joseanmatias_compre_junto_box_height') ? (int) $this->config->get('module_joseanmatias_compre_junto_box_height') : 350);

            $data['text_select'] = $this->language->get('text_select');
            $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
            $data['button_add_to_cart'] = $this->url->link('extension/module/joseanmatias_compre_junto/opencart_cart_add', 'product_id=' . $product_id);

            if ((int) $product_current_info['quantity'] <= 0) {
                return false;
            }

            if ($product_current_info['image']) {
                $product_current_info['image'] = $this->model_tool_image->resize($product_current_info['image'], $product_thumb_width, $product_thumb_height);
            }

            $product_current_info['recurrings'] = $this->model_catalog_product->getProfiles($product_current_info['product_id']);

            $compre_junto_products = $this->model_extension_module_joseanmatias_compre_junto->getProductCompreJunto($product_current_info['product_id']);

            $data['products'] = array();

            $product_current_info_special = 0.0;

            $product_current_info_price = $this->tax->calculate($product_current_info['price'], $product_current_info['tax_class_id'], $this->config->get('config_tax'));
            if ((float) $product_current_info['special']) {
                $product_current_info_special = $this->tax->calculate($product_current_info['special'], $product_current_info['tax_class_id'], $this->config->get('config_tax'));
            }
            
            foreach ($compre_junto_products as $product) {
                $price = false;
                $special = false;
                $price_aggregate = false;
                $special_aggregate = false;

                $product['price'] = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
                if ((float) $product['special']) {
                    $product['special'] = $this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax'));
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($product['price'], $this->session->data['currency']);

                    $price_aggregate = $this->currency->format(($product['price'] + $product_current_info_price), $this->session->data['currency']);
                }

                if ((float) $product['special']) {
                    $special = $this->currency->format($product['special'], $this->session->data['currency']);
                }

                if ((float) $product['special']) {
                    if ((float) $product_current_info_special) {
                        $special_aggregate = $this->currency->format(($product['special'] + $product_current_info_special), $this->session->data['currency']);
                    } else {
                        $special_aggregate = $this->currency->format(($product['special'] + $product_current_info_price), $this->session->data['currency']);
                    }
                } else {
                    if ((float) $product_current_info_special) {
                        $special_aggregate = $this->currency->format(($product['price'] + $product_current_info_special), $this->session->data['currency']);
                    }
                }

                $product_option_data = array();

                foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
                    $product_option_value_data = array();

                    if (in_array($option['type'], array('select', 'radio', 'checkbox', 'image'))) {
                        foreach ($option['product_option_value'] as $option_value) {
                            if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
                                    $option_price = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                                } else {
                                    $option_price = false;
                                }

                                $product_option_value_data[] = array(
                                    'product_option_value_id' => $option_value['product_option_value_id'],
                                    'option_value_id' => $option_value['option_value_id'],
                                    'name' => $option_value['name'],
                                    'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                    'price' => $option_price,
                                    'price_prefix' => $option_value['price_prefix']
                                );
                            }
                        }

                        $product_option_data[] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_option_value' => $product_option_value_data,
                            'option_id' => $option['option_id'],
                            'name' => $option['name'],
                            'type' => $option['type'],
                            'value' => $option['value'],
                            'required' => $option['required']
                        );
                    }
                }

                if ($product['image']) {
                    $image = $product['image'];
                } else {
                    $image = 'no_image.jpg';
                }

                $data['products'][] = array(
                    'compre_junto_id' => $product['product_id'],
                    'name' => $product['name'],
                    'product_options' => $product_option_data,
                    'price' => $price,
                    'special' => $special,
                    'price_aggregate' => $price_aggregate,
                    'special_aggregate' => $special_aggregate,
                    'thumb' => $this->model_tool_image->resize($image, $product_thumb_width, $product_thumb_height),
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
            }
            
            $product_current_info['price'] = $this->currency->format($product_current_info_price, $this->session->data['currency']);
            if ((float) $product_current_info_special) {
                $product_current_info['special'] = $this->currency->format($product_current_info_special, $this->session->data['currency']);
            }
            
            $data['product_current'] = $product_current_info;

            return $this->load->view('extension/module/joseanmatias_compre_junto', $data);
        }
    }

    public function product_discount($compre_junto_id = 0) {

        if ($this->config->get('module_joseanmatias_compre_junto_status') && $compre_junto_id) {
            $this->load->model('extension/module/joseanmatias_compre_junto');

            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

            foreach ($cart_query->rows as $cart) {
                $product_discount = 0;

                if ($compre_junto_id != $cart['product_id']) {

                    $product_discount = $this->model_extension_module_joseanmatias_compre_junto->getProductDiscount($compre_junto_id, $cart['product_id']);

                    if ($product_discount) {
                        return $product_discount;
                    }
                }
            }
        }

        return false;
    }

    public function virtual_cart_add() {
        ob_start();
        $json = array();
        $this->session->data['joseanmatias_virtual_cart'] = array();
        $cart_values = array();

        if (isset($this->request->post['compre_junto']) && $this->config->get('module_joseanmatias_compre_junto_status')) {
            $this->load->model('catalog/product');
            $this->load->language('checkout/cart');

            if (isset($this->request->post['current_option'])) {
                $option = array_filter($this->request->post['current_option']);
            } else {
                $option = array();
            }

            $option_array = array();

            $product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);

            foreach ($product_options as $product_option) {
                if (in_array($product_option['type'], array('select', 'radio', 'checkbox', 'image'))) {
                if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
                    $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                } else {
                    $option_array[$product_option['product_option_id']] = $option[$product_option['product_option_id']];
                }
                }
            }

            $cart_values[$this->request->get['product_id']]['option'] = json_encode($option_array);

            if (isset($this->request->post['recurring_id'])) {
                $recurring_id = $this->request->post['recurring_id'];
            } else {
                $recurring_id = 0;
            }

            $recurrings = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

            if ($recurrings) {
                $recurring_ids = array();

                foreach ($recurrings as $recurring) {
                    $recurring_ids[] = $recurring['recurring_id'];
                }

                if (!in_array($recurring_id, $recurring_ids)) {
                    $json['error']['recurring'] = $this->language->get('error_recurring_required');
                }

                $cart_values[$this->request->get['product_id']]['recurring_id'] = $recurring_id;
            }

            $aggregate_option_array = array();

            foreach ($this->request->post['compre_junto'] as $compre_junto_id) {

                if (isset($this->request->post['aggregate_option'])) {
                    $aggregate_option = array_filter($this->request->post['aggregate_option']);
                } else {
                    $aggregate_option = array();
                }

                $product_aggregate_options = $this->model_catalog_product->getProductOptions($compre_junto_id);

                foreach ($product_aggregate_options as $product_aggregate_option) {
                    if ($product_aggregate_option['type'] == 'select' || $product_aggregate_option['type'] == 'radio' || $product_aggregate_option['type'] == 'checkbox' || $product_aggregate_option['type'] == 'image') {
                        if ($product_aggregate_option['required'] && empty($aggregate_option[$product_aggregate_option['product_option_id']])) {
                            $json['error']['aggregate_option'][$product_aggregate_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_aggregate_option['name']);
                        } else {
                            $aggregate_option_array[$product_aggregate_option['product_option_id']] = $aggregate_option[$product_aggregate_option['product_option_id']];
                        }
                    }
                }

                $cart_values[$compre_junto_id]['option'] = json_encode($aggregate_option_array);
            }

            if (!$json && $cart_values) {
                $this->session->data['joseanmatias_virtual_cart'] = $cart_values;

                $json['status'] = true;
            }
        }

        @ob_end_clean();
        @ob_end_flush();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function virtual_cart() {
        $json = array();

        $products_names = '';
        $products_price = 0;

        if (isset($this->session->data['joseanmatias_virtual_cart'])) {
            $this->load->model('extension/module/joseanmatias_compre_junto');
            $this->load->model('catalog/product');

            $option_price = 0;
            
            foreach ($this->session->data['joseanmatias_virtual_cart'] as $compre_junto_id => $compre_junto_options) {
                $compre_junto_info = $this->model_catalog_product->getProduct($compre_junto_id);

                $compre_junto_price = $this->model_extension_module_joseanmatias_compre_junto->getProductDiscount($compre_junto_id, $this->request->get['product_id']);
               
                if (isset($compre_junto_options['option'])) {
                    $aggregate_option = json_decode($compre_junto_options['option'], true);
                } else {
                    $aggregate_option = array();
                }

                $product_aggregate_options = $this->model_catalog_product->getProductOptions($compre_junto_id);

                foreach ($product_aggregate_options as $product_aggregate_option) {
                    if ($product_aggregate_option['type'] == 'select' || $product_aggregate_option['type'] == 'radio' || $product_aggregate_option['type'] == 'image') {                        
                        if (isset($aggregate_option[$product_aggregate_option['product_option_id']])) {                            
                            foreach ($product_aggregate_option['product_option_value'] as $product_aggregate_option_value) {
                                if ($aggregate_option[$product_aggregate_option['product_option_id']] == $product_aggregate_option_value['product_option_value_id']) {
                                    if ($product_aggregate_option_value['price_prefix'] == '+') {
                                        $option_price += $this->tax->calculate($product_aggregate_option_value['price'], $compre_junto_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
                                    } elseif ($product_aggregate_option_value['price_prefix'] == '-') {
                                        $option_price -= $this->tax->calculate($product_aggregate_option_value['price'], $compre_junto_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
                                    }
                                }
                            }
                        }
                    } elseif ($product_aggregate_option['type'] == 'checkbox' && is_array($aggregate_option[$product_aggregate_option['product_option_id']])) {
                        foreach ($product_aggregate_option['product_option_value'] as $product_aggregate_option_value) {
                            if (in_array($product_aggregate_option_value['product_option_value_id'], $aggregate_option[$product_aggregate_option['product_option_id']])) {
                                if ($product_aggregate_option_value['price_prefix'] == '+') {
                                    $option_price += $this->tax->calculate($product_aggregate_option_value['price'], $compre_junto_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
                                } elseif ($product_aggregate_option_value['price_prefix'] == '-') {
                                    $option_price -= $this->tax->calculate($product_aggregate_option_value['price'], $compre_junto_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
                                }
                            }
                        }
                    }
                }
                
                $products_names .= ' + ' . html_entity_decode($compre_junto_info['name']);

                if ($compre_junto_price) {
                    $products_price += (float) $compre_junto_price;
                } else {
                    if ($compre_junto_info['special']) {
                        $products_price += $this->tax->calculate((float) $compre_junto_info['special'], $compre_junto_info['tax_class_id'], $this->config->get('config_tax'));
                    } else {
                        $products_price += $this->tax->calculate((float) $compre_junto_info['price'], $compre_junto_info['tax_class_id'], $this->config->get('config_tax'));
                    }
                }
            }

            $json = array(
                'names' => preg_replace('/^\+/', '', trim($products_names)),
                'price' => $products_price ? $this->currency->format($products_price + $option_price, $this->session->data['currency']) : ''
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function virtual_cart_clear() {
        if (isset($this->session->data['joseanmatias_virtual_cart'])) {
            $this->session->data['joseanmatias_virtual_cart'] = array();
        }
    }

    public function opencart_cart_add() {

        if (isset($this->session->data['joseanmatias_virtual_cart']) && $this->config->get('module_joseanmatias_compre_junto_status')) {
            $this->load->model('catalog/product');

            foreach ($this->session->data['joseanmatias_virtual_cart'] as $compre_junto_id => $compre_junto_options) {
                $aggregate_info = $this->model_catalog_product->getProduct($compre_junto_id);

                if (isset($compre_junto_options['option'])) {
                    $aggregate_option = json_decode($compre_junto_options['option'], true);
                } else {
                    $aggregate_option = array();
                }

                if (isset($compre_junto_options['recurring_id'])) {
                    $recurring_id = $compre_junto_options['recurring_id'];
                } else {
                    $recurring_id = 0;
                }

                $aggregate_quantity = $aggregate_info['minimum'] ? $aggregate_info['minimum'] : 1;

                $this->cart->add($compre_junto_id, $aggregate_quantity, $aggregate_option, $recurring_id);
            }
        }

        $this->response->redirect($this->url->link('checkout/cart'));
    }

}

?>
