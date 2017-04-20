<?php

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 6-1-17
 * Time: 6:38
 */
class HeaderField extends Field
{
    const FIELD_TYPE = 'header';

    /**
     * HeaderField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::FIELD_TYPE, $class, $style, $overrideRight);
    }

    /**
     * @param string $json
     *
     * @return HeaderField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        if ($values->field_type != self::FIELD_TYPE) {
            throw new Exception('Incorrect field type');
        }
        return new HeaderField(
            $values->id,
            $values->title,
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
        $class = !empty($this->class) ? 'class="' . esc_html($this->class) . '"' : '';
        $style = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';
        ob_start();
        ?>
        <h2 <?= $class ?> <?= $style ?>><?= esc_html($this->title) ?></h2>
        <?php
        return ob_get_clean();
    }
}
