<?php
declare(strict_types=1);

namespace PageParser;
interface ITagService
{
    public function __construct(string $url);
    public function getCollection(): array;
    public function getContentSize(): float;
}