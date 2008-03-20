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

class Rabbit_Field_Foreign extends Rabbit_Field
{
    /**
     * Get input name
     *
     * @return string
     */
    public function getCheckName()
    {
        return 'rabbit_foreign_check_' . $this->getName();
    }
    
    /**
     * Get populate data for field
     *
     * @param array $config
     * @return array
     */
    public function getPopulate(array $config)
    {
        if(isset($_POST[$this->getCheckName()])) {
            return $_POST[$this->getCheckName()];
        } elseif($this->form->getEditId()) {
            $ci =& get_instance();
            $ci->load->database();
            
            $id = $this->form->getEditId();
            $type = $this->getAttribute('type', '');
            
            if($type == 'onemany') {
                $query = $ci->db->query(sprintf(
                    "select `%s` from `%s` where `%s` = '%s'",
                    $config['primary_key'],
                    $config['table'],
                    $this->getAttribute('foreign', ''),
                    $id
                ))->result_array();
                
                $data = array();
                
                foreach($query as $row) {
                    $data[] = $row[$config['primary_key']];
                }
                
                return $data;
            } elseif($type == 'manymany') {
                $query = $ci->db->query(sprintf(
                    "select `%s` from `%s` where `%s` = '%s'",
                    $this->getAttribute('foreign', ''),
                    $this->getAttribute('table', ''),
                    $this->getAttribute('local', ''),
                    $id
                ))->result_array();
                
                $data = array();
                
                foreach($query as $row) {
                    $data[] = $row[$this->getAttribute('foreign', '')];
                }
                
                return $data;
            }
            
            show_error('Invalid foreign type ' . $type);
            
            return null;
        } else {
            return array();
        }
    }

    /**
     * @see Rabbit_Field::postChange()
     *
     */
    public function postChange($id)
    {
        $ci =& get_instance();
        $ci->load->database();
        
        $type = $this->getAttribute('type', '');
        $config = $ci->rabbitform->prepare_config($this->getAttribute('config', array()));
        $data = $ci->input->post($this->getCheckName(), array());
        
        if($type == 'onemany') {
            $ci->db->query(sprintf(
                "update `%s` set `%s` = null where `%s` = '%s'",
                $config['table'],
                $this->getAttribute('foreign', ''),
                $this->getAttribute('foreign', ''),
                $id
            ));
            
            $ci->db->query(sprintf(
                "update `%s` set `%s` = '%s' where `%s` in ('%s')",
                $config['table'],
                $this->getAttribute('foreign', ''),
                $id,
                $config['primary_key'],
                implode("','", $data)
            ));
        } elseif($type == 'manymany') {
            $ci->db->query(sprintf(
                "delete from `%s` where `%s` = '%s'",
                $this->getAttribute('table'),
                $this->getAttribute('local'),
                $id
            ));
            
            $insert = array($this->getAttribute('local') => $id);
            
            foreach($data as $foreign) {
                $insert[$this->getAttribute('foreign')] = $foreign;
                $ci->db->insert($this->getAttribute('table'), $insert);
            }
        }
    }

    /**
     * @see Rabbit_Field::preDelete()
     *
     */
    public function postDelete($id)
    {
        $ci =& get_instance();
        $ci->load->database();
        
        $type = $this->getAttribute('type', '');
        $config = $ci->rabbitform->prepare_config($this->getAttribute('config', array()));
        
        if($type == 'onemany') {
            $ci->db->query(sprintf(
                "update `%s` set `%s` = null where `%s` = '%s'",
                $config['table'],
                $this->getAttribute('foreign', ''),
                $this->getAttribute('foreign', ''),
                $id
            ));
        } elseif($type == 'manymany') {
            $ci->db->query(sprintf(
                "delete from `%s` where `%s` = '%s'",
                $this->getAttribute('table'),
                $this->getAttribute('local'),
                $id
            ));
        }
    }
    
    /**
     * @see Rabbit_Field::getFieldHtml()
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $ci =& get_instance();
        $ci->load->library('rabbitform');

        $config = $ci->rabbitform->prepare_config($this->getAttribute('config', array()));
        $retrieve = $ci->rabbitform->prepare_retrieve($config);
        
        $checkedFields = $this->getPopulate($config);
        
        foreach($retrieve['rows'] as $k => $row) {
            $retrieve['rows'][$k]['rabbit_foreign_check'] = sprintf(
                '<input type="checkbox" name="%s[]" value="%s" %s />',
                $this->getCheckName(),
                $row['rabbit_row_id'],
                in_array($row['rabbit_row_id'], $checkedFields) ? 'checked="checked"' : ''
            );
        }
        
        $defaultView = array('template' => 'rabbit-forms/retrieve_foreign');
        
        return $ci->rabbitform->loadView($this->getAttribute('retriveView', $defaultView), $retrieve, true);
    }
}