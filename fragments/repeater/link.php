<?php
    $id = $this->repeater->getFieldId($this->field, $this->type);
    $nameId = $this->repeater->getFieldId($this->field, $this->type, '+\'_NAME\'');
?>

<label :for="<?= $nameId ?>"><?= $this->field['title'] ?></label>
<div class="input-group">
    <input class="form-control"
           type="text"
           x-model="<?= $this->repeater->getFieldModel($this->field, $this->type, '.name') ?>"
           :id="<?= $nameId ?>"
           readonly="">
    <input type="hidden"
           name="<?= $this->field['name'] ?>[]"
           x-model="<?= $this->repeater->getFieldModel($this->field, $this->type, '.id') ?>"
           :id="<?= $id ?>">
    <span class="input-group-btn">
        <a href="#"
           class="btn btn-popup"
           @click.prevent="addLink(<?= $id ?>, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Link auswählen"><i class="rex-icon rex-icon-open-linkmap"></i>
        </a>
        <a href="#"
           class="btn btn-popup"
           @click.prevent="removeLink(groupIndex, index, '<?= $this->field['name'] ?>');return false;"
           title="Ausgewählten Link löschen"><i class="rex-icon rex-icon-delete-link"></i>
        </a>
    </span>
</div>
