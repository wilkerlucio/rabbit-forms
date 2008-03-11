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

require_once(APPPATH . 'rabbit-forms/spyc.php');
require_once(APPPATH . 'rabbit-forms/Rabbit/Form.php');
require_once(APPPATH . 'rabbit-forms/Rabbit/Field.php');
require_once(APPPATH . 'rabbit-forms/Rabbit/Field/Factory.php');
require_once(APPPATH . 'rabbit-forms/Rabbit/Validator.php');
require_once(APPPATH . 'rabbit-forms/Rabbit/Validator/Factory.php');

class Rabbitform
{
    /**
     * Code Igniter reference
     *
     * @var unknow
     */
    private $ci;

    public function __construct()
    {
        $this->ci = get_instance();
        $this->ci->config->load('rabbit-forms');
    }

    /**
     * Fastest way to create a form
     *
     * @param mixed $config Config array or path
     * @param mixed $id Id to edit
     * @return string
     */
    public function run($config, $id = false)
    {
        if(gettype($config) == 'string') {
            foreach($this->ci->config->item('rabbit-yml-classpath') as $dir) {
                $path = $dir . $config;

                if(file_exists($path)) {
                    $config = Spyc::YAMLLoad($path);
                }
            }
        }

        $edit = array();

        if($id !== false && count($_POST) == 0) {
            $fields = implode(',', array_keys($config['form']['fields']));

            $this->ci->load->database();

            $edit = $this->ci->db->query(sprintf(
                "select %s from %s where %s = '%s'",
                $fields,
                $config['form']['table'],
                $config['form']['primary_key'],
                $id
            ))->row_array();
        }

        $form = new Rabbit_Form($config['form']['table']);

        foreach($config['form']['fields'] as $name => $field) {
            $f = Rabbit_Field_Factory::factory($field['type'], $form);
            $f->setName($name);
            $f->setLabel($field['label']);

            //set params
            if(isset($field['params'])) {
                $f->setAttributes($field['params']);
            }

            //populate field
            if(isset($_POST[$name])) {
                $f->setValue($_POST[$name]);
            } elseif(isset($field['value'])) {
                $f->setValue($field['value']);
            } elseif(isset($edit[$name])) {
                $f->setValue($edit[$name]);
            }

            //validators
            if(isset($field['validators'])) {
                foreach($field['validators'] as $validator) {
                    $v = Rabbit_Validator_Factory::factory($validator['type'], $f);

                    if(isset($validator['params'])) {
                        $v->setParams($validator['params']);
                    }
                }
            }
        }

        if(count($_POST) > 0 && $form->validate()) {
            if($id === false) {
                $form->saveData();
            } else {
                $form->editData($config['form']['primary_key'], $id);
            }

            $this->ci->load->helper('url');

            redirect($config['redirect']);

            return '';
        } else {
            $data = $form->generate();
            $data['view_params'] = $config['view']['params'];

            return $this->ci->load->view(
            	'rabbit-forms/view_linear.php',
                $data,
                true
            );
        }
    }
}