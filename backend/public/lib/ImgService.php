<?php
declare(strict_types=1);

namespace PageParser;

class ImgService extends TagService implements ITagService
{
    protected array $urlParts;
    protected float $fullImagesSize = 0;
    public function __construct(string $url)
    {
        $this->url = trim($url);
        $this->regExpArray = [
            '/<img\s+[^>]*(src|data\-original)\s*=\s*(["\'])([^\2]+?)\2[^>]*>/is',
            '/(background|background-image)\s*:[^">\']*url\s*\((["\']?)([^\)]+)\2\)/is',
        ];
    }
    public function getCollection(): array
    {
        parent::getCollection();
        $this->urlParts = parse_url($this->url);

        return $this->prepareCollection();
    }

    public function getContentSize(): float
    {
        return round($this->fullImagesSize / pow(1024, 2), 2);
    }

    protected function prepareCollection(): array
    {
        $collection = [];
        $urlParts = $this->urlParts;

        array_map(
            function ($item) use (&$collection, $urlParts){
                $elements = [];

                foreach ($item[count($item) - 1] as $value){
                    if(str_starts_with($value, '/')){
                        $value = $urlParts['scheme'] . '://' . $urlParts['host'] . $value;
                    }

                    $elements[] = ['url' => $value];
                }

                $collection = array_merge($collection, $elements);
            },
            $this->collection
        );

        return $this->collection = $this->getSizes($collection);
    }

    protected function getSizes(array $collection): array
    {
        if(!$collection){
            return $collection;
        }

        $requests = [];

        foreach ($collection as $item){
            $requests[] = $this->createCurlRequest($item['url']);
        }

        return $this->multiRequest($requests, $collection);

    }

    protected function multiRequest(array $requests, array $collection): array
    {
        $mh = curl_multi_init();
        foreach($requests as $ch)
        {
            curl_multi_add_handle($mh, $ch);
        }

        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        foreach($requests as $key => $ch)
        {
            $collection[$key]['size'] = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            $this->fullImagesSize += $collection[$key]['size'];

            curl_multi_remove_handle($mh, $ch);
        }

        curl_multi_close($mh);

        return $collection;
    }

    protected function createCurlRequest(string $url): \CurlHandle|false
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

}