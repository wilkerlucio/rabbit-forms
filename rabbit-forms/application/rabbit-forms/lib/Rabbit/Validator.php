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

abstract class Rabbit_Validator
{
    /**
     * Field to validate
     *
     * @var Rabbit_Field
     */
    protected $field;

    /**
     * Validator parameters
     *
     * @var array
     */
    protected $params = array();

    /**
     * Validator message
     *
     * @var string
     */
    protected $message = '';

    /**
     * Instantiate the validator
     *
     * @param Rabbit_Field $field
     * @param array $params
     */
    public function __construct(Rabbit_Field $field, array $params = array())
    {
        $this->field = $field;
        $this->field->addValidator($this);

        $this->setParams($params);
    }

    /**
     * Get validation message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    /**
     * Set validator parameters
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Validate data
     *
     * @return boolean
     */
    abstract function validate();
}