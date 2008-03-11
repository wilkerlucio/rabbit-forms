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
<?= $form_open ?>
<?php foreach($fields as $field): ?>
<div style="float: left; width: <?= $view_params['label_width'] ?>px;">
    <?= $field['label'] ?>:
</div>
<div style="float: left; width: 500px;">
<?= $field['component'] ?>
</div>
<div style="color: #f00;">
    <?= $field['validation'] ?>
</div>
<hr style="clear: both; margin-top: 5px;" />
<?php endforeach; ?>
<button type="submit">Enviar</button>
<?= $form_close ?>
