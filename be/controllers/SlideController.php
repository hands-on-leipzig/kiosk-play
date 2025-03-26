<?php

namespace controllers;

class SlideController extends Controller
{
    public function addSlide()
    {
        $slide = [];
        foreach ($_POST as $param => $value) {
            $value = mysqli_real_escape_string($this->db->mysqli, $value);
            $slide[$param] = $value;
        }
        $screen = 1;

        mysqli_stmt_prepare($this->db->stmt, "INSERT INTO slides (screen, title, content) VALUES (?,?,?)");
        mysqli_stmt_bind_param($this->db->stmt, "iss", $screen, $slide["title"], $slide["content"]);
        try {
            mysqli_stmt_execute($this->db->stmt);
        } catch (\Exception $e) {
            throw $e;
        }
        return null;
    }

    public function fetchSlides(): string
    {
        return json_encode($this->db->select("SELECT * FROM slides WHERE id = " . $this->event_id));
    }
}