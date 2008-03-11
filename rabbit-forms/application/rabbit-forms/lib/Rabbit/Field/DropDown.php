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
class Rabbit_Field_DropDown extends Rabbit_Field
{
    /**
     * Get options of dropdown
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getAttribute('items', array());
    }

    /**
     * @see Rabbit_Field::getFieldHtml()
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $html = sprintf(
            '<select name="%s">',
            $this->getName()
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