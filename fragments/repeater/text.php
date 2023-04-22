<?php
    $id = $this->repeater->getFieldId($this->field, $this->type);
?>

<label :for="<?= $id ?>"><?= $this->field['title'] ?></label>
<input type="text"
       class="form-control"
       placeholder="<?= $this->field['title'] ?>"
       x-model="<?= $this->repeater->getFieldModel($this->field, $this->type) ?>"
       type="text"
       name="<?= $this->field['name'] ?>[]"
       :id="<?= $id ?>"
       x-on:change="updateValues()">