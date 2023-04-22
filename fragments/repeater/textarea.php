<?php
    $id = $this->repeater->getFieldId($this->field, $this->type);
?>

<label :for="<?= $id ?>"><?= $this->field['title'] ?></label>
<textarea class="form-control"
          name="<?= $this->field['name'] ?>[]"
          placeholder="<?= $this->field['title'] ?>"
          :id="<?= $id ?>"
          x-model="<?= $this->repeater->getFieldModel($this->field, $this->type) ?>"
          x-on:change="updateValues()"></textarea>