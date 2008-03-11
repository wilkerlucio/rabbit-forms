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
 * This classe manage a form
 */
class Rabbit_Form
{
    /**
     * List containg shared assets of form
     *
     * @var array
     */
    protected $assets = array();

    /**
     * Parameters of form
     *
     * @var array
     */
    protected $params = array();

    /**
     * Table that form is managing
     *
     * @var string
     */
    protected $table = '';

    /**
     * Fields of form
     *
     * @var array
     */
    protected $fields = array();

    /**
     * View template of form
     *
     * @var string
     */
    protected $view;

    /**
     * Create a new form
     *
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Add a field to form
     *
     * @param Rabbit_Field $field
     * @return void
     */
    public function addField(Rabbit_Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * Get form fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get specifiq field
     *
     * @param string $name
     */
    public function getField($name)
    {
        foreach($this->fields as $field) {
            if($field->getName() == $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Get open tag of form
     *
     * @return string
     */
    public function getOpenTag()
    {
        return sprintf('<form action="%s" method="post">',
                       $_SERVER['REQUEST_URI']);
    }

    /**
     * Get close tag of form
     *
     * @return string
     */
    public function getCloseTag()
    {
        return '</form>';
    }

    /**
     * Generate data to send into view
     *
     * @return array
     */
    public function generate()
    {
        $data['form_open'] = $this->getOpenTag();

        $data['fields'] = array();

        foreach($this->fields as $field) {
            $data['fields'][] = array(
                'label'      => $field->getLabel(),
                'component'  => $field->getFieldHtml(),
                'validation' => $field->getValidationMessage()
            );
        }

        $data['form_close'] = $this->getCloseTag();

        return $data;
    }

    /**
     * Validate form and fields
     *
     * @return boolean
     */
    public function validate()
    {
        foreach($this->fields as $field) {
            if($field->validate() == false) {
                return false;
            }
        }

        return $this->formValidate();
    }

    /**
     * Validate form at all
     *
     * Extends this method to apply a custom form validation
     *
     * @return boolean
     */
    public function formValidate()
    {
        return true;
    }

    /**
     * Enter description here...
     *
     */
    public function getFieldsData()
    {
        $data = array();

        foreach($this->fields as $field) {
            $data[$field->getName()] = $field->getRawValue();
        }

        return $data;
    }

    /**
     * Save data into database
     *
     * @return void
     */
    public function saveData()
    {
        foreach($this->fields as $field) {
            $field->preInsert();
            $field->preChange();
        }

        $ci = get_instance();
        $ci->load->database();

        $data = $this->getFieldsData();

        $ci->db->insert($this->table, $data);

        foreach($this->fields as $field) {
            $field->postInsert();
            $field->postChange();
        }
    }

    /**
     * Edit data in database
     *
     * @param string $primary_key
     * @param string $id
     * @return void
     */
    public function editData($primary_key, $id)
    {
        foreach($this->fields as $field) {
            $field->preUpdate();
            $field->preChange();
        }

        $ci = get_instance();
        $ci->load->database();

        $data = $this->getFieldsData();

        $ci->db->where($primary_key, $id)->update($this->table, $data);

        foreach($this->fields as $field) {
            $field->postUpdate();
            $field->postChange();
        }
    }
}