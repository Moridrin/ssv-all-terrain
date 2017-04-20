<?php

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 6-1-17
 * Time: 6:38
 */
class TabField extends Field
{
    const FIELD_TYPE = 'tab';

    public $fields;
    public $name;

    /**
     * TabField constructor.
     *
     * @param int     $id
     * @param string  $title
     * @param string  $class
     * @param string  $style
     * @param Field[] $fields
     * @param string  $overrideRight
     */
    protected function __construct($id, $title, $class, $style, $overrideRight, $fields = array())
    {
        parent::__construct($id, $title, self::FIELD_TYPE, $class, $style, $overrideRight);
        $this->fields = $fields;
        $this->name   = strtolower(str_replace(' ', '_', $title));
    }

    /**
     * @param int   $id
     * @param Field $field
     */
    public function addField($id, $field)
    {
        $this->fields[$id] = $field;
    }

    /**
     * @param string $json
     *
     * @return TabField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        if ($values->field_type != self::FIELD_TYPE) {
            throw new Exception('Incorrect field type');
        }
        $fields = array();
        if (isset($values->fields)) {
            foreach ($values->fields as $field) {
                $fields[] = Field::fromJSON(json_encode($field));
            }
        }
        return new TabField(
            $values->id,
            $values->title,
            $values->class,
            $values->style,
            $values->override_right,
            $fields
        );
    }

    /**
     * @param bool $encode
     *
     * @return string the class as JSON object.
     */
    public function toJSON($encode = true)
    {
        $jsonFields = array();
        foreach ($this->fields as $field) {
            $jsonFields[] = $field->toJSON(false);
        }
        $values = array(
            'id'             => $this->id,
            'title'          => $this->title,
            'field_type'     => $this->fieldType,
            'class'          => $this->class,
            'style'          => $this->style,
            'override_right' => $this->overrideRight,
            'fields'         => $jsonFields,
        );
        if ($encode) {
            $values = json_encode($values);
        }
        return $values;
    }

    /**
     * @return string the field as HTML object.
     */
    public function getHTML()
    {
        $activeClass = isset($_POST['tab']) && $_POST['tab'] == $this->id ? 'active' : '';
        $class       = !empty($this->class) ? 'class="tab ' . esc_html($this->class) . '"' : 'class="tab ' . esc_html($activeClass) . '"';
        $style       = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';
        ob_start();
        ?>
        <li <?= $class ?> <?= $style ?>><a href="#<?= esc_html($this->name) ?>"><?= esc_html($this->title) ?></a></li>
        <?php
        return ob_get_clean();
    }

    public function getFieldsHTML()
    {
        ob_start();
        ?>
        <input type="hidden" name="tab" value="<?= esc_html($this->id) ?>">
        <div id="<?= esc_html($this->name) ?>">
            <?php foreach ($this->fields as $field): ?>
                <?= $field->getHTML() ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
