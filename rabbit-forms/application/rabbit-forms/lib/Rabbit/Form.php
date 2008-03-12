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
     * Create a new form
     *
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
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
        return sprintf('<form action="%s" method="post">' . "\n",
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
        $data['form_open']   = $this->getOpenTag();
        $data['fields']      = array();

        foreach($this->fields as $field) {
            $field->loadAssets();
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

        foreach($this->getHiddenFields() as $hidden) {
            $data[$hidden['name']] = $hidden['value'];
        }

        foreach($this->fields as $field) {
            if($field->getPersist() == true) {
                $data[$field->getName()] = $field->getRawValue();
            }
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

        $ci =& get_instance();
        $ci->load->database();

        $data = $this->getFieldsData();

        $ci->db->insert($this->table, $data);
        $id = $ci->db->insert_id();

        foreach($this->fields as $field) {
            $field->postInsert($id);
            $field->postChange($id);
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
            $field->preUpdate($id);
            $field->preChange($id);
        }

        $ci =& get_instance();
        $ci->load->database();

        $data = $this->getFieldsData();

        $ci->db->where($primary_key, $id)->update($this->table, $data);

        foreach($this->fields as $field) {
            $field->postUpdate($id);
            $field->postChange($id);
        }
    }
}