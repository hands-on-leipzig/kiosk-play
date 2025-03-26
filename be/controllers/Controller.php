<?php

namespace controllers;

use model\MysqlDB;

class Controller
{
    public int $event_id;
    public MysqlDB $db;

    public function __construct($event_id)
    {
        $this->event_id = $event_id;

        $this->db = new MysqlDB();
        $this->db->dbConnect();
    }
}