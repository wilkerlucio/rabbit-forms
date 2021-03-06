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

class Rabbit_Form_Factory
{
    /**
     * Create a new field based on name
     *
     * @param string $field
     * @param string $form
     * @return Rabbit_Form
     */
    public static function factory($form, $table)
    {
        $classname = $form ? 'Rabbit_Form_' . $form : 'Rabbit_Form';

        if(!class_exists($classname)) {
            $ci = get_instance();
            $ci->config->load('rabbit-forms');

            foreach($ci->config->item('rabbit-form-classpath') as $dir) {
                $path = $dir . $form . '.php';

                if(file_exists($path)) {
                    require_once($path);
                    break;
                }
            }
        }

        return class_exists($classname) ? new $classname($table) : null;
    }
}