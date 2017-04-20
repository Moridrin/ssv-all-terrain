<?php

require_once 'input-fields/TextInputField.php';
require_once 'input-fields/CheckboxInputField.php';
require_once 'input-fields/SelectInputField.php';
require_once 'input-fields/ImageInputField.php';
require_once 'input-fields/HiddenInputField.php';
require_once 'input-fields/CustomInputField.php';

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 6-1-17
 * Time: 6:38
 */
class InputField extends Field
{
    const FIELD_TYPE = 'input';

    /** @var string $inputType */
    public $inputType;
    /** @var string $name */
    public $name;

    /** @var string $value */
    public $value;

    /**
     * InputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $inputType
     * @param string $name
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $inputType, $name, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::FIELD_TYPE, $class, $style, $overrideRight);
        $this->inputType = $inputType;
        $this->name      = $name;
    }

    /**
     * @param string $json
     *
     * @return InputField
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        switch ($values->input_type) {
            case TextInputField::INPUT_TYPE:
                return TextInputField::fromJSON($json);
            case SelectInputField::INPUT_TYPE:
                return SelectInputField::fromJSON($json);
            case CheckboxInputField::INPUT_TYPE:
                return CheckboxInputField::fromJSON($json);
            case ImageInputField::INPUT_TYPE:
                return ImageInputField::fromJSON($json);
            case HiddenInputField::INPUT_TYPE:
                return HiddenInputField::fromJSON($json);
            default:
                return CustomInputField::fromJSON($json);
        }
    }

    /**
     * @param bool $encode
     *
     * @return string the class as JSON object.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function toJSON($encode = true)
    {
        throw new Exception('This should be implemented in a sub class.');
    }

    /**
     * @return string the field as HTML object.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function getHTML()
    {
        throw new Exception('This should be implemented in sub class: ' . get_class($this) . '.');
    }

    /**
     * @return string the field as HTML object.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function getFilterRow()
    {
        throw new Exception('This should be implemented in sub class: ' . get_class($this) . '.');
    }

    /**
     * @return string the field as HTML object.
     */
    public function getFilterRowBase($filter)
    {
        ob_start();
        ?>
        <td>
            <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?></label>
        </td>
        <td>
            <label>
                Filter
                <input id="filter_<?= esc_html($this->id) ?>" type="checkbox" name="filter_<?= esc_html($this->name) ?>">
            </label>
        </td>
        <td>
            <?= $filter ?>
        </td>
        <?php
        return ob_get_clean();
    }

    /**
     * @return array|bool array of errors or true if no errors.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function isValid()
    {
        throw new Exception('This should be implemented in sub class: ' . get_class($this) . '.');
    }

    /**
     * @param string|array|User|mixed $value
     */
    public function setValue($value)
    {
        if ($this instanceof HiddenInputField) {
            return; //Can't change the value of hidden fields.
        }
        if ($value instanceof User) { //User values can always be set (even if isDisabled())
            $this->value = SSV_General::sanitize($value->getMeta($this->name));
        } elseif (is_array($value)) {
            if (isset($value[$this->name])) {
                $this->value = SSV_General::sanitize($value[$this->name]);
            }
        } else {
            $this->value = SSV_General::sanitize($value);
        }
    }

    /**
     * @return bool returns if the field is disabled or not.
     */
    public function isDisabled()
    {
        if ($this instanceof CheckboxInputField
            || $this instanceof CustomInputField
            || $this instanceof SelectInputField
            || $this instanceof TextInputField
        ) {
            return $this->disabled;
        } else {
            return false;
        }
    }
}
