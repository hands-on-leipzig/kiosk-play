<?php

namespace model;
require_once 'vendor/autoload.php';
class Screen extends BaseModel
{
    public $table = "screen";

    public $id;
    public $event;
    public $name;

    public function register($event_id)
    {
        $this->db->insertInto($this->table, ["event"], [$event_id]);
    }

    public function show_screens($event_id): array
    {
        return $this->db->selectAsObj($this->table, "event" ,"=", $event_id);
    }
}
