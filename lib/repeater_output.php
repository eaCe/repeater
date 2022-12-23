<?php

class repeater_output
{
    const GROUP = 1;
    const FIELD = 2;

    private $groupDefinition = null;
    private $fieldsDefinition = null;
    private $initialValues = null;
    private $output = '';

    protected function getOutput(): string
    {
        $this->output .= $this->getLoader();
        $this->output .= $this->getSectionStart();
        $this->getRepeaterObject();
        $this->output .= $this->getSectionEnd();

        return $this->output;
    }

    private function getLoader(): string
    {
        return '<div class="alpine-loader rex-ajax-loader rex-visible"><div class="rex-ajax-loader-elements"><div class="rex-ajax-loader-element1 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element2 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element3 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element4 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element5 rex-ajax-loader-element"></div></div></div>';
    }

    private function getSectionStart(): string
    {
//        $initialValue = htmlspecialchars(rex_article_slice::getArticleSliceById(rex_request::get('slice_id'))->getValue($this->rexVarId));
        $output = '<section class="rex-repeater"><div x-data="repeater()" x-repeater @repeater:ready.once="setInitialValue()" id="x-repeater">';
        $output .= '<template x-if="groups.length"><a href="#" type="button" class="btn btn-primary mb-3" @click.prevent="addGroup(0)"><i class="rex-icon fa-plus-circle"></i> Gruppe hinzufügen</a></template>';
        return $output;
    }

    private function getSectionEnd(): string
    {
        $output = '<a href="#" type="button" class="btn btn-primary" @click.prevent="addGroup(1)"><i class="rex-icon fa-plus-circle"></i> Gruppe hinzufügen</a>';
        $output .= '<textarea name="REX_INPUT_VALUE[' . $this->rexVarId . ']" class="hidden" cols="1" rows="1" x-bind:value="value">REX_VALUE[' . $this->rexVarId . ']</textarea>';
        $output .= '</div></section>';
        $output .= '<script>var initialValues = ' . $this->initialValues . '; var repeaterGroup = ' . $this->groupDefinition . ';var repeaterFields = ' . $this->fieldsDefinition . ';</script>';
        return $output;
    }

    private function getRepeaterObject()
    {
        $groupDefinition = [];
        $fieldsDefinition = [];
        $initialValues = json_decode(rex_article_slice::getArticleSliceById(rex_request::get('slice_id'))->getValue($this->rexVarId), true);

        if ($this->repeater['group'] && array_key_exists('fields', $this->repeater['group'])) {
            foreach ($this->repeater['group']['fields'] as $field) {
                $groupDefinition[$field['name']] = '';


                /**
                 * add any missing fields...
                 */
                if (is_string($field['name']) && $initialValues) {
                    foreach ($initialValues as $index => &$values) {
                        if (!key_exists($field['name'], $values) && $field['name'] !== 'fields') {
                            if ($field['type'] === 'link') {
                                $values[$field['name']] = [
                                    'id' => '',
                                    'name' => '',
                                ];
                            } else {
                                $values[$field['name']] = '';
                            }
                        }
                    }
                }
            }

            $this->output .= '<template x-for="(group, groupIndex) in groups" :key="groupIndex"><div class="repeater-group">';
            $this->getGroupHeader($this->repeater['group']);
        } else {
            return;
        }

        if ($this->depth === 2) {
            if (array_key_exists('fields', $this->repeater['group']['group'])) {
                $groupDefinition['fields'] = [];

                foreach ($this->repeater['group']['group']['fields'] as $field) {
                    if ($field['type'] === 'link') {
                        $fieldsDefinition[$field['name']] = [
                            'id' => '',
                            'name' => '',
                        ];
                    } else {
                        $fieldsDefinition[$field['name']] = '';
                    }

                    /**
                     * add any missing fields...
                     */
                    if (is_string($field['name']) && $initialValues) {
                        foreach ($initialValues as $index => &$values) {
                            if (key_exists('fields', $values)) {
                                for ($i = 0; $i < count($values['fields']); $i++) {
                                    if (!key_exists($field['name'], $values['fields'][$i])) {
                                        $values['fields'][$i][$field['name']] = '';
                                    }

                                    if ($field['type'] === 'link' && is_string($values['fields'][$i][$field['name']])) {
                                        $values['fields'][$i][$field['name']] = $fieldsDefinition[$field['name']];
                                    }
                                }
                            }
                        }
                    }
                }

                $this->output .= '<template x-for="(field, index) in group.fields" :key="index"><div class="repeater-group">';
                $this->output .= $this->getFieldsHeader($this->repeater['group']['group']);
                $this->output .= '</div></template>';
                $this->output .= '<a href="#" type="button" class="btn btn-primary" @click.prevent="addFields(groupIndex)"><i class="rex-icon fa-plus-circle"></i> Felder hinzufügen</a>';
            }
        }

        $this->output .= '</div></div></template>';

        $this->groupDefinition = json_encode($groupDefinition);
        $this->fieldsDefinition = json_encode($fieldsDefinition);
        $this->initialValues = json_encode($initialValues);
    }

