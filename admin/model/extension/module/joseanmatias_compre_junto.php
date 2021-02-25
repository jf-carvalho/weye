<?php

class ModelExtensionModuleJoseanMatiasCompreJunto extends Model {

    public function addCompreJunto($product_id, $products_compre_junto = array()) {
        if ($products_compre_junto && $this->config->get('module_joseanmatias_compre_junto_status')) {
            foreach ($products_compre_junto as $compre_junto) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "joseanmatias_compre_junto SET product_id = '" . (int) $product_id . "', compre_junto_id = '" . (int) $compre_junto['compre_junto_id'] . "', product_option_value = '', price = '" . (float) $compre_junto['price'] . "', sort_order = '" . (int) $compre_junto['sort_order'] . "', date_start = '" . $this->db->escape($compre_junto['date_start']) . "', date_end = '" . $this->db->escape($compre_junto['date_end']) . "'");
            }
        }
    }

    public function editCompreJunto($product_id, $products_compre_junto = array()) {
        if ($this->config->get('module_joseanmatias_compre_junto_status')) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "joseanmatias_compre_junto WHERE product_id = '" . (int) $product_id . "'");

            if ($products_compre_junto) {
                foreach ($products_compre_junto as $compre_junto) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "joseanmatias_compre_junto SET product_id = '" . (int) $product_id . "', compre_junto_id = '" . (int) $compre_junto['compre_junto_id'] . "', product_option_value = '', price = '" . (float) $compre_junto['price'] . "', sort_order = '" . (int) $compre_junto['sort_order'] . "', date_start = '" . $this->db->escape($compre_junto['date_start']) . "', date_end = '" . $this->db->escape($compre_junto['date_end']) . "'");
                }
            }
        }
    }

    public function deleteCompreJunto($compre_junto_id) {
        if ($this->config->get('module_joseanmatias_compre_junto_status')) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "joseanmatias_compre_junto WHERE compre_junto_id = '" . (int) $compre_junto_id . "'");
        }
    }

    public function getProductsHasCompreJunto($data) {

        $sql = "SELECT DISTINCT pa.*, pd.name FROM " . DB_PREFIX . "joseanmatias_compre_junto pa LEFT JOIN " . DB_PREFIX . "product_description pd ON pa.product_id = pd.product_id WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' GROUP BY pa.product_id ORDER BY pd.name";

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
    
    public function getTotalProductsHasCompreJunto() {

        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "joseanmatias_compre_junto GROUP BY product_id");

        return $query->num_rows;
    }

    public function getCompreJunto($product_id) {
        if ($this->config->get('module_joseanmatias_compre_junto_status')) {
            $query = $this->db->query("SELECT DISTINCT pa.*, pd.name FROM " . DB_PREFIX . "joseanmatias_compre_junto pa INNER JOIN " . DB_PREFIX . "product p ON pa.compre_junto_id = p.product_id LEFT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id WHERE pa.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY pa.sort_order");

            $products = array();

            foreach ($query->rows as $result) {
                $products[] = array(
                    'joseanmatias_compre_junto_id' => $result['joseanmatias_compre_junto_id'],
                    'compre_junto_id' => $result['compre_junto_id'],
                    'compre_junto_name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'price' => $result['price'],                    
                    'date_start' => ($result['date_start'] != '0000-00-00' ? $result['date_start'] : ''),
                    'date_end' => ($result['date_end'] != '0000-00-00' ? $result['date_end'] : ''),
                    'sort_order' => $result['sort_order']
                );
            }

            return $products;
        }
    }

    public function createTable() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "joseanmatias_compre_junto` (
                    `joseanmatias_compre_junto_id` int(11) NOT NULL AUTO_INCREMENT,
                    `product_id` int(11) NOT NULL,
                    `compre_junto_id` int(11) NOT NULL,
                    `product_option_value` varchar(255) NOT NULL,
                    `price` decimal(15,4) NOT NULL,
                    `date_start` date NOT NULL DEFAULT '0000-00-00',
                    `date_end` date NOT NULL DEFAULT '0000-00-00',
                    `sort_order` tinyint(4) NOT NULL,
                    PRIMARY KEY (`joseanmatias_compre_junto_id`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
    }

    public function dropTable() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "joseanmatias_compre_junto`;");
    }

}

?>
