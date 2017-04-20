<?php

require_once 'TabField.php';
require_once 'HeaderField.php';
require_once 'InputField.php';
require_once 'LabelField.php';

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 5-1-17
 * Time: 20:25
 */
abstract class Field
{
    #region Constants
    const PREFIX = 'custom_field_';
    const CUSTOM_FIELD_IDS_META = 'custom_field_ids';
    #endregion

    #region Variables
    /** @var int $id */
    public $id;
    /** @var string $title */
    public $title;
    /** @var string $fieldType */
    public $fieldType;
    /** @var string $class */
    public $class;
    /** @var string $style */
    public $style;
    /** @var string $overrideRight */
    public $overrideRight;
    #endregion

    #region __construct($id, $title, $fieldType, $class, $style, $overrideRight)
    /**
     * Field constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $fieldType
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $fieldType, $class, $style, $overrideRight)
    {
        $this->id            = $id;
        $this->title         = $title;
        $this->fieldType     = $fieldType;
        $this->class         = $class;
        $this->style         = $style;
        $this->overrideRight = $overrideRight;
    }
    #endregion

    /**
     * @param string $name
     *
     * @return string with the title for that field or empty if no match is found.
     */
    public static function fromDatabase($name)
    {
        global $wpdb;
        $table  = SSV_General::CUSTOM_FIELDS_TABLE;
        $sql    = "SELECT customField FROM $table WHERE customField LIKE '%\"name\":\"$name\"%'";
        $fields = $wpdb->get_results($sql);
        foreach ($fields as $field) {
            $field = self::fromJSON($field->customField);
            if ($field instanceof TabField) {
                foreach ($field->fields as $childField) {
                    if ($childField instanceof InputField) {
                        if ($childField->name == $name) {
                            return $field->title;
                        }
                    }
                }
            } elseif ($field instanceof InputField && $field->name == $name) {
                return $field->title;
            }
        }
        return '';
    }

    #region fromJSON($json)
    /**
     * This function extracts a Field from the JSON string.
     *
     * @param string $json
     *
     * @return Field
     * @throws Exception if the field type is unknown.
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        switch ($values->field_type) {
            case InputField::FIELD_TYPE:
                return InputField::fromJSON($json);
            case TabField::FIELD_TYPE:
                return TabField::fromJSON($json);
            case HeaderField::FIELD_TYPE:
                return HeaderField::fromJSON($json);
            case LabelField::FIELD_TYPE:
                return LabelField::fromJSON($json);
        }
        throw new Exception('Unknown field type');
    }
    #endregion

    #region toJSON($encode = true)
    /**
     * This function creates an array containing all variables of this Field.
     *
     * @param bool $encode can be set to false if it is important not to json_encode the array.
     *
     * @return string the class as JSON object.
     */
    abstract public function toJSON($encode = true);
    #endregion

    #region getHTML()
    /**
     * This function returns a string with the Field as HTML (to be used in the frontend).
     *
     * @return string the field as HTML object.
     */
    abstract public function getHTML();
    #endregion

    #region getMaxID($fields)
    /**
     * This function returns the highest ID in all the fields (including all sub-fields)
     *
     * @param Field[] $fields
     *
     * @return int the max ID
     */
    public static function getMaxID($fields)
    {
        $maxID = 0;
        foreach ($fields as $field) {
            $maxID = $field->id > $maxID ? $field->id : $maxID;
            if ($field instanceof TabField) {
                foreach ($field->fields as $tabField) {
                    $maxID = $tabField->id > $maxID ? $tabField->id : $maxID;
                }
            }
        }
        return $maxID;
    }
    #endregion

    #region __toString()
    /**
     * @return string HTML code for the field
     */
    public function __toString()
    {
        return $this->getHTML();
    }
    #endregion
}
