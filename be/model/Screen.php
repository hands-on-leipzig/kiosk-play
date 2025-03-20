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
        if (!isset($_POST["name"])) {
            return "500";
        }
        return $this->db->insertInto($this->table, ["event", "name"], [$event_id, $_POST["name"]]);
    }

    public function show_screens($event_id): array
    {
        return $this->db->selectAsObj($this->table, "event" ,"=", $event_id);
    }
}
