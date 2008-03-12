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
 * Provides a drop-down form field
 *
 * Use "items" attribute to set options
 */
class Rabbit_Field_DropDown extends Rabbit_Field_List
{
    /**
     * @see Rabbit_Field::loadAssets()
     *
     */
    public function loadAssets()
    {
        if($this->getAttribute('updateField') !== null) {
            $this->form->addAsset('jquery-1.2.3.pack.js');
            $this->form->addAsset('dropdown-forward.js');
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
        $ci->load->helper('rabbit');

        $attr['id']   = $this->getName();
        $attr['name'] = $this->getName();
        $attr['class'] = $this->getAttribute('class', '');
        $attr['style'] = $this->getAttribute('style', '');

        if($this->getAttribute('updateField') !== null) {
            $update = $this->getAttribute('updateField');

            $attr['onchange'] = sprintf(
                "forwardSelect('%s', '%s', '%s')",
                $update['url'],
                $this->getName(),
                $update['target']
            );
        }

        $html = sprintf(
            '<select %s>',
            rabbit_attributes_build($attr)
        );

        foreach($this->getItems() as $value => $name) {
            $selected = $this->getValue() == $value ? ' selected="selected"' : '';

            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                $value,
                $selected,
                $name
            );
        }

        $html .= '</select>';

        return $html;
    }
}