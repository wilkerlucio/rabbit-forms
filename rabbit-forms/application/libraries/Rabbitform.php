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

/*
 * Load base Rabbit libs
 */
require_once(APPPATH . 'rabbit-forms/lib/spyc.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Container.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Form.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Field.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Field/Factory.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Validator.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Validator/Factory.php');

/**
 * This class provides fast methods to generate forms
 */
class Rabbitform
{
    /**
     * Code Igniter reference
     *
     * @var unknow
     */
    private $ci;

    /**
     * Initialize class
     */
    public function __construct()
    {
        $this->ci = get_instance();
        $this->ci->config->load('rabbit-forms');
    }
    
    /**
     * Prepare config data
     *
     * @param mixed $config
     * @return array
     */
    public function prepare_config($config)
    {
    	//load helper
        $this->ci->load->helper('rabbit');
        
        //check for YML config
        if(gettype($config) == 'string') {
            foreach($this->ci->config->item('rabbit-yml-classpath') as $dir) {
                $path = $dir . $config;

                if(file_exists($path)) {
                    $config = Spyc::YAMLLoad($path);
                }
            }
        }

        //merge with default
        $config = rabbit_array_merge(
            $this->ci->config->item('rabbit-default-settings'),
            $config
        );
        
        //return data
        return $config;
    }

    /**
     * Prepare edit predefined data
     *
     * @param array $config
     * @param string $id
     * @return array
     */
    public function prepare_edit(array $config, $id)
    {
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
        
        return $edit;
    }
    
    /**
     * Prepare form
     *
     * @param array $config
     * @param array $defaults
     * @return Rabbit_Form
     */
    public function prepare_form(array $config, array $defaults = array())
    {
    	//create form
        $form = new Rabbit_Form($config['form']['table']);

        //parse fields
        foreach($config['form']['fields'] as $name => $field) {
            $f = Rabbit_Field_Factory::factory($field['type'], $form);
            $f->setName($name);
            $f->setLabel($field['label']);

            //set params
            if(isset($field['params'])) {
                $f->setAttributes($field['params']);
            }

            //populate field
            if(isset($_POST[$name])) {           //check for post repopulate
                $f->setValue($_POST[$name]);
            } elseif(isset($defaults[$name])) {  //check for predefined repopulate
                $f->setValue($defaults[$name]);
            } elseif(isset($field['value'])) {   //check for config repopulate
                $f->setValue($field['value']);
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
        
        return $form;
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
        $config = $this->prepare_config($config);
        $edit   = $this->prepare_edit($config, $id);
        $form   = $this->prepare_form($config, $edit);

        //if post, try to validate, if validated, send data
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
            $data['params'] = new Rabbit_Container();
            
            if(isset($config['view']['params'])) {
                $data['params']->setData($config['view']['params']);
            }

            return $this->ci->load->view(
            	$config['view']['template'],
                $data,
                true
            );
        }
    }
}