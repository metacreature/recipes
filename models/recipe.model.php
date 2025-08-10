<?php
/*
 File: recipe.model.php
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 
 MIT License
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
*/


require_once (DOCUMENT_ROOT . '/_lib/base.model.php');

class Model_Recipe extends Model_Base{

    
    function get_category_list() {

        $res = $this->_db->executeUnbufferedQuery('SELECT 
            category_id,
            category_name
            FROM tbl_category
            ORDER BY category_id ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['category_id']] = $row['category_name'];
        }
        return $data;
    }

    function get_tag_list() {
        
        $res = $this->_db->executePreparedQuery('SELECT 
            tag_id,
            tag_name
            FROM tbl_tag
            JOIN tbl_recipe_tag USING (tag_id)
            JOIN tbl_recipe USING(recipe_id)
            WHERE (tbl_recipe.public = 1 OR tbl_recipe.user_id = ?) AND tbl_recipe.deleted = 0 
            ORDER BY tbl_tag.tag_id ASC;', 
            [$this->_user_id]);

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['tag_id']] = $row['tag_name'];
        }
        return $data;
    }

    function get_ingredients_list() {

        $res = $this->_db->executePreparedQuery('SELECT
            ingredients_id,
            ingredients_name
            FROM tbl_ingredients 
            JOIN tbl_recipe_ingredients USING (ingredients_id)
            JOIN tbl_recipe USING(recipe_id)
            WHERE (tbl_recipe.public = 1 OR tbl_recipe.user_id = ?) AND tbl_recipe.deleted = 0 
            ORDER BY tbl_ingredients.ingredients_name ASC;', 
            [$this->_user_id]);

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['ingredients_id']] = $row['ingredients_name'];
        }
        return $data;
    }

    function get_unit_list() {

        $res = $this->_db->executePreparedQuery('SELECT 
            unit_id,
            unit_name
            FROM tbl_unit
            JOIN tbl_recipe_ingredients USING (unit_id)
            JOIN tbl_recipe USING(recipe_id)
            WHERE (tbl_recipe.public = 1 OR tbl_recipe.user_id = ?) AND tbl_recipe.deleted = 0
            ORDER BY tbl_unit.unit_id ASC;', 
            [$this->_user_id]);

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['unit_id']] = $row['unit_name'];
        }
        return $data;
    }

    protected function _create_in_query($field_name, $values) {
        if (is_array($values) && count($values) > 0) {
            return ' AND ' . $field_name . ' IN (' . implode(',', array_fill(0, count($values), '?')) . ') ';
        }
        return null;
    }

    protected function _create_and_query($field_name, $table_name, $values) {
        if (is_array($values)) {
            $ret = '';
            foreach($values as $i) {
                $ret .= ' AND EXISTS (SELECT recipe_id FROM ' . $table_name . ' WHERE ' . $field_name . ' = ? AND ' . $table_name . '.recipe_id = tbl_recipe.recipe_id) ';
            }
            return $ret;
        }
        return null;
    }

    protected function _get_query_value($values) {
        if (is_null($values)){
            return [];
        }
        if(is_array($values)) {
            return $values;
        } else {
            return [$values];
        }
    }
    
    function list($user_ids, $category_ids, $tag_ids, $ingredients_ids, $name, $sort, $limit = null, $offset = null) {
        $query = $this->_create_in_query('user_id', $user_ids)
            . $this->_create_in_query('category_id', $category_ids)
            . $this->_create_and_query('tag_id', 'tbl_recipe_tag', $tag_ids)
            . $this->_create_and_query('ingredients_id', 'tbl_recipe_ingredients', $ingredients_ids)
            . (!is_null($name) && strlen($name) >= 1 ? " AND recipe_name LIKE CONCAT ('%', ?, '%') " : '');

        $query_values = array_merge(
            [$this->_user_id],
            $this->_get_query_value($user_ids),
            $this->_get_query_value($category_ids),
            $this->_get_query_value($tag_ids),
            $this->_get_query_value($ingredients_ids),
            $this->_get_query_value($name)
        );

        $sort_list = [
            'tbl_recipe.category_id, tbl_recipe.recipe_name ASC',
            'COALESCE(tbl_recipe.costs / tbl_recipe.persons, 2147483640) ASC, tbl_recipe.recipe_name ASC' ,
            'COALESCE(tbl_recipe.costs / tbl_recipe.persons, -2147483640)  DESC, tbl_recipe.recipe_name ASC' ,
            'COALESCE(tbl_recipe.duration, 2147483640) ASC, tbl_recipe.recipe_name ASC' ,
            'COALESCE(tbl_recipe.duration, -2147483640)  DESC, tbl_recipe.recipe_name ASC',
            'COALESCE(tbl_recipe.total_duration, 2147483640) ASC, tbl_recipe.recipe_name ASC' ,
            'COALESCE(tbl_recipe.total_duration, -2147483640)  DESC, tbl_recipe.recipe_name ASC',
        ];
        if (!empty($sort) && !empty($sort_list[$sort])) {
            $order = $sort_list[$sort];
        } else {
            $order = $sort_list[0];
        }

        $this->_db->executePreparedQuery('SELECT 
                tbl_recipe.recipe_id as recipe_id,
                tbl_category.category_name as category_name,
                tbl_recipe.recipe_name as recipe_name, 
                tbl_recipe.image_name as image_name, 
                tbl_user.user_name as user_name, 
                tbl_recipe.public as public,
                tbl_recipe.costs / tbl_recipe.persons as costs_pp, 
                tbl_recipe.duration as duration, 
                tbl_recipe.total_duration as total_duration
            FROM tbl_recipe 
            INNER JOIN tbl_category USING (category_id)
            INNER JOIN tbl_user USING (user_id)'
            .'WHERE (tbl_recipe.public = 1 OR tbl_recipe.user_id = ?) AND deleted = 0 '
            . $query 
            .'ORDER BY '.$order
            . ($limit ? ' LIMIT ' . (int)$limit : '')
            . ($offset ? ' OFFSET ' . (int)$offset : ''),
            $query_values
        );

        return $this->_db->getAssocResults();
    }

    function setdelete($recipe_id) {
        $this->_db->executePreparedQuery('UPDATE tbl_recipe SET 
            deleted = 1,
            last_edited = NOW(),
            cnt_update = cnt_update + 1
        WHERE recipe_id = ? AND user_id = ? AND deleted = 0;', 
        [(int) $recipe_id, $this->_user_id]);
        if ($this->_db->getAffectedRows() == 1) {
            return true;
        }
        return false;
    }

    function delete($recipe_id, $clean_refs = true) {
        $this->_db->executePreparedQuery('SELECT * FROM tbl_recipe WHERE recipe_id = ? AND user_id = ?;', 
            [(int)$recipe_id, $this->_user_id]);
        if ($this->_db->fetchAssoc()) {
            $this->_db->begin();
            try{
                $this->_clean_recipe_refs($recipe_id);
                if ($clean_refs) {
                    $this->clean_refs();
                }
                $this->_db->executePreparedQuery('DELETE FROM tbl_recipe WHERE recipe_id = ? AND user_id = ?;', 
                    [(int)$recipe_id, $this->_user_id]);
            } catch (Exception $e) {
                $this->_db->rollback();
                return false;
            }
            $this->_db->commit();
            return true;
        }
        return false;
    }

    function get($recipe_id) {
        $this->_db->executePreparedQuery('SELECT 
            tbl_recipe.recipe_id as recipe_id,
            tbl_recipe.category_id as category_id,
            tbl_category.category_name as category_name,
            tbl_recipe.recipe_name as recipe_name, 
            tbl_recipe.image_name as image_name, 
            tbl_recipe.user_id as user_id,  
            tbl_user.user_name as user_name, 
            tbl_recipe.public as public,
            tbl_recipe.persons as persons,
            tbl_recipe.costs as costs, 
            tbl_recipe.duration as duration, 
            tbl_recipe.total_duration as total_duration,
            tbl_recipe.original_text as original_text
            FROM tbl_recipe 
            INNER JOIN tbl_category USING (category_id)
            INNER JOIN tbl_user USING (user_id)
            WHERE tbl_recipe.recipe_id = ? AND (tbl_recipe.public = 1 OR tbl_recipe.user_id = ?) AND tbl_recipe.deleted = 0;', 
            [(int) $recipe_id, $this->_user_id]);
        $recipe = $this->_db->fetchAssoc();
        if (!$recipe) {
            return;
        }

        $recipe['editable'] = $this->_user_id == $recipe['user_id'];

        $this->_db->executePreparedQuery('SELECT tbl_tag.tag_name 
            FROM tbl_recipe_tag 
            INNER JOIN tbl_tag USING (tag_id) 
            WHERE tbl_recipe_tag.recipe_id = ? ORDER BY tbl_tag.tag_id ASC;',
            [$recipe_id]);
        $recipe['tag_list'] = array_column($this->_db->getAssocResults(), 'tag_name');

        $this->_db->executePreparedQuery('SELECT 
                tbl_recipe_ingredients.is_alternative as is_alternative,
                tbl_recipe_ingredients.quantity as quantity,
                tbl_unit.unit_name as unit_name, 
                tbl_ingredients.ingredients_name as ingredients_name 
            FROM tbl_recipe_ingredients 
            LEFT JOIN tbl_unit USING (unit_id) 
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

    function save($recipe_id, $public, $category_id, $recipe_name, $persons, $costs, $duration, $total_duration, $original_text, $tag_list, $ingredients_list, $step_list, $del_image, $image_upload) {
        
        $this->_db->begin();
        try{
            if ($recipe_id) {
                $this->_db->executePreparedQuery('UPDATE tbl_recipe SET 
                    public = ?, 
                    category_id = ?, 
                    recipe_name = ?, 
                    persons = ?, 
                    costs = ?, 
                    duration = ?, 
                    total_duration = ?, 
                    original_text = ?,
                    last_edited = NOW(),
                    cnt_update = cnt_update + 1
                WHERE recipe_id = ? AND user_id = ? AND deleted = 0;', 
                [$public, $category_id, $recipe_name, $persons, $costs, $duration, $total_duration, $original_text, (int) $recipe_id, $this->_user_id]);
                if ($this->_db->getAffectedRows() != 1) {
                    $this->_db->rollback();
                    return;
                }

                $this->_clean_recipe_refs($recipe_id);
                $this->_save_tag_list($recipe_id, $tag_list);
                $this->_save_ingredients_list($recipe_id, $ingredients_list);
                $this->_save_step_list($recipe_id, $step_list);

            } else {
                $this->_db->executePreparedQuery('INSERT INTO tbl_recipe SET 
                    public = ?, 
                    category_id = ?, 
                    recipe_name = ?, 
                    persons = ?, 
                    costs = ?, 
                    duration = ?, 
                    total_duration = ?, 
                    original_text = ?,
                    last_edited = NOW(),
                    user_id = ?;', 
                [$public, $category_id, $recipe_name, $persons, $costs, $duration, $total_duration, $original_text, $this->_user_id]);
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

        $this->clean_refs();

        if ($del_image) {
            $this->_db->executePreparedQuery("UPDATE tbl_recipe SET image_name = '', orig_image_name = ''  WHERE recipe_id = ? AND deleted = 0;", 
                [$recipe_id]);
        } elseif($image_upload) {
            $this->_db->executePreparedQuery('UPDATE tbl_recipe SET image_name = ?, orig_image_name = ? WHERE recipe_id = ? AND deleted = 0;', 
                [$image_upload['image_name'], $image_upload['orig_image_name'], $recipe_id]);
        }
        
        $this->_db->commit();
        return $recipe_id;
    }

    protected function _save_tag_list($recipe_id, $tag_list) {
        if (empty($tag_list)) return;

        $tag_list = array_values(array_unique($tag_list));

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

        $ingredients_name_list = array_values(array_unique(array_column($ingredients_list, 'ingredients_name')));
        $unit_name_list = array_unique(array_column($ingredients_list, 'unit_name'));
        function not_empty($val) {
            return !empty($val);
        }
        $unit_name_list = array_values(array_filter($unit_name_list, 'not_empty'));

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
    
    function clean_refs() {
        $this->_db->executeQuery('DELETE FROM tbl_tag 
            WHERE locked = 0 AND tag_id NOT IN (SELECT tag_id FROM tbl_recipe_tag GROUP BY tag_id)');
        $this->_db->executeQuery('DELETE FROM tbl_ingredients 
            WHERE ingredients_id NOT IN (SELECT ingredients_id FROM tbl_recipe_ingredients GROUP BY ingredients_id)');
        $this->_db->executeQuery('DELETE FROM tbl_unit 
            WHERE locked = 0 AND unit_id NOT IN (SELECT unit_id FROM tbl_recipe_ingredients WHERE unit_id IS NOT NULL GROUP BY unit_id)');
    }
}
