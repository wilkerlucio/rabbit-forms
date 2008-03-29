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

require_once(APPPATH . 'rabbit-forms/lib/Image_Toolbox.php');
require_once(APPPATH . 'rabbit-forms/lib/Rabbit/Field/File.php');

class Rabbit_Field_Image extends Rabbit_Field_File
{
    /**
     * Overload the construtor to force image extensions
     *
     * @param Rabbit_Form $form
     * @param array $attributes
     */
    public function __construct(Rabbit_Form $form, array $attributes = array())
    {
        parent::__construct($form, $attributes);
        
        $validator = Rabbit_Validator_Factory::factory('File', $this);
        $validator->setParams(array(
            'extensions' => array(
                'jpg',
                'jpeg',
                'gif',
                'png',
                'bmp'
            )
        ));
    }
    
    /**
     * @see Rabbit_Field_File::removeFile()
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
        
        //remove copies
        $copies = $this->getAttribute('copies', array());
        
        foreach($copies as $copy) {
            $label = isset($copy['label']) ? $copy['label'] : '';
            
            $path = $this->baseFilePath() . $id . '_' . $label . '_' . $data[$this->getName()];
            
            if(file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Get image tag to display image
     *
     * @return string
     */
    public function getImageTag()
    {
        if(!$this->getValue()) {
            return '';
        }
        
        $basepath = $this->reachFieldPath() . $this->form->getEditId() . '_' . $this->getValue();
        
        $ci =& get_instance();
        $ci->load->helper('url');
        
        $copy = $this->getAttribute('display_copy', '');
        
        if($copy) {
            $linkopen = sprintf(
                '<a href="%s" target="%s">',
                base_url() . $basepath,
                $this->getAttribute('link_target', '_blank')
            );
            
            $path = $this->reachFieldPath() . $this->form->getEditId() . '_' . $copy . '_' . $this->getValue();
            $linkclose = '</a>';
        } else {
            $linkopen = '';
            $path = $basepath;
            $linkclose = '';
        }
        
        return sprintf(
            '%s<img src="%s" alt="%s" />%s',
            $linkopen,
            base_url() . $path,
            $this->getValue(),
            $linkclose
        );
    }
    
    /**
     * @see Rabbit_Field::getDisplayValue()
     *
     * @return string
     */
    public function getDisplayValue()
    {
        return $this->getImageTag();
    }

    /**
     * @see Rabbit_Field_File::preChange()
     *
     */
    public function preChange()
    {
        if($this->hasUpload()) {
            $filename = pathinfo($_FILES[$this->getName()]['name'], PATHINFO_FILENAME);
            $filetype = $this->getAttribute('output_type', pathinfo($_FILES[$this->getName()]['name'], PATHINFO_EXTENSION));
            
            $this->getForm()->addHiddenField($this->getName(), $filename . '.' . $filetype);
        }
    }
    
    /**
     * @see Rabbit_Field_File::postChange()
     *
     * @param unknown_type $id
     */
    public function postChange($id)
    {
        if($this->hasUpload()) {
            $this->getForm()->removeHiddenField($this->getName());
            
            $modes = array(
                'fix' => 0,
                'crop' => 1,
                'preserve' => 2
            );

            $file = $_FILES[$this->getName()];

            //add new file
            $path = $this->baseFilePath();

            //make dir if not exists
            if(!is_dir($path)) {
                mkdir($path);
            }
            
            $path .= $id . '_' . $file['name'];
            
            $filename = pathinfo($file['name'], PATHINFO_FILENAME);
            $filetype = $this->getAttribute('output_type', pathinfo($file['name'], PATHINFO_EXTENSION));
            
            $img = new Image_Toolbox($file['tmp_name']);
            
            if($this->getAttribute('resize_width') || $this->getAttribute('resize_height')) {
                $img->newOutputSize(
                    $this->getAttribute('resize_width', 0),
                    $this->getAttribute('resize_height', 0),
                    $modes[$this->getAttribute('resize_mode', 'fix')],
                    false,
                    $this->getAttribute('resize_bgcolor', '#000000')
                );
            }
            
            $img->save(
                $this->baseFilePath() . $id . '_' . $filename . '.' . $filetype,
                $filetype
            );
            
            $copies = $this->getAttribute('copies', array());
            
            foreach($copies as $copy) {
                $filetype = isset($copy['output_type']) ? $copy['output_type'] : pathinfo($file['name'], PATHINFO_EXTENSION);
                $label = isset($copy['label']) ? $copy['label'] : '';
                
                $img = new Image_Toolbox($file['tmp_name']);
                
                $img->newOutputSize(
                    isset($copy['resize_width']) ? $copy['resize_width'] : 0,
                    isset($copy['resize_height']) ? $copy['resize_height'] : 0,
                    $modes[isset($copy['resize_mode']) ? $copy['resize_mode'] : 'fix'],
                    false,
                    isset($copy['resize_bgcolor']) ? $copy['resize_bgcolor'] : '#000000'
                );
                
                $img->save(
                    $this->baseFilePath() . $id . '_' . $label . '_' . $filename . '.' . $filetype,
                    $filetype
                );
            }
        }
    }

    /**
     * @see Rabbit_Field_File::getFieldHtml()
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $img = $this->getImageTag();
        
        if($img) {
            $img = '<br /><br />' . $img;
        }
        
        return parent::getFieldHtml() . $img;
    }
}