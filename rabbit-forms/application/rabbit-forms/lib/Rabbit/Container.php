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
 * Simple container to easy get values that not exists (replacing by defaults)
 */
class Rabbit_Container
{
	/**
	 * Container data
	 *
	 * @var array
	 */
	private $data = array();
	
	/**
	 * Create a new container
	 *
	 * @param array $data
	 */
	public function __construct(array $data = array())
	{
		$this->setData($data);
	}
	
	/**
	 * Get container data
	 *
	 * @param string $index
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($index, $default = null)
	{
		return isset($this->data[$index]) ? $this->data[$index] : $default;
	}
	
	/**
	 * Set container data
	 *
	 * @param string $index
	 * @param mixed $value
	 * @return void
	 */
	public function set($index, $value)
	{
		$this->data[$index] = $value;
	}
	
	/**
	 * Set main data
	 * 
	 * Note: this function will replace any old data into container
	 *
	 * @param array $data
	 * @return void
	 */
	public function setData(array $data)
	{
		$this->data = $data;
	}
	
	/**
	 * Get magic method, return value if exists or null string otherwise
	 *
	 * @param string $index
	 * @return mixed
	 */
	public function __get($index)
	{
		return $this->get($index, '');
	}
}