<?php

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 10-1-17
 * Time: 12:03
 */
class TextInputField extends InputField
{
    const INPUT_TYPE = 'text';

    /** @var bool $disabled */
    public $disabled;
    /** @var array $required */
    public $required;
    /** @var string $defaultValue */
    public $defaultValue;
    /** @var string $placeholder */
    public $placeholder;

    /**
     * TextInputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $name
     * @param string $required
     * @param string $disabled
     * @param string $defaultValue
     * @param string $placeholder
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $name, $disabled, $required, $defaultValue, $placeholder, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::INPUT_TYPE, $name, $class, $style, $overrideRight);
        $this->disabled     = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
        $this->required     = filter_var($required, FILTER_VALIDATE_BOOLEAN);
        $this->defaultValue = $defaultValue;
        $this->placeholder  = $placeholder;
    }

    /**
     * @param string $json
     *
     * @return TextInputField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        if ($values->input_type != self::INPUT_TYPE) {
            throw new Exception('Incorrect input type');
        }
        return new TextInputField(
            $values->id,
            $values->title,
            $values->name,
            $values->disabled,
            $values->required,
            $values->default_value,
            $values->placeholder,
            $values->class,
            $values->style,
            $values->override_right
        );
    }

    /**
     * @param bool $encode
     *
     * @return string the class as JSON object.
     */
    public function toJSON($encode = true)
    {
        $values = array(
            'id'             => $this->id,
            'title'          => $this->title,
            'field_type'     => $this->fieldType,
            'input_type'     => $this->inputType,
            'name'           => $this->name,
            'disabled'       => $this->disabled,
            'required'       => $this->required,
            'default_value'  => $this->defaultValue,
            'placeholder'    => $this->placeholder,
            'class'          => $this->class,
            'style'          => $this->style,
            'override_right' => $this->overrideRight,
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
        if ($this->defaultValue == 'NOW') {
            $this->defaultValue = (new DateTime('NOW'))->format('Y-m-d');
        }
        $value       = isset($this->value) ? $this->value : $this->defaultValue;
        $id          = !empty($this->id) ? 'id="' . esc_html($this->id) . '"' : '';
        $name        = 'name="' . $this->name . '"';
        $class       = !empty($this->class) ? 'class="' . esc_html($this->class) . '"' : 'class="validate"';
        $style       = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';
        $placeholder = !empty($this->placeholder) ? 'placeholder="' . esc_html($this->placeholder) . '"' : '';
        $value       = !empty($value) ? 'value="' . esc_html($value) . '"' : '';
        $disabled    = $this->disabled ? 'disabled' : '';
        $required    = $this->required ? 'required' : '';

        if (isset($overrideRight) && current_user_can($overrideRight)) {
            $disabled = '';
            $required = '';
        }

        ob_start();
        if (current_theme_supports('materialize')) {
            ?>
            <div class="input-field">
                <input type="text" <?= $id ?> <?= $name ?> <?= $class ?> <?= $style ?> <?= $value ?> <?= $disabled ?> <?= $placeholder ?> <?= $required ?> title="<?= esc_html($this->title) ?>"/>
                <label><?= esc_html($this->title) ?><?= $this->required ? '*' : '' ?></label>
            </div>
            <?php
        }

        return trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
    }

    /**
     * @return string the filter for this field as HTML object.
     */
    public function getFilterRow()
    {
        ob_start();
        ?><input id="<?= esc_html($this->id) ?>" type="text" name="<?= esc_html($this->name) ?>" class="field-filter" title="<?= esc_html($this->title) ?>"/><?php
        return $this->getFilterRowBase(ob_get_clean());
    }

    /**
     * @return Message[]|bool array of errors or true if no errors.
     */
    public function isValid()
    {
        $errors = array();
        if (($this->required && !$this->disabled) && (empty($this->value))) {
            $errors[] = new Message($this->title . ' is required but not set.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
        }
        return empty($errors) ? true : $errors;
    }
}
