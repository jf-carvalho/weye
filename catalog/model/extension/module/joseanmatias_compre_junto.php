<?php

class ModelExtensionModuleJoseanMatiasCompreJunto extends Model {

    public function getProductCompreJunto($product_id) {

        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $product_data = array();

        $joseanmatias_compre_junto_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "joseanmatias_compre_junto WHERE product_id = '" . (int) $product_id . "' AND compre_junto_id != '" . (int) $product_id . "' AND ((date_start = '0000-00-00' OR date_start <= NOW()) AND (date_end = '0000-00-00' OR date_end >= NOW())) ORDER BY sort_order");

        foreach ($joseanmatias_compre_junto_query->rows as $result) {
            $product_query = $this->db->query("
                  SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, ss.name AS stock,
                  (SELECT AVG(r.rating) FROM " . DB_PREFIX . "review r WHERE p.product_id = r.product_id GROUP BY r.product_id) AS rating,
                  (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int) $customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start <= NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end >= NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special
                  FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                  LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
                  LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
                  WHERE p.product_id = '" . (int) $result['compre_junto_id'] . "' AND p.quantity > '0' AND p.status = '1' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW()");

            if ($product_query->num_rows) {
                $product_data[] = array(
                    'product_id' => $product_query->row['product_id'],
                    'product_option_value' => $result['product_option_value'],
                    'name' => $product_query->row['name'],
                    'model' => $product_query->row['model'],
                    'price' => $product_query->row['price'],
                    'special' => ((float) $result['price'] > 0 ? $result['price'] : $product_query->row['special']),
                    'image' => $product_query->row['image'],
                    'tax_class_id' => $product_query->row['tax_class_id']
                );
            }
        }

        return $product_data;
    }

    public function getProductDiscount($compre_junto_id, $product_id) {
        $compre_junto_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "joseanmatias_compre_junto WHERE compre_junto_id = '" . (int) $compre_junto_id . "' AND product_id = '" . (int) $product_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY sort_order LIMIT 1");

        if (isset($compre_junto_query->row['price'])) {
            return (float) $compre_junto_query->row['price'];
        } else {
            return 0;
        }
    }

}
?>