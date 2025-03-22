<?php

namespace controllers;

use model\MysqlDB;

class ScoreController
{
    public function __construct()
    {

    }

    public function getScore($event_id): array|bool
    {
        $db = new MysqlDB("contao");
        $db->dbConnect();

        $results = [];

        $q = "SELECT te.team_name AS name, te.id AS id, a.points AS points, r.matches AS num_matches FROM `tl_hot_round` AS r";
        $q .= " JOIN `tl_hot_tournament` AS t ON r.tournament=t.id";
        $q .= " JOIN `tl_hot_match` AS m ON m.round=r.id";
        $q .= " JOIN `tl_hot_assessment`AS a ON a.matchx=m.id";
        $q .= " JOIN `tl_hot_teams` AS te ON a.team=te.id";
        $q .= " WHERE t.region=$event_id";
        $q .= " AND r.type='VR'";
        $q .= " AND confirmed_team=1 AND confirmed_referee=1";
        $q .= " ORDER BY a.crdate ASC";
        $result = $db->execute($q);
        if (!$result) {
            return false;
        }
        for ($i = 0; $i < $db->num_rows; $i++) {
            $o = mysqli_fetch_object($result);
            $results["preliminary"][$o->id] = ["name" => $o->name];
            $results["preliminary"][$o->id]["scores"][$i % $o->num_matches] = $o->points;
        }

        $db->dbDisconnect();
        return $results;
    }
}
