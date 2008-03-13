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

?>
<table>
    <thead>
        <tr>
            <?php foreach($fields as $field): ?>
            <th><?= $field ?></th>
            <?php endforeach; ?>
            <th colspan="2"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($rows as $row): ?>
        <tr>
            <?php foreach($kfields as $field): ?>
            <th><?= $row[$field] ?></th>
            <?php endforeach; ?>
            <th><a href="<?= site_url($manage . $row['rabbit_row_id']) ?>" />editar</a></th>
            <th><a href="<?= site_url($delete . $row['rabbit_row_id']) ?>" />delete</a></th>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>