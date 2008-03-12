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

class Rabbit_Field_CheckBox extends Rabbit_Field
{
    /**
     * @see Rabbit_Field::initialize()
     *
     * @return void
     */
    public function initialize()
    {
        $this->value = 0;
    }

    /**
     * @see Rabbit_Field::setValue()
     *
     * @param unknown_type $value
     */
    public function setValue($value)
    {
        $this->value = $value ? 1 : 0;
    }

    /**
     * @see Rabbit_Field::getFieldHtml()
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $ci =& get_instance();
        $ci->load->helper('rabbit');

        $attr = array();

        $attr['type']  = 'checkbox';
        $attr['name']  = $this->getName();
        $attr['value'] = '1';
        $attr['class'] = $this->getAttribute('class', '');
        $attr['style'] = $this->getAttribute('style', '');
        $attr['id']    = $this->getName();

        if($this->getValue() == 1) {
            $attr['checked'] = 'checked';
        }

        return sprintf(
            '<input %s />',
            rabbit_attributes_build($attr)
        );
    }
}