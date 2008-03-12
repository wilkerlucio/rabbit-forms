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

class Rabbit_Field_CheckBoxGroup extends Rabbit_Field_List
{
    /**
     * @see Rabbit_Field::initialize()
     *
     * @return void
     */
    public function initialize()
    {
        $this->setValue(array());
    }

    /**
     * @see Rabbit_Field::getValue()
     *
     * @return string
     */
    public function getValue()
    {
        return unserialize($this->value);
    }

    /**
     * @see Rabbit_Field::setValue()
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = serialize($value);
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

        $attr['name'] = $this->getName() . '[]';
        $attr['type'] = 'checkbox';
        $attr['class'] = $this->getAttribute('class', '');
        $attr['style'] = $this->getAttribute('style', '');

        $html = '';
        $cvalue = $this->getValue();

        foreach($this->getItems() as $value => $name) {
            $selected = in_array($value, $cvalue) ? 'checked="checked"' : '';
            $attr['value'] = $value;

            $html .= sprintf(
                '<input %s %s /> %s<br />',
                rabbit_attributes_build($attr),
                $selected,
                $name
            );
        }

        return $html;
    }
}