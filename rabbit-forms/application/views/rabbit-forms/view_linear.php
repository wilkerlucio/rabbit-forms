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
<style type="text/css">

.rabbit-field-container {
    background: #eef;
    margin: 10px 0;
    padding: 10px;
}

.rabbit-field-label {
    float: left;
    width: 180px;
}

.rabbit-field-component {
    float: left;
    width: 250px;
}

.rabbit-field-error {
    color: #f00;
}

.rabbit-field-clear {
    clear: both;
}

</style>
<?= $form_assets ?>
<?= $form_open ?>
<?php foreach($fields as $field): ?>
<div class="rabbit-field-container">
    <div class="rabbit-field-label"><?= $field['label'] ?>:</div>
    <div class="rabbit-field-component"><?= $field['component'] ?></div>
    <div class="rabbit-field-error"><?= $field['validation'] ?></div>
    <div class="rabbit-field-clear"></div>
</div>
<?php endforeach; ?>
<button type="submit">Enviar</button>
<?= $form_close ?>
<?= $form_exec ?>