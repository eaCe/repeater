<?php

class repeater_output
{
    public const GROUP = 1;
    public const FIELD = 2;

    public $groupDefinition;
    public $fieldsDefinition;
    public $initialValues;
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
        $fragment = new \rex_fragment();
        return $fragment->parse('repeater/loader.php');
    }

    private function getSectionStart(): string
    {
        //        $initialValue = htmlspecialchars(rex_article_slice::getArticleSliceById(rex_request::get('slice_id'))->getValue($this->rexVarId));
        $fragment = new \rex_fragment();
        return $fragment->parse('repeater/section_start.php');
    }

    private function getSectionEnd(): string
    {
        $fragment = new \rex_fragment();
        $fragment->setVar('repeater', $this, false);
        return $fragment->parse('repeater/section_end.php');
    }

    private function getRepeaterObject(): void
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
                        if (!array_key_exists($field['name'], $values) && 'fields' !== $field['name']) {
                            if ('link' === $field['type']) {
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

        if (2 === $this->depth) {
            if (array_key_exists('fields', $this->repeater['group']['group'])) {
                $groupDefinition['fields'] = [];

                foreach ($this->repeater['group']['group']['fields'] as $field) {
                    if ('link' === $field['type']) {
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
                            if (array_key_exists('fields', $values)) {
                                for ($i = 0, $iMax = count($values['fields']); $i < $iMax; ++$i) {
                                    if (!array_key_exists($field['name'], $values['fields'][$i])) {
                                        $values['fields'][$i][$field['name']] = '';
                                    }

                                    if ('link' === $field['type'] && is_string($values['fields'][$i][$field['name']])) {
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

    private function getGroupHeader(array $group): void
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

    private function getFieldsHeader(array $group): void
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

    private function getField(array $field, int $type): string
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

    public function getFieldId(array $field, int $type, string $suffix = ''): string
    {
        if (self::GROUP === $type) {
            return '\'group-' . $field['name'] . '-\'+groupIndex' . $suffix;
        }

        if (self::FIELD === $type) {
            return '\'' . $field['name'] . '-\'+groupIndex+\'-\'+index' . $suffix;
        }

        return '';
    }

    public function getFieldModel(array $field, int $type, string $suffix = ''): string
    {
        if (self::GROUP === $type) {
            return 'group.' . $field['name'] . $suffix;
        }

        if (self::FIELD === $type) {
            return 'field.' . $field['name'] . $suffix;
        }

        return '';
    }

    private function getTextField(array $field, int $type): string
    {
        $fragment = new \rex_fragment();
        $fragment->setVar('repeater', $this, false);
        $fragment->setVar('field', $field, false);
        $fragment->setVar('type', $type, false);
        return $fragment->parse('repeater/text.php');
    }

    private function getTextareaField(array $field, int $type): string
    {
        $fragment = new \rex_fragment();
        $fragment->setVar('repeater', $this, false);
        $fragment->setVar('field', $field, false);
        $fragment->setVar('type', $type, false);
        return $fragment->parse('repeater/textarea.php');
    }

    private function getLinkField(array $field, int $type): string
    {
        $id = $this->getFieldId($field, $type);
        $nameId = $this->getFieldId($field, $type, '+\'_NAME\'');

        $fragment = new \rex_fragment();
        $fragment->setVar('id', $id, false);
        $fragment->setVar('nameId', $nameId, false);
        $fragment->setVar('repeater', $this, false);
        $fragment->setVar('field', $field, false);
        $fragment->setVar('type', $type, false);
        return $fragment->parse('repeater/link.php');
    }

    private function getMediaField(array $field, int $type): string
    {
        $mediaId = null;

        if (self::GROUP === $type) {
            $mediaId = '\'REX_MEDIA_group-' . $field['name'] . '-\'+groupIndex';
        } elseif (self::FIELD === $type) {
            $mediaId = '\'REX_MEDIA_' . $field['name'] . '-\'+groupIndex+\'-\'+index';
        }

        $fragment = new \rex_fragment();
        $fragment->setVar('mediaId', $mediaId, false);
        $fragment->setVar('repeater', $this, false);
        $fragment->setVar('field', $field, false);
        $fragment->setVar('type', $type, false);
        return $fragment->parse('repeater/media.php');
    }
}
