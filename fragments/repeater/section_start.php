<section class="rex-repeater">
    <div x-data="repeater()" x-repeater @repeater:ready.once="setInitialValue()" id="x-repeater">
        <template x-if="groups.length">
            <a href="#" type="button" class="btn btn-primary mb-3" @click.prevent="addGroup(0)">
                <i class="rex-icon fa-plus-circle"></i> Gruppe hinzufügen
            </a>
        </template>