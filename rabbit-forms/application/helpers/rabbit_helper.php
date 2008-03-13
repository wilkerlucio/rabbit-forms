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

/**
 * Fuse array content
 *
 * @param array $array1
 * @param array $array2
 * @return void
 */
function rabbit_array_merge(array $array1, array $array2)
{
	foreach($array2 as $k => $v) {
		if(gettype($v) == 'array') {
			if(!isset($array1[$k]) || gettype($array1[$k]) != 'array') {
				$array1[$k] = array();
			}

			$array1[$k] = rabbit_array_merge($array1[$k], $v);

			continue;
		}

		$array1[$k] = $v;
	}

	return $array1;
}

/**
 * Filter field list of table
 *
 * @param string $table
 * @param array $data
 * @return array
 */
function rabbit_filter_fields($table, array $data) {
    $ci =& get_instance();
    $ci->load->database();

    $out = array();
    $fields = $ci->db->list_fields($table);

    foreach($data as $field) {
        if(in_array($field, $fields)) {
            $out[] = $field;
        }
    }

    return $out;
}

/**
 * Filter array of data to save, letting only the valid fields stay
 *
 * @param string $table
 * @param array $data
 * @return array
 */
function rabbit_filter_db_data($table, $data)
{
    $ci =& get_instance();

    $out = array();
    $fields = $ci->db->list_fields($table);

    foreach($fields as $field)
        if(isset($data[$field]))
            $out[$field] = $data[$field];

    return $out;
}

/**
 * Build attributes string based on array data
 *
 * @param array $data
 * @return string
 */
function rabbit_attributes_build(array $data)
{
    $attributes = array();

    foreach($data as $k => $v) {
        $attributes[] = $k . '="' . $v . '"';
    }

    return implode(" ", $attributes);
}