<?php
?>
        <a href="#" type="button" class="btn btn-primary" @click.prevent="addGroup(1)">
            <i class="rex-icon fa-plus-circle"></i>
            Gruppe hinzufügen
        </a>

        <textarea name="REX_INPUT_VALUE[<?= $this->repeater->rexVarId ?>]" class="hidden" cols="1" rows="1" x-bind:value="value">
            REX_VALUE[<?= $this->repeater->rexVarId ?>]
        </textarea>
    </div>
</section>

<script>
  var initialValues = <?= $this->repeater->initialValues ?>;
  var repeaterGroup = <?= $this->repeater->groupDefinition ?>;
  var repeaterFields = <?= $this->repeater->fieldsDefinition ?>;
</script>