    private function getGroupHeader($group)
    {
        $this->output .= '
            <header class="mb-3 pb-3">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-sm-9"><strong>' . $group['title'] . '</strong></div>
                        <div class="col-sm-3 text-right">

                            <template x-if="groupIndex !== 0">
                                <a href="#" @click.prevent="moveGroup(groupIndex, groupIndex-1)" class="button move"><i class="rex-icon fa-chevron-up"></i></a>
                            </template>

                            <template x-if="groupIndex+1 < groups.length">
                                <a href="#" @click.prevent="moveGroup(groupIndex, groupIndex+1)" class="button move"><i class="rex-icon fa-chevron-down"></i></a>
                            </template>

                            <a href="#" @click.prevent="removeGroup(groupIndex)" class="button remove"><i class="rex-icon fa-times"></i></a>
                        </div>
                    </div>
                </div>
            </header>
            <div>';

        if (isset($group['fields'])) {
            $this->output .= '<div class="flex-wrapper">';
            foreach ($group['fields'] as $field) {
                $this->output .= $this->getField($field, self::GROUP);
            }
            $this->output .= '</div>';
        }
    }

    private function getFieldsHeader($group)
    {
        $this->output .= '
            <header class="mb-3 pb-3">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-sm-9"><strong>' . $group['title'] . '</strong></div>
                        <div class="col-sm-3 text-right">

                            <template x-if="index !== 0">
                                <a href="#" @click.prevent="moveField(groupIndex, index, index-1)" class="button move"><i class="rex-icon fa-chevron-up"></i></a>
                            </template>
                            <template x-if="index+1 < group.fields.length">
                                <a href="#" @click.prevent="moveField(groupIndex, index, index+1)" class="button move"><i class="rex-icon fa-chevron-down"></i></a>
                            </template>

                            <a href="#" @click.prevent="removeField(groupIndex, index)" class="button remove"><i class="rex-icon fa-times"></i></a>
                        </div>
                    </div>
                </div>
            </header>';

        if (isset($group['fields'])) {
            $this->output .= '<div class="flex-wrapper">';
            foreach ($group['fields'] as $field) {
                $this->output .= $this->getField($field, self::FIELD);
            }
            $this->output .= '</div>';
        }
    }

    private function getField($field, $type): string
    {
        $output = '<div class="mb-3 field " ' . (isset($field['width']) && $field['width'] ? 'style="width:' . $field['width'] . '%;"' : 'style="width:100%;"') . '>';
        switch ($field['type']) {
            case 'text':
                $output .= $this->getTextField($field, $type);
                break;
            case 'textarea':
                $output .= $this->getTextareaField($field, $type);
                break;
            case 'link':
                $output .= $this->getLinkField($field, $type);
                break;
            case 'media':
                $output .= $this->getMediaField($field, $type);
                break;
            default:
                return '';
                break;
        }
        $output .= '</div>';
        return $output;
    }

    private function getFieldId($field, $type, $suffix = ''): string
    {
        if ($type === self::GROUP) {
            return '\'group-' . $field['name'] . '-\'+groupIndex' . $suffix;
        } elseif ($type === self::FIELD) {
            return '\'' . $field['name'] . '-\'+groupIndex+\'-\'+index' . $suffix;
        }
    }

