<?php

namespace controllers;

class SlideController extends Controller
{
    public function addSlide()
    {
        $title = mysqli_real_escape_string($this->db->mysqli, $_POST["title"]);
        $content = json_encode(json_decode($_POST["content"]));
        $screen = 1;

        mysqli_stmt_prepare($this->db->stmt, "INSERT INTO slides (screen, title, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($this->db->stmt, "iss", $screen, $title, $content);
        try {
            mysqli_stmt_execute($this->db->stmt);
        } catch (\Exception $e) {
            throw $e;
        }
        return null;
    }

    public function fetchSlides(): string
    {
        return json_encode($this->db->select("SELECT slides.id AS id, slides.title AS title, slides.content AS content FROM slides JOIN screen ON slides.screen = screen.id WHERE screen.event = " . $this->event_id));
    }

    public function saveSlidesOrder(): void
    {
        $slides = json_decode($_POST["slides"]);
        foreach ($slides as $i => $slide) {
            $this->db->execute("UPDATE slides SET sort = $i WHERE id = " . $slide->id . " AND screen = 1");
        }
    }
}