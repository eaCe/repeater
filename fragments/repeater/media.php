<label :for="<?= $this->imageId ?>">Bild</label>
<div class="input-group">
    <input class="form-control"
           type="text"
           name="<?= $this->field['name'] ?>"
           :id="<?= $this->imageId ?>"
           readonly=""
           x-model="field.<?= $this->field['name'] ?>">
    <span class="input-group-btn">
        <a href="#"
           class="btn btn-popup"
           @click.prevent="selectImage('<?= $this->field['name'] ?>-'+groupIndex+'-'+index, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Medium auswählen"><i class="rex-icon rex-icon-open-mediapool"></i></a>
    
        <a href="#"
           class="btn btn-popup"
           @click.prevent="addImage('<?= $this->field['name'] ?>-'+groupIndex+'-'+index, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Neues Medium hinzufügen"><i class="rex-icon rex-icon-add-media"></i></a>
    
        <a href="#"
           class="btn btn-popup"
           @click.prevent="deleteImage('<?= $this->field['name'] ?>-'+groupIndex+'-'+index, groupIndex, index, '<?= $this->field['name'] ?>')"
           title="Ausgewähltes Medium löschen"><i class="rex-icon rex-icon-delete-media"></i></a>
    </span>
</div>