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

class Rabbit_Validator_Unique extends Rabbit_Validator
{
    /**
     * @see Rabbit_Validator::validate()
     *
     * @return boolean
     */
    public function validate()
    {
        $ci =& get_instance();
        $ci->lang->load('rabbit');

        $field = $this->field->getName();
        
        $ci->load->database();
        
        $query = $ci->db->query(sprintf(
            "select `%s` from `%s` where `%s` = '%s'",
            $this->field->getForm()->getPrimaryKey(),
            $this->field->getForm()->getTable(),
            $field,
            $this->field->getRawValue()
        ))->result_array();

        if(count($query) > 0 && $query[0][$this->field->getForm()->getPrimaryKey()] != $this->field->getForm()->getEditId()) {
            $this->message = sprintf(
                $ci->lang->line('rabbit_valid_unique'),
                $this->field->getLabel(),
                $this->field->getRawValue()
            );
            
            return false;
        }
        
        return true;
    }
}