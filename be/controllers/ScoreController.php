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
    public function getScores($event_id): array|string
    {
        $round_show_setting = json_decode($this->getRoundsToShow($event_id));
        $tournament_id = $this->getTournamentId($event_id);
        $db = new MysqlDB("contao");
        $db->dbConnect();

        $q = "SELECT * FROM `tl_hot_tournament` AS t";
        $q .= " WHERE t.region=$tournament_id";
        $result = $db->execute($q);
        if ($db->num_rows == 0) {
            throw new \Exception("No event found for id " . $tournament_id, 404);
        }

        $data = mysqli_fetch_object($result);
        $results = array(
            "id" => $data->id,
            "name" => $data->name,
            "rounds" => array(),
        );

        $rounds_to_show = [];
        if ($round_show_setting->vr1 || $round_show_setting->vr2 || $round_show_setting->vr3) $rounds_to_show[] = "VR";
        if ($round_show_setting->af) $rounds_to_show[] = "AF";
        if ($round_show_setting->vf) $rounds_to_show[] = "VF";
        if ($round_show_setting->hf) $rounds_to_show[] = "HF";

        foreach ($rounds_to_show as $round) {
            $q = "SELECT te.team_name AS name, te.id AS id, a.points AS points, r.matches AS num_matches FROM `tl_hot_round` AS r";
            $q .= " JOIN `tl_hot_tournament` AS t ON r.tournament=t.id";
            $q .= " JOIN `tl_hot_match` AS m ON m.round=r.id";
            $q .= " JOIN `tl_hot_assessment`AS a ON a.matchx=m.id";
            $q .= " JOIN `tl_hot_teams` AS te ON a.team=te.id";
            $q .= " WHERE t.region=$tournament_id";
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
                    $maxPoints[$ind] = "";
                }
                if ($o->points > $maxPoints[$ind]) {
                    $maxPoints[$ind] = $o->points;
                }

                $results["rounds"][$round][$o->id]["scores"][] = [
                    "points" => $o->points,
                    "highlight" => false,
                ];

                $results["rounds"][$round][$o->id]["name"] = $o->name;
            }
        }
        $db->dbDisconnect();

        if (empty($results["rounds"])) return $results;

        foreach ($results["rounds"]["VR"] as $t_id => $r) {
            if (!$round_show_setting->vr1) $results["rounds"]["VR"][$t_id]["scores"][0]["points"] = 0;
            if (!$round_show_setting->vr2) $results["rounds"]["VR"][$t_id]["scores"][1]["points"] = 0;
            if (!$round_show_setting->vr3) $results["rounds"]["VR"][$t_id]["scores"][2]["points"] = 0;
        }

        return $results;
    }

    /**
     * @throws \Exception
     */
    public function setRoundsToShow($event_id): null
    {
        $rounds = json_encode(array(
            "vr1" => $_POST["vr1"] == "true",
            "vr2" => $_POST["vr2"] == "true",
            "vr3" => $_POST["vr3"] == "true",
            "af" => $_POST["af"] == "true",
            "vf" => $_POST["vf"] == "true",
            "hf" => $_POST["hf"] == "true",
        ));

        $db = new MysqlDB();
        $db->dbConnect();
        mysqli_stmt_prepare($db->stmt,"UPDATE event SET rg_score_show = ? WHERE id = ?" );
        mysqli_stmt_bind_param($db->stmt, "ss", $rounds, $event_id);
        try {
            mysqli_stmt_execute($db->stmt);
        } catch (\Exception $e) {
            throw $e;
        }
        $db->dbDisconnect();
        return null;
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
