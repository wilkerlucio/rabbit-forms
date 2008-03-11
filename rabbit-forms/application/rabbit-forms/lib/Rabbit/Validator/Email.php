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

class Rabbit_Validator_Email extends Rabbit_Validator
{

    /**
     * @see Rabbit_Validator::validate()
     *
     * @return boolean
     */
    function validate()
    {
        $value = $this->field->getRawValue();
        
        if(!preg_match("/^([a-z0-9\\+_\\-]+)(\\.[a-z0-9\\+_\\-]+)*@([a-z0-9\\-]+\\.)+[a-z]{2,6}$/ix", $value)) {
            $this->message = sprintf(
                'O campo %s deve ser um email válido',
                $this->field->getLabel()
            );
            
            return false;
        }
        
        return true;
    }
}