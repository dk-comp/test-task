<?php
declare(strict_types=1);

namespace PageParser;

abstract class TagService
{
    protected string $url;
    protected array $regExpArray;
    protected array $collection = [];
    public function getCollection(): array
    {
        if($this->url && str_starts_with($this->url, 'http')){
            $this->parseContent(file_get_contents($this->url));
        }

        return $this->collection;
    }

    protected function parseContent(string $content): void
    {
        foreach ($this->regExpArray as $item){
            preg_match_all($item, $content, $matches);

            if($matches){
                $this->collection[$item] = $matches;
            }
        }
    }
}