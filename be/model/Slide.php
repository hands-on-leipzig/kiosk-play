<?php

namespace model;

class Slide {
    public $id;
    public $title;
    public $content;

    public function __construct($id, $title, SlideContent $content) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content->toArray()
        ];
    }

    public function toJson(): string {
        return json_encode($this->toArray());
    }

    public static function fromJson(string $json): Slide {
        $arr = json_decode($json, true);
        $content = null;
        switch ($arr['content']['type']) {
            case 'ImageSlideContent':
                $content = new ImageSlideContent($arr['content']['imageUrl']);
                break;
            case 'RobotGameSlideContent':
                $content = new RobotGameSlideContent(
                    $arr['content']['backgroundImageUrl'],
                    $arr['content']['teamsPerPage'],
                    $arr['content']['showFooter'],
                    $arr['content']['footerImages'],
                    $arr['content']['highlightColor']
                );
                break;
        }
        return new Slide($arr['id'], $arr['title'], $content);
    }
}
