<?php

/**
 * Rabbit Forms
 *
 * Copyright (c) 2008 Wilker Lúcio
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author   Wilker Lúcio da Silva
 * @version  $Id$
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

abstract class Rabbit_Field_List extends Rabbit_Field
{
    /**
     * @see Rabbit_Field::getDisplayValue()
     *
     * @return string
     */
    public function getDisplayValue()
    {
        $items = $this->getItems();

        foreach($items as $k => $v) {
            if($k == $this->getRawValue()) {
                return $v;
            }
        }

        return $this->getRawValue();
    }

    /**
     * Get options of dropdown
     *
     * @return array
     */
    public function getItems()
    {
        $items      = $this->getAttribute('items', null);
        $controller = $this->getAttribute('controllersource', null);
        $db         = $this->getAttribute('dbsource', null);

        if($items === null) {
            if($db !== null) {
                $table = $db['table'];
                $title = $db['title'];
                $value = $db['value'];

                if(isset($db['filters'])) {
                    $where = 'where ' . implode(' and ', $db['filters']);
                } else {
                    $where = '';
                }

                $sql = "select `{$title}`, `{$value}` from `{$table}` {$where}";

                if(isset($db['orderby'])) {
                    $sql .= ' order by ' . $db['orderby'];
                }

                $ci =& get_instance();
                $ci->load->database();

                $data  = $ci->db->query($sql)->result_array();
                $items = array();

                foreach($data as $row) {
                    $items[$row[$value]] = $row[$title];
                }
            } elseif($controller !== null) {
                //TODO: implement controller source
            } else {
                $items = array();
            }
        }

        return $items;
    }
}