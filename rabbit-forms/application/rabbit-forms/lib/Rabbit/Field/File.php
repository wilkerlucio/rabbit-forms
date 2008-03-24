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

class Rabbit_Field_File extends Rabbit_Field
{
    /**
     * Remove stored file from file system
     *
     * @param string $id
     */
    public function removeFile($id)
    {
        $ci =& get_instance();
        $ci->load->database();

        $data = $ci->db->query(sprintf(
            "select %s from %s where %s = '%s'",
            $this->getName(),
            $this->getForm()->getTable(),
            $this->getForm()->getPrimaryKey(),
            $id
        ))->row_array();

        $path = $this->baseFilePath() . $id . '_' . $data[$this->getName()];

        if(file_exists($path)) {
            unlink($path);
        }
    }
    
    /**
     * Return path of folder that contains files
     *
     * @return string
     */
    protected function baseFilePath()
    {
        $ci =& get_instance();

        return   $ci->config->item('rabbit-upload-path')
               . $this->form->getTable()
               . '/';
    }
    
    protected function reachFieldPath()
    {
        $ci =& get_instance();

        return   $ci->config->item('rabbit-upload')
               . $this->form->getTable()
               . '/';
    }

    /**
     * Detect if a file of field is uploaded or not
     *
     * @return boolean
     */
    protected function hasUpload()
    {
        $file = $_FILES[$this->getName()];

        if($file['tmp_name']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @see Rabbit_Field::preUpdate()
     *
     * If a new upload is sent on edit, remove the previous (if exists)
     *
     * @param string $id
     */
    public function preUpdate($id)
    {
        if($this->hasUpload()) {
            $this->removeFile($id);
        } else {
            $ci =& get_instance();
            $ci->load->database();
        
            $data = $ci->db->query(sprintf(
                "select %s from %s where %s = '%s'",
                $this->getName(),
                $this->getForm()->getTable(),
                $this->getForm()->getPrimaryKey(),
                $id
            ))->row_array();
        
            $this->getForm()->addHiddenField($this->getName(), $data[$this->getName()]);
        }
    }

    /**
     * @see Rabbit_Field::preChange()
     *
     * Add field name to save into db
     */
    public function preChange()
    {
        if($this->hasUpload()) {
            $this->getForm()->addHiddenField($this->getName(), $_FILES[$this->getName()]['name']);
        }
    }

    /**
     * @see Rabbit_Field::postChange()
     *
     * Do file upload
     */
    public function postChange($id)
    {
        //check for file sent
        if($this->hasUpload()) {
            $this->getForm()->removeHiddenField($this->getName());

            $file = $_FILES[$this->getName()];

            //add new file
            $path = $this->baseFilePath();

            //make dir if not exists
            if(!is_dir($path)) {
                mkdir($path);
            }

            $path .= $id . '_' . $file['name'];

            if(!move_uploaded_file($file['tmp_name'], $path)) {
                show_error('Error sending file, please check upload path and permissions');
            }
        }
    }

    /**
     * @see Rabbit_Field::postDelete()
     *
     * @param string $id
     */
    public function preDelete($id)
    {
        $this->removeFile($id);
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

        $attr['type']  = 'file';
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