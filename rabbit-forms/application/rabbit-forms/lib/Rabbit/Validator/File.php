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

class Rabbit_Validator_File extends Rabbit_Validator
{
    /**
     * @see Rabbit_Validator::validate()
     *
     * @return boolean
     */
    public function validate()
    {
        $ci =& get_instance();
        $ci->lang->load('rabbit');

        if($this->field->hasUpload()) {
            //validate file extensions
            $extensions = $this->getParam('extensions', array());
            
            if(count($extensions) > 0) {
                $ext = strtolower(pathinfo($_FILES[$this->field->getName()]['name'], PATHINFO_EXTENSION));
                
                if(!in_array($ext, $extensions)) {
                    $this->message = sprintf(
                        $ci->lang->line('rabbit_valid_file_extension'),
                        $ext,
                        $this->field->getLabel()
                    );
                    
                    return false;
                }
            }
            
            //validate file size
            $filesize = $_FILES[$this->field->getName()]['size'];
            
            $minsize = $this->getParam('min_size', false);
            $maxsize = $this->getParam('max_size', false);
        
            if($minsize !== false && $filesize < $minsize) {
                $this->message = sprintf(
                    $ci->lang->line('rabbit_valid_file_minsize'),
                    $this->field->getLabel(),
                    $minsize
                );
                
                return false;
            }
            
            if($maxsize !== false && $filesize > $maxsize) {
                $this->message = sprintf(
                    $ci->lang->line('rabbit_valid_file_maxsize'),
                    $this->field->getLabel(),
                    $maxsize
                );
                
                return false;
            }
        } else {
            if($this->getParam('required', false) && !$this->field->getForm()->getEditId()) {
                $this->message = sprintf(
                    $ci->lang->line('rabbit_valid_required'),
                    $this->field->getLabel()
                );
                
                return false;
            }
        }

        return true;
    }
}