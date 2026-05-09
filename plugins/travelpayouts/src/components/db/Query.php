<?php

namespace Travelpayouts\components\db;

use Travelpayouts\components\BaseObject;

/**
 * Class MigrationDB
 * @package Travelpayouts\includes\migrations
 * @property-read \wpdb $db
 */
class Query extends BaseObject
{
    protected $db;

    protected function getConnection()
    {
        if (!$this->db) {
            global $wpdb;
            $this->db = $wpdb;
        }
        return $this->db;
    }

    public function select(array $fields, $table)
    {
        $fieldQuery = implode(',', $fields);
        $tableName = $this->getTableName($table);
        $sql = 'SELECT ' . $fieldQuery . ' FROM ' . $tableName . ' LIMIT 1000';
        return $this->getConnection()->get_results($sql, ARRAY_A);
    }

    public function getTableName($name)
    {
        return $this->getConnection()->prefix . $name;
    }

    public function tableExists($table)
    {
        $tableName = $this->getTableName($table);
        return $this->getConnection()->get_var("SHOW TABLES LIKE '{$tableName}'") == $tableName;
    }

    public function count($table)
    {
        $tableName = $this->getTableName($table);
        return  $this->tableExists($table) ? (int) $this->getConnection()->get_row("select COUNT(*) as count from $tableName")->count: 0;
    }

    public function drop($table)
    {
        $tableName = $this->getTableName($table);
        $sql = "DROP TABLE IF EXISTS {$tableName}";
        return $this->getConnection()->query($sql);
    }
}
