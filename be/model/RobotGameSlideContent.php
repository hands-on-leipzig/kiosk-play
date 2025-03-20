<?php

namespace model;

class RobotGameSlideContent extends SlideContent {
    public $backgroundImageUrl;
    public $teamsPerPage;
    public $showFooter;
    public $footerImages;
    public $highlightColor;

    public function __construct($backgroundImageUrl, $teamsPerPage, $showFooter, $footerImages, $highlightColor) {
        $this->backgroundImageUrl = $backgroundImageUrl;
        $this->teamsPerPage = $teamsPerPage;
        $this->showFooter = $showFooter;
        $this->footerImages = $footerImages;
        $this->highlightColor = $highlightColor;
    }

    public function getType(): string {
        return 'RobotGameSlideContent';
    }

    public function toArray(): array {
        return [
            'type' => $this->getType(),
            'backgroundImageUrl' => $this->backgroundImageUrl,
            'teamsPerPage' => $this->teamsPerPage,
            'showFooter' => $this->showFooter,
            'footerImages' => $this->footerImages,
            'highlightColor' => $this->highlightColor
        ];
    }
}
