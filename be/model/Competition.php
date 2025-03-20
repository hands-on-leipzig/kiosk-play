<?php

namespace model;

class Competition
{
    private $id;

    private $slidesets = [];

    private $screens = [];

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function addScreen($screen)
    {
        $this->screens[] = $screen;
    }

    public function addSlideSet($slideSet)
    {
        $this->slidesets[] = $slideSet;
    }

}
