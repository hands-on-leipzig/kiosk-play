<?php

namespace controllers;

use model\MysqlDB;

class ScoreController
{
    public function __construct()
    {

    }

    /**
     * @throws \Exception
     */
    public function getScores($event_id): array|bool|\mysqli_result|string
    {
        $db = new MysqlDB("contao");
        $db->dbConnect();

        $q = "SELECT * FROM `tl_hot_tournament` AS t";
        $q .= " WHERE t.region=$event_id";
        $result = $db->execute($q);
        if ($db->num_rows == 0) {
            throw new \Exception("No event found for id " . $event_id, 404);
        }

        $data = mysqli_fetch_object($result);
        $results = array(
            "id" => $data->id,
            "name" => $data->name,
            "rounds" => array(),
        );
$count = "";
        foreach (["VR", "AF", "VF", "HF"] as $round) {
            $q = "SELECT te.team_name AS name, te.id AS id, a.points AS points, r.matches AS num_matches FROM `tl_hot_round` AS r";
            $q .= " JOIN `tl_hot_tournament` AS t ON r.tournament=t.id";
            $q .= " JOIN `tl_hot_match` AS m ON m.round=r.id";
            $q .= " JOIN `tl_hot_assessment`AS a ON a.matchx=m.id";
            $q .= " JOIN `tl_hot_teams` AS te ON a.team=te.id";
            $q .= " WHERE t.region=$event_id";
            $q .= " AND r.type='$round'";
            $q .= " AND confirmed_team=1 AND confirmed_referee=1";
            $q .= " ORDER BY a.crdate ASC";
            $result = $db->execute($q);
            if (!$result) {
                continue;
            }

            $maxPoints = [];
            $added = "";
            for ($i = 0; $i < $db->num_rows; $i++) {
                $o = mysqli_fetch_object($result);
                $ind = $o->id;

                if (!isset($maxPoints[$ind])) {
                    $added .= $ind . ", " . $i . ";;  ";
                    $maxPoints[$ind] = "";
                }
                if ($o->points > $maxPoints[$ind]) {
                    $maxPoints[$ind] = $o->points;
                }
                $team = ["name" => $o->name, "scores" => []];

                $team["scores"][] = [
                    "points" => $o->points,
                    "highlight" => false,
                ];

                $results["rounds"][$round][] = $team;
                //$results["rounds"][$round][$o->id]["scores"][] = ["points" => $o->points, "highlight" => false];
                // qmax($results["rounds"][$round][$o->id]["scores"])
            }

        }
        $db->dbDisconnect();
        return $results;
    }
}
