<?php

namespace Svv\Framework\Form\Fields;

use Svv\Framework\Model;

abstract class BaseField
{

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
        $this->model = $model;
        $this->attribute = $attribute;
    }

    abstract public function renderInput (): string;

    /**
     * Return the input with args of the model
     *
     * @return string
     */
    public function __toString ()
    {
        return sprintf('
            <div class="form-group">
                <label>%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ', $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }

}
