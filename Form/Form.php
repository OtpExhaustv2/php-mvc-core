<?php

namespace Svv\Framework\Form;

use Svv\Framework\Model;
use Svv\Framework\Form\Fields\InputField;
use Svv\Framework\Form\Fields\TextAreaField;

class Form
{

    /**
     * Return the begin of a form
     *
     * @param string $action
     * @param string $method
     * @return string
     */
    public static function begin (string $action, string $method)
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    /**
     * Return the end of a form
     *
     * @return string
     */
    public static function end ()
    {
        echo '</form>';
    }

    /**
     * @param \Svv\Framework\Model $model
     * @param string          $attribute
     * @return \Svv\Framework\Form\Fields\InputField
     */
    public function inputField (Model $model, string $attribute)
    {
        return new InputField($model, $attribute);
    }

    /**
     * @param \Svv\Framework\Model $model
     * @param string          $attribute
     * @return \Svv\Framework\Form\Fields\TextAreaField
     */
    public function textareaField (Model $model, string $attribute)
    {
        return new TextAreaField($model, $attribute);
    }

}
