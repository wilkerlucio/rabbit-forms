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
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Form/Factory.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Field.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Field/List.php');
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
    private $serial_counter = 0;

    /**
     * Initialize class
     */
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->config->load('rabbit-forms');
        $this->ci->load->helper('rabbit');
    }

    /**
     * Generate form ID based on config
     *
     * @param array $config
     * @return string
     */
    public function getFormIdentifier(array $config)
    {
        return md5($this->serial_counter . serialize($config));
    }
    
    /**
     * Load configuration YAML file
     *
     * @param string $config
     * @return array
     */
    public function configLoad($config) {
        foreach($this->ci->config->item('rabbit-yml-classpath') as $dir) {
            $path = $dir . $config;

            if(file_exists($path)) {
                $config = Spyc::YAMLLoad($path);
            }
        }
        
        return $config;
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
            $config = $this->configLoad($config);
        }
        
        //build configuration stack
        $configStack = array();
        array_push($configStack, $config);
        $parsing = $config;
        
        while(isset($parsing['extends'])) {
            $c = gettype($parsing['extends']) == 'string' ? 
                 $this->configLoad($parsing['extends']) :
                 $parsing['extends'];
            
            array_push($configStack, $c);
            $parsing = $c;
        }
        
        //merge configurations
        $final = $this->ci->config->item('rabbit-default-settings');
        
        while(count($configStack) > 0) {
            $final = rabbit_array_merge($final, array_pop($configStack));
        }

        //return data
        return $final;
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
            $fields = rabbit_filter_fields(
                $config['table'],
                array_keys($config['fields'])
            );

            $fields = implode(',', $fields);

            $this->ci->load->database();

            $edit = $this->ci->db->query(sprintf(
                "select %s from %s where %s = '%s'",
                $fields,
                $config['table'],
                $config['primary_key'],
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
    public function prepare_form(array $config, array $defaults = array(), $id = '')
    {
    	//create form
        $form = Rabbit_Form_Factory::factory($config['form']['type'], $config['table']);
        $form->setGenerateAssets($config['form']['automatic_assets']);
        $form->setPrimaryKey($config['primary_key']);
        $form->setEditId($id);
        
        //form validator
        if(isset($config['form']['validation'])) {
            $form->setValidationCallback($config['form']['validation']);
        }
        
        //form events
        if(isset($config['form']['preinsert'])) {
            $form->setPreInsert($config['form']['preinsert']);
        }
        if(isset($config['form']['postinsert'])) {
            $form->setPostInsert($config['form']['postinsert']);
        }
        if(isset($config['form']['preupdate'])) {
            $form->setPreUpdate($config['form']['preupdate']);
        }
        if(isset($config['form']['postupdate'])) {
            $form->setPostUpdate($config['form']['postupdate']);
        }
        if(isset($config['form']['prechange'])) {
            $form->setPreChange($config['form']['prechange']);
        }
        if(isset($config['form']['postchange'])) {
            $form->setPostChange($config['form']['postchange']);
        }
        if(isset($config['form']['predelete'])) {
            $form->setPreDelete($config['form']['predelete']);
        }
        if(isset($config['form']['postdelete'])) {
            $form->setPostDelete($config['form']['postdelete']);
        }

        //parse hiddens
        if(isset($config['form']['hidden'])) {
            foreach($config['form']['hidden'] as $name => $value) {
                $form->addHiddenField($name, $value);
            }
        }

        //add hidden form indentifier
        $form->addHiddenField('rabbit-form-id', $this->getFormIdentifier($config));

        //parse fields
        foreach($config['fields'] as $name => $field) {
            //get params
            $params = isset($field['params']) ? $field['params'] : array();

            //initialize field
            $f = Rabbit_Field_Factory::factory($field['type'], $form, $params);
            $f->setName($name);
            $f->setLabel($field['label']);

            //set persist
            if(isset($field['persist'])) {
                $f->setPersist($field['persist']);
            }

            //populate field
            if(isset($_POST[$name])) {           //check for post repopulate
                $f->setValue($_POST[$name]);
            } elseif(isset($defaults[$name])) {  //check for predefined repopulate
                $f->setRawValue($defaults[$name]);
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

            //initialize field
            $f->initialize();
        }

        return $form;
    }

    /**
     * Prepare retrive data
     * 
     * This method generate retrive data based on config data
     *
     * @param array $config
     * @return array
     */
    public function prepare_retrieve(array $config, $page = 0)
    {
        //get code igniter
        $this->ci->load->database();
        $this->ci->load->helper('url');
        
        //initialize data
        $data = array();
        
        //base data
        $data['manage']  = $config['retrieve']['manage'];
        $data['delete']  = $config['retrieve']['delete'];
        $data['kfields'] = $config['retrieve']['fields'];
        
        //order
        $orderby = $config['retrieve']['orderby'];
        
        if($orderby) {
            $orderby = sprintf('order by `%s`', $orderby);
        }
        
        //filters
        if(isset($config['retrieve']['filters'])) {
            $filters = 'where ' . implode(' and ', $config['retrieve']['filters']);
        } else {
            $filters = '';
        }
        
        //pagination
        if(isset($config['retrieve']['pagination']) && isset($config['retrieve']['pagination']['base_url'])) {
            $this->ci->load->library('pagination');
            
            $ptotal = $this->ci->db->query(sprintf(
                'select count(*) as total from `%s` %s',
                $config['table'],
                $filters
            ))->row_array();
            
            if(!isset($config['retrieve']['pagination']['per_page'])) {
                show_error("Rabbit forms: you need to configure per_page of pagination");
            }
            
            $config['retrieve']['pagination']['base_url'] = site_url($config['retrieve']['pagination']['base_url']);
            
            $config['retrieve']['pagination']['total_rows'] = $ptotal['total'];
            
            $this->ci->pagination->initialize($config['retrieve']['pagination']);
            
            $data['pagination'] = $this->ci->pagination->create_links();
            
            $pagination = sprintf(
                'limit %s, %s',
                $page,
                $config['retrieve']['pagination']['per_page']
            );
        } else {
            $pagination = '';
        }

        //create form
        $form = new Rabbit_Form($config['table']);
        $form->setGenerateAssets($config['form']['automatic_assets']);
        $form->setPrimaryKey($config['primary_key']);

        //load field headers
        $fields = $config['retrieve']['fields'];
        $data['fields']  = array();

        foreach($fields as $field) {
            $data['fields'][$field] = $config['fields'][$field]['label'];
        }

        //load fields skeleton
        $skeletons = array();

        foreach($fields as $field) {
            $skeletons[$field] = Rabbit_Field_Factory::factory(
                $config['fields'][$field]['type'],
                $form,
                isset($config['fields'][$field]['params']) ? $config['fields'][$field]['params'] : array());
            $skeletons[$field]->initialize();
        }

        //load rows of data
        $data['rows'] = array();

        $rows = $this->ci->db->query(sprintf(
            'select `%s`, `%s` from `%s` %s %s %s',
            $config['primary_key'],
            implode('`,`', $fields),
            $config['table'],
            $filters,
            $orderby,
            $pagination
        ))->result_array();

        foreach($rows as $row) {
            $form->setEditId($row[$config['primary_key']]);
            $line = array();
            $line['rabbit_row_id'] = $row[$config['primary_key']];

            foreach($skeletons as $field => $skeleton) {
                $skeleton->setRawValue($row[$field]);
                $line[$field] = $skeleton->getDisplayValue();
            }

            $data['rows'][] = $line;
        }
        
        return $data;
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
        //increment serial (avoid form)
        $this->serial_counter += 1;

        //load config
        $config = $this->prepare_config($config);

        //load edit data
        $edit = $this->prepare_edit($config, $id);

        //prepare form
        $form = $this->prepare_form($config, $edit, $id);

        $check  = $this->ci->input->post('rabbit-form-id') == $this->getFormIdentifier($config);

        //if post, try to validate, if validated, send data
        if($check && $form->validate()) {
            if($id === false) {
                $form->saveData();
            } else {
                $form->editData($id);
            }

            return '';
        } else {
            $data = $form->generate();

            return $this->loadView($config['form']['view'], $data);
        }
    }

    /**
     * Get a list o data from table
     *
     * @param mixed $config
     * @return string
     */
    public function retrieve($config, $page = 0)
    {
        $config = $this->prepare_config($config);
        $data = $this->prepare_retrieve($config, $page);

        //return data
        return $this->loadView($config['retrieve']['view'], $data);
    }

    /**
     * Delete item from database
     *
     * @param unknown_type $config
     * @param unknown_type $id
     */
    public function delete($config, $id)
    {
        //load config
        $config = $this->prepare_config($config);

        //load edit data
        $edit = $this->prepare_edit($config, $id);

        //prepare form
        $form = $this->prepare_form($config, $edit);

        //delete action
        $form->deleteData($id);
    }

    /**
     * Load view and retrieve result html
     *
     * @param array $config config of view
     * @param array $data data to display
     * @return string
     */
    public function loadView($config, $data)
    {
        $data['params'] = new Rabbit_Container();

        if(isset($config['params'])) {
            $data['params']->setData($config['params']);
        }

        return $this->ci->load->view(
            $config['template'],
            $data,
            true
        );
    }
}