<?php

namespace model;

class BaseModel
{
    public MysqlDB $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
}