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

class Rabbit_Validator_Compare extends Rabbit_Validator
{
    /**
     * @see Rabbit_Validator::validate()
     *
     * @return boolean
     */
    public function validate()
    {
        $to = $this->field->getForm()->getField($this->getParam('compareTo', ''));
        
        if($to === null) {
            $this->message = sprintf(
                'The %s field is not found',
                $this->getParam('compareTo', '')
            );
            
            return false;
        }
        
        $comparator = $this->getParam('comparator', 'equals');
        $method     = 'compare_' . $comparator;
        
        if(!method_exists($this, $method)) {
            $this->message = sprintf(
                'The %s comparator is not found',
                $comparator
            );
            
            return false;
        }
        
        return call_user_func(array($this, $method), $to);
    }
    
    /**
     * Check if the fields has same value
     *
     * @param Rabbit_Field $to
     * @return boolean
     */
    public function compare_equals(Rabbit_Field $to)
    {
        $v1 = $this->field->getRawValue();
        $v2 = $to->getRawValue();
        
        if($v1 != $v2) {
            $this->message = sprintf(
                'The %s field must be equals to %s field',
                $this->field->getLabel(),
                $to->getLabel()
            );
            
            return false;
        }
        
        return true;
    }
    
    //TODO: implement more comparations
}