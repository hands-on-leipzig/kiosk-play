<?php

namespace model;

class BaseModel
{
    /**
     * @var \MysqlDB
     */
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
}