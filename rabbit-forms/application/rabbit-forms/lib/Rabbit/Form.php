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
     * Set false to not generate form assets
     *
     * @var boolean
     */
    protected $generateAssets = true;

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
     * Table primary key field
     *
     * @var string
     */
    protected $primaryKey = '';
    
    /**
     * This field mantains ID of editing row if form is editing
     *
     * @var string
     */
    protected $editId = '';
    
    /**
     * The name of callback function to validate form
     *
     * @var string
     */
    protected $validationCallback = '';
    
    /**
     * The validation message of form
     *
     * @var string
     */
    protected $validationMessage = '';

    /**
     * Fields of form
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Client JS post form executions
     *
     * @var array
     */
    protected $clientExec = array();

    /**
     * Hidden fields of form
     *
     * @var array
     */
    protected $hiddenFields = array();

    /**
     * The name of callback function to pre insert event
     *
     * @var string
     */
    protected $preInsert = '';

    /**
     * The name of callback function to post insert event
     *
     * @var string
     */
    protected $postInsert = '';

    /**
     * The name of callback function to pre update event
     *
     * @var string
     */
    protected $preUpdate = '';

    /**
     * The name of callback function to post update event
     *
     * @var string
     */
    protected $postUpdate = '';

    /**
     * The name of callback function to pre change event
     *
     * @var string
     */
    protected $preChange = '';

    /**
     * The name of callback function to post change event
     *
     * @var string
     */
    protected $postChange = '';

    /**
     * The name of callback function to pre change event
     *
     * @var string
     */
    protected $preDelete = '';

    /**
     * The name of callback function to post change event
     *
     * @var string
     */
    protected $postDelete = '';
    
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
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param string $primary_key
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return string
     */
    public function getEditId()
    {
        return $this->editId;
    }

    /**
     * @param string $editId
     */
    public function setEditId($editId)
    {
        $this->editId = $editId;
    }

    /**
     * @return string
     */
    public function getValidationCallback()
    {
        return $this->validationCallback;
    }

    /**
     * @param string $validationCallback
     */
    public function setValidationCallback($validationCallback)
    {
        $this->validationCallback = $validationCallback;
    }

    /**
     * @return string
     */
    public function getValidationMessage()
    {
        return $this->validationMessage;
    }

    /**
     * @param string $validationMessage
     */
    public function setValidationMessage($validationMessage)
    {
        $this->validationMessage = $validationMessage;
    }

    /**
     * @return string
     */
    public function getPostChange()
    {
        return $this->postChange;
    }

    /**
     * @param string $postChange
     */
    public function setPostChange($postChange)
    {
        $this->postChange = $postChange;
    }

    /**
     * @return string
     */
    public function getPostDelete()
    {
        return $this->postDelete;
    }

    /**
     * @param string $postDelete
     */
    public function setPostDelete($postDelete)
    {
        $this->postDelete = $postDelete;
    }

    /**
     * @return string
     */
    public function getPostInsert()
    {
        return $this->postInsert;
    }

    /**
     * @param string $postInsert
     */
    public function setPostInsert($postInsert)
    {
        $this->postInsert = $postInsert;
    }

    /**
     * @return string
     */
    public function getPostUpdate()
    {
        return $this->postUpdate;
    }

    /**
     * @param string $postUpdate
     */
    public function setPostUpdate($postUpdate)
    {
        $this->postUpdate = $postUpdate;
    }

    /**
     * @return string
     */
    public function getPreChange()
    {
        return $this->preChange;
    }

    /**
     * @param string $preChange
     */
    public function setPreChange($preChange)
    {
        $this->preChange = $preChange;
    }

    /**
     * @return string
     */
    public function getPreDelete()
    {
        return $this->preDelete;
    }

    /**
     * @param string $preDelete
     */
    public function setPreDelete($preDelete)
    {
        $this->preDelete = $preDelete;
    }

    /**
     * @return string
     */
    public function getPreInsert()
    {
        return $this->preInsert;
    }

    /**
     * @param string $preInsert
     */
    public function setPreInsert($preInsert)
    {
        $this->preInsert = $preInsert;
    }

    /**
     * @return string
     */
    public function getPreUpdate()
    {
        return $this->preUpdate;
    }

    /**
     * @param string $preUpdate
     */
    public function setPreUpdate($preUpdate)
    {
        $this->preUpdate = $preUpdate;
    }
    
    /**
     * @return boolean
     */
    public function getGenerateAssets()
    {
        return $this->generateAssets;
    }

    /**
     * @param boolean $generateAssets
     */
    public function setGenerateAssets($generateAssets)
    {
        $this->generateAssets = $generateAssets;
    }

    /**
     * Add hidden field to form
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addHiddenField($name, $value)
    {
        $this->hiddenFields[] = array('name' => $name, 'value' => $value);
    }

    /**
     * Remove hidden field from form
     *
     * @param string $name
     * @return void
     */
    public function removeHiddenField($name)
    {
        $hiddens = array();

        foreach($this->hiddenFields as $hidden) {
            if($hidden['name'] != $name) {
                $hiddens[] = $hidden;
            }
        }

        $this->hiddenFields = $hiddens;
    }

    /**
     * Get a especifique hidden field
     *
     * @param string $name
     * @return array | null
     */
    public function getHiddenField($name)
    {
        foreach($this->hiddenFields as $hidden) {
            if($hidden['name'] == $name) {
                return $hidden;
            }
        }

        return null;
    }

    /**
     * Get form hidden fields
     *
     * @return array
     */
    public function getHiddenFields()
    {
        return $this->hiddenFields;
    }

    /**
     * Get hidden fields form string
     *
     * @return string
     */
    public function generateHidden()
    {
        $output = '';
        $pattern = '<input type="hidden" name="%s" value="%s" />';

        foreach($this->getHiddenFields() as $hidden) {
            $output .= sprintf(
                $pattern,
                $hidden['name'],
                $hidden['value']
            );

            $output .= "\n";
        }

        return $output;
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
	 * @return Rabbit_Field
     */
    public function getField($name)
    {
        foreach($this->getFields() as $field) {
            if($field->getName() == $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Add form asset
     *
     * @param string $path
     * @return void
     */
    public function addAsset($path)
    {
        if(!in_array($path, $this->assets)) {
            $this->assets[] = $path;
        }
    }

    /**
     * Add client code to run after form
     *
     * @param string $code
     * @return void
     */
    public function addClientExec($code)
    {
        $this->clientExec[] = $code;
    }

    /**
     * Generate output for resources
     *
     * @return string
     */
    public function getAssetsOutput()
    {
        if(!$this->getGenerateAssets()) {
            return '';
        }

        $ci =& get_instance();
        $ci->load->helper('url');

        $patterns = array(
            'js'  => '<script type="text/javascript" src="%s"></script>',
            'css' => '<link rel="stylesheet" type="text/css" href="%s" />'
        );

        $output = "";

        foreach($this->assets as $asset) {
            $url  = base_url() . $ci->config->item('rabbit-assets') . $asset;
            $info = pathinfo($asset);
            $ext  = strtolower($info['extension']);

            $output .= sprintf($patterns[$ext], $url) . "\n";
        }

        return $output;
    }

    /**
     * Get open tag of form
     *
     * @return string
     */
    public function getOpenTag()
    {
        return sprintf('<form action="%s" method="post" enctype="multipart/form-data">' . "\n",
                       $_SERVER['REQUEST_URI']);
    }

    /**
     * This method load post execute form javascripts
     *
     * @return string
     */
    public function getPostExec()
    {
        $output = '';

        if(count($this->clientExec) > 0) {
            $output = '<script type="text/javascript">' . "\n"
                    . implode("\n", $this->clientExec)
                    . "\n</script>";
        }

        return $output;
    }

    /**
     * Get close tag of form
     *
     * @return string
     */
    public function getCloseTag()
    {
        return "</form>\n";
    }

    /**
     * Generate data to send into view
     *
     * @return array
     */
    public function generate()
    {
        $data['form_open']       = $this->getOpenTag();
        $data['fields']          = array();
        $data['form_validation'] = $this->getValidationMessage();

        foreach($this->fields as $field) {
            $data['fields'][$field->getName()] = array(
                'label'      => $field->getLabel(),
                'component'  => $field->getFieldHtml(),
                'validation' => $field->getValidationMessage()
            );
        }

        $data['form_close']  = $this->getCloseTag();
        $data['form_hidden'] = $this->generateHidden();
        $data['form_assets'] = $this->getAssetsOutput();
        $data['form_exec']   = $this->getPostExec();

        return $data;
    }

    /**
     * Validate form and fields
     *
     * @return boolean
     */
    public function validate()
    {
        $return = true;

        foreach($this->fields as $field) {
            if($field->validate() == false) {
                $return = false;
            }
        }

        return $return && $this->formValidate();
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
        $ci =& get_instance();
        $method = $this->validationCallback;
        
        if(method_exists($ci, $method)) {
            return $ci->$method($this);
        }
        
        return true;
    }

    /**
     * Get array containg data of fields
     *
     * @return array
     */
    public function getFieldsData()
    {
        $ci =& get_instance();
        $ci->load->helper('rabbit');

        $data = array();

        foreach($this->fields as $field) {
            if($field->getPersist() == true) {
                $data[$field->getName()] = $field->getRawValue();
            }
        }

        foreach($this->getHiddenFields() as $hidden) {
            $data[$hidden['name']] = $hidden['value'];
        }

        return rabbit_filter_db_data($this->table, $data);
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
        
        $this->preInsert();
        $this->preChange();

        $ci =& get_instance();
        $ci->load->database();

        $data = $this->getFieldsData();

        $ci->db->insert($this->table, $data);
        $id = $ci->db->insert_id();

        foreach($this->fields as $field) {
            $field->postInsert($id);
            $field->postChange($id);
        }
        
        $this->postInsert($id);
        $this->postChange($id);
    }

    /**
     * Edit data in database
     *
     * @param string $id
     * @return void
     */
    public function editData($id)
    {
        foreach($this->fields as $field) {
            $field->preUpdate($id);
            $field->preChange($id);
        }
        
        $this->preUpdate($id);
        $this->preChange($id);

        $ci =& get_instance();
        $ci->load->database();

        $data = $this->getFieldsData();

        $ci->db->where($this->getPrimaryKey(), $id)->update($this->table, $data);

        foreach($this->fields as $field) {
            $field->postUpdate($id);
            $field->postChange($id);
        }
        
        $this->postUpdate($id);
        $this->postChange($id);
    }

    /**
     * Delete a data from db
     *
     * @param string $id
     * @return void
     */
    public function deleteData($id)
    {
        foreach($this->fields as $field) {
            $field->preDelete($id);
        }
        
        $this->preDelete($id);

        $ci =& get_instance();
        $ci->load->database();

        $ci->db->query(sprintf(
            "delete from `%s` where `%s` = '%s'",
            $this->getTable(),
            $this->getPrimaryKey(),
            $id
        ));

        foreach($this->fields as $field) {
            $field->postDelete($id);
        }
        
        $this->postDelete($id);
    }
    
    /*
     * Events
     */

    protected function dispatchEvent($method, $id = null)
    {
        $ci =& get_instance();
        
        if(method_exists($ci, $method)) {
            $ci->$method($this, $id);
        }
    }
    
    /**
     * Event dispatched before insert
     *
     * @return void
     */
    public function preInsert()
    {
        $this->dispatchEvent($this->getPreInsert());
    }

    /**
     * Event dispatched after insert
     *
     * @param string $id
     * @return void
     */
    public function postInsert($id)
    {
        $this->dispatchEvent($this->getPostInsert(), $id);
    }

    /**
     * Event dispatched before update
     *
     * @param string $id
     * @return void
     */
    public function preUpdate($id)
    {
        $this->dispatchEvent($this->getPreUpdate(), $id);
    }

    /**
     * Event dispatched after update
     *
     * @param string $id
     * @return void
     */
    public function postUpdate($id)
    {
        $this->dispatchEvent($this->getPostUpdate(), $id);
    }

    /**
     * Event dispatched before record change (shortcut for insert and
     * update together)
     *
     * @return void
     */
    public function preChange($id = null)
    {
        $this->dispatchEvent($this->getPreChange(), $id);
    }

    /**
     * Event dispatched after record change (shortcut for insert and
     * update together)
     *
     * @param string $id
     * @return void
     */
    public function postChange($id)
    {
        $this->dispatchEvent($this->getPostChange(), $id);
    }

    /**
     * Event dispatched before delete
     *
     * @param string $id
     * @return void
     */
    public function preDelete($id)
    {
        $this->dispatchEvent($this->getPreDelete(), $id);
    }

    /**
     * Event dispatched after delete
     *
     * @param string $id
     * @return void
     */
    public function postDelete($id)
    {
        $this->dispatchEvent($this->getPostDelete(), $id);
    }
}