<?php

class repeater extends repeater_output
{
    public $rexVarId;
    protected $repeater = [];
    private $group;
    protected $depth;

    /**
     * @throws rex_exception
     */
    public function __construct($rexVarId = null)
    {
        if (!$rexVarId) {
            $this->throwException('REX_VAR id is missing');
        }

        $this->rexVarId = $rexVarId;
    }

    private function setRexVar($rexVarId)
    {
        //        $this->output
    }

    public function addGroup($name = 'Group')
    {
        if (array_key_exists('group', $this->repeater)) {
            if (2 === $this->depth) {
                $this->throwException('Maximum depth of nested groups reached. [' . $name . ']');
                return;
            }

            $this->repeater['group']['group'] = $this->getFieldBasics($name);
            $this->group = &$this->repeater['group']['group'];
            $this->depth = 2;
        } else {
            $this->repeater['group'] = $this->getFieldBasics($name);
            $this->group = &$this->repeater['group'];
            $this->depth = 1;
        }
    }

    public function addText($name, $width = null): void
    {
        $this->addField($name, ['type' => 'text'], $width);
    }

    public function addTextarea($name, $width = null)
    {
        $this->addField($name, ['type' => 'textarea'], $width);
    }

    public function addLink($name, $width = null)
    {
        $this->addField($name, ['type' => 'link', ['id' => '', 'name' => '']], $width);
    }

    public function addMedia($name, $width = null)
    {
        $this->addField($name, ['type' => 'media'], $width);
    }

    private function addField($name, $params, $width)
    {
        $this->addFields();
        $this->fieldExists($name);

        $fieldName = $this->getFieldName($name);
        $this->group['fields'][$fieldName] = $this->getFieldBasics($name);

        if ($params) {
            $this->group['fields'][$fieldName] = array_merge($this->group['fields'][$fieldName], $params);
        }

        if ($width) {
            $this->group['fields'][$fieldName]['width'] = str_replace(',', '.', (float) $width);
        }

        //        if(array_key_exists('fields', $this->group)) {
        //            echo '<pre>';
        //            var_dump($this->depth);
        //            echo '</pre>';
        //        }
        //        else if(array_key_exists('group', $this->repeater)) {
        //            echo '<pre>';
        //            var_dump($this->depth);
        //            echo '</pre>';
        //        }
        //        else {
        //            $this->throwException('Group missing, please add at least one group.');
        //        }
    }

    private function addFields()
    {
        if (array_key_exists('fields', $this->group)) {
            return;
        }

        $this->group['fields'] = [];
    }

    private function fieldExists($name)
    {
        if (array_key_exists($this->getFieldName($name), $this->group['fields'])) {
            $this->throwException('Field "' . $name . '" already exists');
        }
    }

    private function getFieldBasics($name)
    {
        return [
            'name' => $this->getFieldName($name),
            'title' => $name,
        ];
    }

    private function checkFieldName()
    {
        if (array_key_exists('group', $this->repeater['group'])) {
        } elseif (array_key_exists('group', $this->repeater)) {
        } else {
            $this->throwException('Group missing, please add at least one group.');
        }
    }

    private function getFieldName($name): string
    {
        return \rex_string::normalize($name, '_');
    }

    public function show()
    {
        echo $this->getOutput();
    }

    public static function getRepeater($rexVarId)
    {
        //        json_decode(html_entity_decode());
    }

    /**
     * @throws rex_exception
     */
    private function throwException($message)
    {
        throw new rex_exception('Repeater: ' . $message);
    }
}
