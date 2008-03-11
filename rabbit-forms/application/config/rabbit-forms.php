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

/*
 * Configure plugins paths
 *
 * This array contains the paths to search for form plugins
 */
$config['rabbit-fields-classpath'] = array(APPPATH . 'rabbit-forms/lib/Rabbit/Field/');

/**
 * Configure YAML paths
 */
$config['rabbit-yml-classpath'] = array(APPPATH . 'rabbit-forms/forms/');

/**
 * Configure Validator paths
 */
$config['rabbit-validator-classpath'] = array(APPPATH . 'rabbit-forms/lib/Rabbit/Validator/');

/**
 * Default configuration
 */
$config['rabbit-default-settings'] = array(
	'form' => array(
		'primary_key' => 'id'
	),
	
	'view' => array(
		'template' => 'rabbit-forms/view_linear'
	),
	
	'redirect' => ''
);