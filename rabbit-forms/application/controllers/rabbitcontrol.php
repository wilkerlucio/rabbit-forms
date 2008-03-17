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

class Rabbitcontrol extends Controller
{
    /**
     * Initialize Rabbit Form controller
     *
     */
    public function __construct()
    {
        parent::Controller();

        $this->load->library('rabbitform');
    }

    /**
     * Retrieve action
     *
     * @param string $config
     */
    public function retrieve($config)
    {
        $configData = $this->rabbitform->prepare_config($config . '.yml');
        $configData['retrieve']['manage'] = 'rabbitcontrol/manage/' . $config . '/';
        $configData['retrieve']['delete'] = 'rabbitcontrol/delete/' . $config . '/';

        echo $this->rabbitform->retrieve($configData);
    }

    /**
     * Manage action
     *
     * @param string $config
     * @param string $id
     */
    public function manage($config, $id = false)
    {
        echo $this->rabbitform->run($config . '.yml', $id);
    }

    /**
     * Delete action
     *
     * @param string $config
     * @param string $id
     */
    public function delete($config, $id)
    {
        $this->load->helper('url');

        $this->rabbitform->delete($config . '.yml', $id);

        redirect('rabbitcontrol/retrieve/' . $config);
    }
}