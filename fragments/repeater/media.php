<?php
    $openerId = str_replace('REX_MEDIA_', '', $this->mediaId);
?>

<label :for="<?= $this->mediaId ?>"><?= $this->field['title'] ?></label>
<div class="input-group" x-init="console.log(group)">
    <input class="form-control"
           type="text"
           name="<?= $this->field['name'] ?>[]"
           :id="<?= $this->mediaId ?>"
           readonly=""
           x-model="<?= $this->repeater->getFieldModel($this->field, $this->type) ?>">
    <span class="input-group-btn">
        <a href="#"
           class="btn btn-popup"
           @click.prevent="selectMedia(<?= $openerId ?>, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Medium auswählen"><i class="rex-icon rex-icon-open-mediapool"></i></a>

        <a href="#"
           class="btn btn-popup"
           @click.prevent="addMedia(<?= $openerId ?>, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Neues Medium hinzufügen"><i class="rex-icon rex-icon-add-media"></i></a>

        <a href="#"
           class="btn btn-popup"
           @click.prevent="deleteMedia(<?= $openerId ?>, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Ausgewähltes Medium löschen"><i class="rex-icon rex-icon-delete-media"></i></a>
    </span>
</div>