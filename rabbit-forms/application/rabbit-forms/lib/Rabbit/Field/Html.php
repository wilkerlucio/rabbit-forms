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
 * @version  $Id: TextArea.php 36 2008-03-17 09:06:18Z wilkerlucio $
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Simple text area
 */
class Rabbit_Field_Html extends Rabbit_Field
{
    /**
     * @see Rabbit_Field::loadAssets()
     *
     */
    public function initialize()
    {
        $this->form->addAsset('tiny_mce/tiny_mce.js');

        $options = $this->getAttribute('options', array());

        $options['mode']     = 'exact';
        $options['elements'] = $this->getName();

        $options = json_encode($options);

        $this->form->addClientExec(sprintf('
            tinyMCE.init(%s);
        ', $options));
    }

    /**
     * @see Rabbit_Field::setValue()
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        if(!$this->getAttribute('accept_scripts', false)) {
            $value = preg_replace('@<script[^>]*?>.*?</script>@siu', '', $value);
        }
        
        $this->value = $value;
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

        $attr['name']  = $this->getName();
        $attr['id']    = $this->getName();
        $attr['class'] = $this->getAttribute('class', '');
        $attr['style'] = $this->getAttribute('style', '');

        return sprintf(
            '<textarea %s>%s</textarea>',
            rabbit_attributes_build($attr),
            $this->getValue()
        );
    }
}