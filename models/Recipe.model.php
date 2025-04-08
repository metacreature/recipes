
<?php

require_once (DOCUMENT_ROOT . '/_lib/base.model.php');

class Model_Recipe extends Model_Base{

    
    function get_category_list() {
        $res = $this->_db->executeUnbufferedQuery('SELECT 
            category_id,
            category_name
            FROM tbl_category
            ORDER BY category_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['category_id']] = $row['category_name'];
        }
        return $data;
    }

    function get_tag_list() {
        $res = $this->_db->executeUnbufferedQuery('SELECT 
            tag_id,
            tag_name
            FROM tbl_tag
            ORDER BY tag_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['tag_id']] = $row['tag_name'];
        }
        return $data;
    }

    function get_ingredients_list() {
        $res = $this->_db->executeUnbufferedQuery('SELECT
            ingredients_id,
            ingredients_name
            FROM tbl_ingredients 
            ORDER BY ingredients_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['tag_id']] = $row['tag_name'];
        }
        return $data;
    }

    protected function _create_in_query($field_name, $values) {
        if (!empty($values) && is_array($values)) {
            return ' AND ' . $field_name . ' IN (' . implode(',', array_fill(0, count($values), '?')) . ') ';
        }
        return null;
    }

    protected function _get_query_value($values) {
        if (!empty($values) ) {
            if(is_array($values)) {
                return $values;
            } else {
                return [$values];
            }
        }
        return [];
    }
    
    function get_recipe_list($user_id, $user_ids, $category_ids, $tag_ids, $ingredients_ids, $name, $limit = null, $offset = null) {
        $query = $this->_create_in_query('user_id', $user_ids)
            . $this->_create_in_query('category_id', $category_ids)
            . $this->_create_in_query('tag_id', $tag_ids)
            . $this->_create_in_query('ingredients_id', $ingredients_ids)
            . (!empty($name) && strlen($name) > 1 ? " AND recipe_name LIKE CONCAT ('%', ?, '%') " : '');


        $query_values = array_merge(
            [$user_id ? $user_id : 0],
            $this->_get_query_value($user_ids),
            $this->_get_query_value($category_ids),
            $this->_get_query_value($tag_ids),
            $this->_get_query_value($ingredients_ids),
            $this->_get_query_value($name)
        );

        $join = '';
        if (!empty($tag_ids)) {
            $join .= ' INNER JOIN tbl_recipe_tag USING (recipe_id) ';
        }
        if (!empty($ingredients_ids)) {
            $join .= ' INNER JOIN tbl_recipe_ingredients USING (recipe_id) ';
        }

        $this->_db->executePreparedQuery('SELECT recipe_id, recipe_name, user_id, public
            FROM tbl_recipe '. $join.'
            WHERE (public = 1 OR user_id = ?) '. $query
            . ($limit ? ' LIMIT ' . (int)$limit : '')
            . ($offset ? ' OFFSET ' . (int)$offset : ''),
            $query_values
        );

        return $this->_db->getAssocResults();
    }

}