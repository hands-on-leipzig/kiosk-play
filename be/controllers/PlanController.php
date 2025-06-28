<?php

namespace controllers;

use model\MysqlDB;

class PlanController
{
    public function __construct()
    {

    }

    /**
     * @throws \Exception
     */
    public function getPlan($plan_id): array
    {
        $db = new MysqlDB("planning");
        $db->dbConnect();

        $q = "SELECT * FROM `plan` AS p";
        $q .= " WHERE id=$plan_id";
        $result = $db->execute($q);
        if ($db->num_rows == 0) {
            throw new \Exception("No event found for id " . $plan_id, 404);
        }

        $data = mysqli_fetch_object($result);
        $results = array(
            "id" => $data->id,
            "name" => $data->name,
            "event" => $data->event,
            "created" => $data->created,
            "last_change" => $data->last_change,
            "public" => $data->public,
        );


        $db->dbDisconnect();


        return $results;
    }

    public function getParams($plan_id): array
    {
        $db = new MysqlDB("planning");
        $db->dbConnect();

        $q = "SELECT * FROM `plan_param_value` AS p";
        $q .= " WHERE plan=$plan_id";
        $result = $db->execute($q);
        if ($db->num_rows == 0) {
            throw new \Exception("No params found for plan " . $plan_id, 404);
        }

        $data = mysqli_fetch_object($result);
        $results = array(
            "id" => $data->id,
            "parameter" => $data->parameter,
            "plan" => $data->plan,
            "set_value" => $data->set_value,
        );

        $db->dbDisconnect();

        return $results;
    }

}
