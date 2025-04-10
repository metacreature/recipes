
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
            $data[$row['ingredients_id']] = $row['ingredients_name'];
        }
        return $data;
    }

    function get_unit_list() {
        $res = $this->_db->executeUnbufferedQuery('SELECT 
            unit_id,
            unit_name
            FROM tbl_unit
            ORDER BY unit_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['unit_id']] = $row['unit_name'];
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
    
    function list($user_id, $user_ids, $category_ids, $tag_ids, $ingredients_ids, $name, $limit = null, $offset = null) {
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

        $this->_db->executePreparedQuery('SELECT 
                tbl_recipe.recipe_id as recipe_id,
                tbl_category.category_name as category_name,
                tbl_recipe.recipe_name as recipe_name, 
                tbl_user.user_name as user_name, 
                tbl_recipe.public as public
            FROM tbl_recipe 
            INNER JOIN tbl_category USING (category_id)
            INNER JOIN tbl_user USING (user_id)
            '. $join.'
            WHERE (tbl_recipe.public = 1 OR tbl_recipe.user_id = ?) '. $query
            . ($limit ? ' LIMIT ' . (int)$limit : '')
            . ($offset ? ' OFFSET ' . (int)$offset : ''),
            $query_values
        );

        return $this->_db->getAssocResults();
    }

    function get($recipe_id, $user_id) {
        $this->_db->executePreparedQuery('SELECT * FROM tbl_recipe WHERE recipe_id = ? AND (public = 1 OR user_id = ?);', 
            [(int) $recipe_id, (int) $user_id]);
        $recipe = $this->_db->fetchAssoc();
        if (!$recipe) {
            return;
        }

        $this->_db->executePreparedQuery('SELECT tbl_tag.tag_name 
            FROM tbl_recipe_tag 
            INNER JOIN tbl_tag USING (tag_id) 
            WHERE tbl_recipe_tag.recipe_id = ? ORDER BY tbl_tag.tag_name ASC;',
            [$recipe_id]);
        $recipe['tag_list'] = array_column($this->_db->getAssocResults(), 'tag_name');

        $this->_db->executePreparedQuery('SELECT 
                tbl_recipe_ingredients.is_alternative as is_alternative,
                tbl_recipe_ingredients.quantity as quantity,
                tbl_unit.unit_name as unit_name, 
                tbl_ingredients.ingredients_name as ingredients_name 
            FROM tbl_recipe_ingredients 
            INNER JOIN tbl_unit USING (unit_id) 
            INNER JOIN tbl_ingredients USING (ingredients_id) 
            WHERE tbl_recipe_ingredients.recipe_id = ? ORDER BY tbl_recipe_ingredients.nr ASC;',
            [$recipe_id]);
        $recipe['ingredients_list'] = $this->_db->getAssocResults();

        $this->_db->executePreparedQuery('SELECT 
                step_description 
            FROM tbl_recipe_step
            WHERE recipe_id = ? ORDER BY nr ASC;',
            [$recipe_id]);
        $recipe['step_list'] = array_column($this->_db->getAssocResults(), 'step_description');
        return $recipe;
    }

    function save($user_id, $recipe_id, $public, $category_id, $recipe_name, $persons, $original_text, $tag_list, $ingredients_list, $step_list) {
        
        $this->_db->begin();
        try{
            if ($recipe_id) {
                $this->_db->executePreparedQuery('UPDATE tbl_recipe SET 
                    public = ?, 
                    category_id = ?, 
                    recipe_name = ?, 
                    persons = ?, 
                    original_text = ?,
                    last_edited = NOW()
                WHERE recipe_id = ? AND user_id = ?;', 
                [$public, $category_id, $recipe_name, $persons, $original_text, $recipe_id, $user_id]);
                if ($this->_db->getAffectedRows() != 1) {
                    $this->_db->rollback();
                    return;
                }

                $this->_clean_recipe_refs($recipe_id);
                $this->_save_tag_list($recipe_id, $tag_list);
                $this->_save_ingredients_list($recipe_id, $ingredients_list);
                $this->_save_step_list($recipe_id, $step_list);
                $this->_clean_refs();

            } else {
                $this->_db->executePreparedQuery('INSERT INTO tbl_recipe SET 
                    public = ?, 
                    category_id = ?, 
                    recipe_name = ?, 
                    persons = ?, 
                    original_text = ?,
                    last_edited = NOW(),
                    user_id = ?;', 
                [$public, $category_id, $recipe_name, $persons, $original_text, $user_id]);
                if ($this->_db->getAffectedRows() != 1) {
                    $this->_db->rollback();
                    return;
                }
                $recipe_id = $this->_db->getLastInsertId();
                
                $this->_save_tag_list($recipe_id, $tag_list);
                $this->_save_ingredients_list($recipe_id, $ingredients_list);
                $this->_save_step_list($recipe_id, $step_list);
            }
        } catch (Exception $e) {
            $this->_db->rollback();
            return;
        }
        $this->_db->commit();
        return $recipe_id;
    }

    protected function _save_tag_list($recipe_id, $tag_list) {
        if (empty($tag_list)) return;

        $this->_db->executePreparedQuery('INSERT IGNORE INTO tbl_tag (tag_name) 
            VALUES (' . implode('),(', array_fill(0, count($tag_list), '?')) . ')',
            $tag_list);

        $values = array();
        foreach($tag_list as $row) {
            $values[] = $recipe_id;
            $values[] = $row;
        }

        $this->_db->executePreparedQuery('INSERT INTO tbl_recipe_tag (recipe_id, tag_id) 
            VALUES (' . implode('),(', array_fill(0, count($tag_list), '?, (SELECT tag_id FROM tbl_tag WHERE tag_name = ?)')) . ')',
            $values);
        
    }

    protected function _save_ingredients_list($recipe_id, $ingredients_list) {
        if (empty($ingredients_list)) return;

        $ingredients_name_list = array_unique(array_column($ingredients_list, 'ingredients_name'));
        $unit_name_list = array_unique(array_column($ingredients_list, 'unit_name'));

        $this->_db->executePreparedQuery('INSERT IGNORE INTO tbl_ingredients (ingredients_name) 
            VALUES (' . implode('),(', array_fill(0, count($ingredients_name_list), '?')) . ')',
            $ingredients_name_list);

        
        $this->_db->executePreparedQuery('INSERT IGNORE INTO tbl_unit (unit_name) 
            VALUES (' . implode('),(', array_fill(0, count($unit_name_list), '?')) . ')',
            $unit_name_list);
        
        $values = array();
        $cnt = 0;
        foreach($ingredients_list as $row) {
            $cnt++;
            $values[] = $recipe_id;
            $values[] = $cnt;
            $values[] = $row['quantity'];
            $values[] = $row['is_alternative'];
            $values[] = $row['ingredients_name'];
            $values[] = $row['unit_name'];
        }

        $this->_db->executePreparedQuery('INSERT INTO tbl_recipe_ingredients (recipe_id, nr, quantity,is_alternative, ingredients_id, unit_id) 
            VALUES (' . implode('),(', array_fill(0, count($ingredients_list),
                 '?, ?, ?, ?,
                 (SELECT ingredients_id FROM tbl_ingredients WHERE ingredients_name = ?),
                 (SELECT unit_id FROM tbl_unit WHERE unit_name = ?)')) . ')',
            $values);
        
    }

    protected function _save_step_list($recipe_id, $step_list) {
        if (empty($step_list)) return;

        $values = array();
        $cnt = 0;
        foreach($step_list as $row) {
            $values[] = $recipe_id;
            $values[] = $cnt;
            $values[] = $row;
        }

        $this->_db->executePreparedQuery('INSERT INTO tbl_recipe_step (recipe_id, nr, step_description) 
            VALUES (' . implode('),(', array_fill(0, count($step_list), '?, ?, ?')) . ')',
            $values);
    }

    protected function _clean_recipe_refs($recipe_id) {
        $this->_db->executePreparedQuery('DELETE FROM tbl_recipe_tag WHERE recipe_id = ?', [$recipe_id]);
        $this->_db->executePreparedQuery('DELETE FROM tbl_recipe_ingredients WHERE recipe_id = ?', [$recipe_id]);
        $this->_db->executePreparedQuery('DELETE FROM tbl_recipe_step WHERE recipe_id = ?', [$recipe_id]);
    }
    
    protected function _clean_refs() {
        $this->_db->executeQuery('DELETE FROM tbl_tag 
            WHERE locked = 0 AND tag_id NOT IN (SELECT tag_id FROM tbl_recipe_tag GROUP BY tag_id)');
        $this->_db->executeQuery('DELETE FROM tbl_ingredients 
            WHERE ingredients_id NOT IN (SELECT ingredients_id FROM tbl_recipe_ingredients GROUP BY ingredients_id)');
        $this->_db->executeQuery('DELETE FROM tbl_unit 
            WHERE locked = 0 AND unit_id NOT IN (SELECT unit_id FROM tbl_recipe_ingredients GROUP BY unit_id)');
    }
}
