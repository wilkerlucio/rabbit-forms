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
		if(gettype($v) == 'array' && (array_keys($v) !== range(0, count($v) - 1))) {
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

/**
 * Encode into JSON format
 *
 * @param mixed $var
 * @return string
 */
function rabbit_json_encode($var)
{
    switch(gettype($var)) {
        case 'boolean':
            return $var ? 'true' : 'false';
        
        case 'NULL':
            return 'null';
        
        case 'integer':
            return (int) $var;
        
        case 'double':
            return (double) $var;
        
        case 'float':
            return (float) $var;
        
        case 'string':
            $var = $var;
            return (string) '"' . addslashes($var) . '"';
        
        case 'array':
            if(is_array($var) && count($var) && (array_keys($var) !== range(0, count($var) - 1))) {
                $propriedades = array_map("rabbit_json_array_map", array_keys($var), array_values($var));
                return '{' . join(',', $propriedades) . '}';
            }
            
            $elementos = array_map("rabbit_json_encode", $var);
            return '[' . join(',', $elementos) . ']';
    }
    
    return null;
}

/**
 * Map JSON data
 *
 * @param string $chave
 * @param string $valor
 * @return string
 */
function rabbit_json_array_map($chave, $valor)
{
    $chave_codificada = rabbit_json_encode(strval($chave));
    $valor_codificado = rabbit_json_encode($valor);
    
    return $chave_codificada . ":" . $valor_codificado;
}

/**
 * Constants for rabbit path info
 */
define('RABBITPATH_DIRNAME', 1);
define('RABBITPATH_BASENAME', 2);
define('RABBITPATH_EXTENSION', 3);
define('RABBITPATH_FILENAME', 4);

function rabbit_pathinfo($path, $options = 0)
{
    $path_parts = preg_split('/(\\|\/)/', $path);
    $basename = array_pop($path_parts);
    $dirname = implode('/', $path_parts);
    
    $name_parts = explode('.', $basename);
    
    if(count($name_parts) == 1) {
        $extension = '';
        $filename = $basename;
    } else {
        $extension = array_pop($name_parts);
        $filename = implode('.', $name_parts);
    }
    
    switch($options) {
        case RABBITPATH_DIRNAME:
            return $dirname;
        case RABBITPATH_BASENAME:
            return $basename;
        case RABBITPATH_EXTENSION:
            return $extension;
        case RABBITPATH_FILENAME:
            return $filename;
        default:
            return array(
                'dirname'   => $dirname,
                'basename'  => $basename,
                'extension' => $extension,
                'filename'  => $filename
            );
    }
}