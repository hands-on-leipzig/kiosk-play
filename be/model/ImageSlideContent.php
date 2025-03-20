<?php

namespace model;

class ImageSlideContent extends SlideContent {
    public $imageUrl;

    public function __construct($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function getType(): string {
        return 'ImageSlideContent';
    }

    public function toArray(): array {
        return [
            'type' => $this->getType(),
            'imageUrl' => $this->imageUrl
        ];
    }
}
