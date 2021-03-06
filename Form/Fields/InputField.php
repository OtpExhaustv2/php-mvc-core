<?php

namespace Svv\Framework\Form\Fields;

use Svv\Framework\Model;

class InputField extends BaseField
{

    public const TYPE_TEXT = "text";
    public const TYPE_PASSWORD = "password";
    public const TYPE_NUMBER = "number";

    public string $type;
    public Model $model;
    public string $attribute;

    /**
     * Field constructor.
     *
     * @param \Svv\Framework\Model $model
     * @param string          $attribute
     */
    public function __construct (Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    /**
     * Change the current type to : password
     *
     * @return $this
     */
    public function passwordField ()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    /**
     * @return string
     */
    public function renderInput (): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control%s">',
            $this->type,
            $this->attribute,
            $this->model->{"get" . ucfirst($this->attribute)}(),
            $this->model->hasError($this->attribute) ? " is-invalid" : "",
        );
    }
}
