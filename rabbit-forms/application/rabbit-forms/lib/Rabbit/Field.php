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
 * This class is the soul of Rabbit Forms, by extendind
 * this class you are able to create any kind of field
 * to make you form exactly the way you need
 *
 * See documentation to learn how to extends this class
 */
abstract class Rabbit_Field
{
    /**
     * Form where field is contained
     *
     * @var Rabbit_Form
     */
    protected $form;

    /**
     * Field name in table
     *
     * @var string
     */
    protected $name = '';

    /**
     * Short description of field
     *
     * @var string
     */
    protected $label = '';

    /**
     * Value of field
     *
     * the value contained in the class is the db version
     * of data, prepared to save
     *
     * @var string
     */
    protected $value = '';

    /**
     * Attributes of field
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Validators to apply into field
     *
     * @var array
     */
    protected $validators = array();

    /**
     * Last validation message
     *
     * @var string
     */
    protected $validation_message;

    /**
     * This variable determines if a field will be persisted
     * in database or not
     *
     * @var boolean
     */
    protected $persist = true;

    /**
     * Construct a new field
     *
     * @param Rabbit_Form $form
     * @param array $attributes
     */
    public function __construct(Rabbit_Form $form, array $attributes = array())
    {
        $this->form = $form;
        $this->form->addField($this);

        $this->setAttributes($attributes);
    }

    /**
     * Overload this method to initialize your field
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Get form where field is contained
     *
     * @return Rabbit_Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label ? $this->label : $this->getName();
    }

    /**
     * Set field label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set field name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get a human readable version of value
     *
     * @return string
     */
    public function getDisplayValue()
    {
        return $this->value;
    }

    /**
     * Get value of field
     *
     * If your plugin needs to de-serialize data in some
     * way you will overload this method to unserialize
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value of field
     *
     * If your plugin needs to serialize data in some
     * way you will overload this method to serialize
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get field param
     *
     * @param string $name
     * @param mixed $default
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    /**
     * Set field attributes
     *
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get raw value data
     *
     * This function return the real value of field
     * This function can't be overloaded
     *
     * @return string
     */
    final public function getRawValue()
    {
        return $this->value;
    }

    /**
     * Set raw value data
     *
     * This function set the real value of field
     * This function can't be overloaded
     *
     * @return void
     */
    final public function setRawValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function getPersist()
    {
        return $this->persist;
    }

    /**
     * @param boolean $persist
     */
    public function setPersist($persist)
    {
        $this->persist = $persist;
    }

    /**
     * Add a validator to the field
     *
     * @param Rabbit_Validator $validator
     * @return void
     */
    public function addValidator(Rabbit_Validator $validator)
    {
        $this->validators[] = $validator;
    }

    /**
     * Get last validation message
     *
     * @return string
     */
    public function getValidationMessage()
    {
        return $this->validation_message;
    }

    /**
     * Validate field data
     *
     * @return boolean
     */
    public function validate()
    {
        foreach($this->validators as $validator) {
            if(!$validator->validate()) {
                $this->validation_message = $validator->getMessage();

                return false;
            }
        }

        return true;
    }

    /*
     * Abstract methods
     */

    /**
     * Return form HTML of field
     *
     * This method returns the html containing form field
     * to send data across form
     *
     * @return string
     */
    public abstract function getFieldHtml();

    /*
     * Events
     */

    /**
     * Event dispatched before insert
     *
     * @return void
     */
    public function preInsert()
    {

    }

    /**
     * Event dispatched after insert
     *
     * @param string $id
     * @return void
     */
    public function postInsert()
    {

    }

    /**
     * Event dispatched before update
     *
     * @param string $id
     * @return void
     */
    public function preUpdate()
    {

    }

    /**
     * Event dispatched after update
     *
     * @param string $id
     * @return void
     */
    public function postUpdate()
    {

    }

    /**
     * Event dispatched before record change (shortcut for insert and
     * update together)
     *
     * @return void
     */
    public function preChange()
    {

    }

    /**
     * Event dispatched after record change (shortcut for insert and
     * update together)
     *
     * @param string $id
     * @return void
     */
    public function postChange()
    {

    }

    /**
     * Event dispatched before delete
     *
     * @param string $id
     * @return void
     */
    public function preDelete()
    {

    }

    /**
     * Event dispatched after delete
     *
     * @param string $id
     * @return void
     */
    public function postDelete()
    {

    }
}