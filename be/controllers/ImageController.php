<?php

namespace controllers;

class ImageController
{
    public function getRandomPhoto()
    {
        $imageDir = '../../public/photos/';
        $files = array_diff(scandir($imageDir), array('.', '..'));
        if (sizeof($files) == 0) return json_encode("");
        $file = $files[rand(2, sizeof($files) + 1)];
        $imagePath = '/photos/' . $file;
        return json_encode($imagePath);
    }
}