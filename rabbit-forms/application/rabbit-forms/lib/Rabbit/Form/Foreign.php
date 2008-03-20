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

class Rabbit_Form_Foreign extends Rabbit_Form
{
    /**
     * The parent form
     *
     * @var Rabbit_Form
     */
    protected $parentForm = null;
    
    /**
     * @see Rabbit_Form::addAsset()
     *
     * @param string $path
     */
    public function addAsset($path)
    {
        $this->parentForm->addAsset($path);
    }

    /**
     * @see Rabbit_Form::getAssetsOutput()
     *
     * @return string
     */
    public function getAssetsOutput()
    {
        return '';
    }

    /**
     * @see Rabbit_Form::getCloseTag()
     *
     * @return string
     */
    public function getCloseTag()
    {
        return '';
    }

    /**
     * @see Rabbit_Form::getOpenTag()
     *
     * @return string
     */
    public function getOpenTag()
    {
        return '';
    }
}