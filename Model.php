<?php

namespace Svv\Framework;

abstract class Model
{

    public const RULE_REQUIRED = "required";
    public const RULE_EMAIL = "email";
    public const RULE_MIN = "min";
    public const RULE_MAX = "max";
    public const RULE_MATCH = "match";
    public const RULE_UNIQUE = "unique";
    public array $errors = [];

    /**
     * Hydrate the model
     *
     * @param array $data
     */
    public function loadData (array $data)
    {
        foreach ($data as $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->{"set" . ucfirst($key)}($value);
            }
        }
    }

    /**
     * Restrict the fields to several rules
     * Ex: return [
            "firstname"       => [self::RULE_REQUIRED],
            "lastname"        => [self::RULE_REQUIRED],
            "email"           => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]],
            "password"        => [self::RULE_REQUIRED, [self::RULE_MIN, "min" => 2], [self::RULE_MAX, "max" => 20]],
            "passwordConfirm" => [self::RULE_REQUIRED, [self::RULE_MATCH, "match" => "password"]],
        ];
     * @return array
     */
    abstract public function rules (): array;

    public function getLabel ($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * Check all the rules and return if the form is valid
     *
     * @return bool
     */
    public function isValid ()
    {
        foreach ($this->rules() as $attribute => $rules)
        {
            $value = $this->{"get" . ucfirst($attribute)}();
            foreach ($rules as $rule)
            {
                $ruleName = $rule;

                if (!is_string($ruleName))
                {
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::RULE_REQUIRED && !$value)
                {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL))
                {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }

                if ($ruleName === self::RULE_MIN && strlen($value) < $rule["min"])
                {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MAX && strlen($value) > $rule["max"])
                {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }

                if ($ruleName === self::RULE_MATCH && $value !== $this->{"get" . ucfirst($rule["match"])}())
                {
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }

                if ($ruleName === self::RULE_UNIQUE)
                {
                    $className = $rule["class"];
                    $uniqueAttr = $rule["attribute"] ?? $attribute;
                    $tableName = $className::tableName();
                    $stmt = App::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $stmt->bindValue(":attr", $value);
                    $stmt->execute();
                    $record = $stmt->fetchObject();

                    if ($record)
                    {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ["field" => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Return the error if it exists
     *
     * @param string $attribute
     * @return bool|mixed
     */
    public function hasError (string $attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Return the first error for the attribute
     *
     * @param string $attribute
     * @return bool|mixed
     */
    public function getFirstError (string $attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }

    /**
     * Add an error to the errors array
     *
     * @param string $attribute
     * @param string $rule
     * @param array  $params
     */
    private function addErrorForRule (string $attribute, string $rule, array $params = [])
    {
        $message = $this->errorMessage()[$rule] ?? "";
        foreach ($params as $key => $value)
        {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * @param string $attribute
     * @param string $message
     */
    public function addError (string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Return all the errors message for each rules
     *
     * @return string[]
     */
    private function errorMessage ()
    {
        return [
            self::RULE_REQUIRED => "This field is required",
            self::RULE_EMAIL    => "This field must be valid email address",
            self::RULE_MIN      => "Min length of this field must be {min}",
            self::RULE_MAX      => "Max length of this field must be {max}",
            self::RULE_MATCH    => "This field must be the same as {match}",
            self::RULE_UNIQUE   => "Record with this {field} already exists",
        ];
    }

}
