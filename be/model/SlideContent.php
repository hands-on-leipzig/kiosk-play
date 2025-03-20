<?php

namespace model;

abstract class SlideContent {

    private $id;

    abstract public function getType(): string;
    abstract public function toArray(): array;
}
