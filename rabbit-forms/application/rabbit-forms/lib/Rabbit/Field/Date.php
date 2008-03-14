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

class Rabbit_Field_Date extends Rabbit_Field
{
    /**
     * @see Rabbit_Field::loadAssets()
     *
     */
    public function initialize()
    {
        $this->form->addAsset('jquery-1.2.3.pack.js');
        $this->form->addAsset('ui.datepicker.js');
        $this->form->addAsset('ui.datepicker.css');

        $lang = $this->getAttribute('lang', '');

        if($lang) {
            $this->form->addAsset('ui.datepicker-' . $lang . '.js');
        }

        $options = $this->getAttribute('options', array());
        $options = json_encode($options);

        $this->form->addClientExec('
            $(document).ready(function() {
                $("#' . $this->getName() . '").datepicker(' . $options . ');
            });
        ');
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

        $attr['type']  = 'text';
        $attr['id']    = $this->getName();
        $attr['name']  = $this->getName();
        $attr['value'] = $this->getValue();
        $attr['class'] = $this->getAttribute('class', '');
        $attr['style'] = $this->getAttribute('style', '');

        return sprintf(
            '<input %s />',
            rabbit_attributes_build($attr)
        );
    }
}