    private function getFieldModel($field, $type, $suffix = ''): string
    {
        if ($type === self::GROUP) {
            return 'group.' . $field['name'] . $suffix;
        } elseif ($type === self::FIELD) {
            return 'field.' . $field['name'] . $suffix;
        }
    }

    private function getTextField($field, $type): string
    {
        $id = $this->getFieldId($field, $type);
        return '<label :for="' . $id . '">' . $field['title'] . '</label>
                <input type="text"
                       class="form-control"
                       placeholder="' . $field['title'] . '"
                       x-model="' . $this->getFieldModel($field, $type) . '"
                       type="text"
                       name="' . $field['name'] . '[]"
                       :id="' . $id . '"
                       x-on:change="updateValues()">';
    }

    private function getTextareaField($field, $type): string
    {
        $id = $this->getFieldId($field, $type);
        return '<label :for="' . $id . '">' . $field['title'] . '</label>
                <textarea class="form-control"
                          name="' . $field['name'] . '[]"
                          placeholder="' . $field['title'] . '"
                          :id="' . $id . '"
                          x-model="' . $this->getFieldModel($field, $type) . '"
                          x-on:change="updateValues()"></textarea>';
    }

    private function getLinkField($field, $type): string
    {
        $id = $this->getFieldId($field, $type);
        $nameId = $this->getFieldId($field, $type, '+\'_NAME\'');
        return '<label :for="' . $nameId . '">' . $field['title'] . '</label>
                <div class="input-group">
                    <input class="form-control"
                           type="text"
                           x-model="' . $this->getFieldModel($field, $type, '.name') . '"
                           :id="' . $nameId . '"
                           readonly="">
                    <input type="hidden"
                           name="' . $field['name'] . '[]"
                           x-model="' . $this->getFieldModel($field, $type, '.id') . '"
                           :id="' . $id . '">
                    <span class="input-group-btn">
                        <a href="#"
                           class="btn btn-popup"
                           @click.prevent="addLink(' . $id . ', groupIndex, index, \'' . $field['name'] . '\')"
                           title="Link auswählen"><i class="rex-icon rex-icon-open-linkmap"></i>
                        </a>
                        <a href="#"
                           class="btn btn-popup"
                           @click.prevent="removeLink(groupIndex, index, \'' . $field['name'] . '\');return false;"
                           title="Ausgewählten Link löschen"><i class="rex-icon rex-icon-delete-link"></i>
                        </a>
                    </span>
                </div>';
    }

    private function getMediaField($field, $type): string
    {
        $imageId = null;
        $id = null;

        if ($type === self::GROUP) {
            $imageId = '\'REX_MEDIA_\'+group-' . $field['name'] . '-\'+groupIndex';
        } elseif ($type === self::FIELD) {
            $imageId = '\'REX_MEDIA_' . $field['name'] . '-\'+groupIndex+\'-\'+index';
        }

        return '<label :for="' . $imageId . '">Bild</label>
                <div class="input-group">
                    <input class="form-control"
                           type="text"
                           name="' . $field['name'] . '"
                           :id="' . $imageId . '"
                           readonly=""
                           x-model="field.' . $field['name'] . '">
                    <span class="input-group-btn">
                        <a href="#"
                           class="btn btn-popup"
                           @click.prevent="selectImage(\'' . $field['name'] . '-\'+groupIndex+\'-\'+index, groupIndex, index, \'' . $field['name'] . '\')"
                           title="Medium auswählen"><i class="rex-icon rex-icon-open-mediapool"></i></a>

                        <a href="#"
                           class="btn btn-popup"
                           @click.prevent="addImage(\'' . $field['name'] . '-\'+groupIndex+\'-\'+index, groupIndex, index, \'' . $field['name'] . '\')"
                           title="Neues Medium hinzufügen"><i class="rex-icon rex-icon-add-media"></i></a>

                        <a href="#"
                           class="btn btn-popup"
                           @click.prevent="deleteImage(\'' . $field['name'] . '-\'+groupIndex+\'-\'+index, groupIndex, index, \'' . $field['name'] . '\')"
                           title="Ausgewähltes Medium löschen"><i class="rex-icon rex-icon-delete-media"></i></a>
                    </span>
                </div>';
    }
}
