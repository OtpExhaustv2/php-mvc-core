<?php

namespace App\Core\Database;

use App\Core\Model;
use App\Core\App;

abstract class DbModel extends Model
{

    abstract public static function primaryKey (): string;

    /**
     * @param string $sql
     * @return bool|\PDOStatement
     */
    public static function prepare (string $sql)
    {
        return App::$app->db->pdo->prepare($sql);
    }

    /**
     * Return the table name
     * Ex: return "users";
     * @return string
     */
    abstract public function tableName (): string;

    /**
     * Return the attributes of the classe
     * Ex: return ["firstname", "lastname", ...];
     * @return array
     */
    abstract public function attributes (): array;

    /**
     * Insert data to the database
     *
     * @return bool
     */
    public function save ()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        $stmt = self::prepare("INSERT INTO $tableName (" . implode(", ", $attributes) . ") 
            VALUES (" . implode(", ", $params) . ")
        ");

        foreach ($attributes as $attribute)
        {
            $stmt->bindValue(":$attribute", $this->{"get" . ucfirst($attribute)}());
        }

        $stmt->execute();
        return true;
    }

    /**
     * Return a result
     *
     * @param array $where
     * @return mixed
     */
    public function findOne (array $where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $stmt = self::prepare("SELECT * FROM $tableName WHERE $sql");
        $stmt->execute($where);

        return $stmt->fetchObject(static::class);
    }
}
