<?php

namespace controllers;

use model\MysqlDB;

class ScheduleController
{
    public function __construct()
    {

    }

    /**
     * @throws \Exception
     */
    public function getSchedule($schedule_id): array
    {
        $db = new MysqlDB("planning");
        $db->dbConnect();

        $q = "SELECT * FROM `plan` AS p";
        $q .= " WHERE id=$schedule_id";
        $result = $db->execute($q);
        if ($db->num_rows == 0) {
            throw new \Exception("No event found for id " . $schedule_id, 404);
        }

        $data = mysqli_fetch_object($result);
        $results = array(
            "id" => $data->id,
            "name" => $data->name,
            "rounds" => array(),
        );


        $db->dbDisconnect();


        return $results;
    }

    /**
     * @throws \Exception
     */
    public function getRoundsToShow($event_id): string
    {
        $db = new MysqlDB();
        $db->dbConnect();

        try {
            $o = $db->selectAsObj("event", "id", "=", $event_id);
        } catch (\Exception $e) {
            throw $e;
        }
        $db->dbDisconnect();
        return $o[0]->rg_score_show;
    }

    /**
     * @throws \Exception
     */
    public function getTournamentId($event_id): string
    {
        $db = new MysqlDB();
        $db->dbConnect();

        try {
            $o = $db->selectAsObj("event", "id", "=", $event_id);
        } catch (\Exception $e) {
            throw $e;
        }
        $db->dbDisconnect();
        return $o[0]->tournament;
    }
}
