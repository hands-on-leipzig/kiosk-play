<?php

namespace controllers;

class SettingController extends Controller
{

    public function saveSettings()
    {
        $settings = [];
        foreach ($_POST as $param => $value) {
            $value = mysqli_real_escape_string($value);
            $settings[$param] = $value;
        }
        $settings = json_encode($settings);


        mysqli_stmt_prepare($this->db->stmt,"UPDATE event SET settings = ? WHERE id = ?" );
        mysqli_stmt_bind_param($this->db->stmt, "ss", $settings, $event_id);
        try {
            mysqli_stmt_execute($this->db->stmt);
        } catch (\Exception $e) {
            throw $e;
        }
        return null;
    }

    public function getSettings(): string
    {
        return $this->db->select("SELECT settings FROM event WHERE id = " . $this->event_id)[0];
    }
}
