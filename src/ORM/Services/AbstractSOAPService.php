<?php

namespace Fykosak\NetteFKSDBDownloader\ORM\Services;

use DOMDocument;
use Fykosak\FKSDBDownloaderCore\Requests\Request;
use Fykosak\NetteFKSDBDownloader\NetteFKSDBDownloader;
use Fykosak\NetteFKSDBDownloader\ORM\XMLParser;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Nette\SmartObject;

abstract class AbstractSOAPService
{

    use SmartObject;

    protected NetteFKSDBDownloader $downloader;

    protected Cache $cache;
    protected string $expiration;

    /**
     * ServiceEvent constructor.
     * @param string $expiration
     * @param NetteFKSDBDownloader $downloader
     * @param Storage $storage
     */
    public function __construct(string $expiration, NetteFKSDBDownloader $downloader, Storage $storage)
    {
        $this->cache = new Cache($storage, static::class);
        $this->downloader = $downloader;
        $this->expiration = $expiration;
    }

    /**
     * @param Request $request
     * @param string $rootNodeName
     * @param string $modelClassName
     * @param string|null $explicitExpiration
     * @return array
     * @throws \Throwable
     */
    protected function getItems(Request $request, string $rootNodeName, string $modelClassName, ?string $explicitExpiration = null): array
    {
        return $this->cache->load($request->getCacheKey(), function (&$dependencies) use ($request, $rootNodeName, $modelClassName, $explicitExpiration): array {
            $dependencies[Cache::EXPIRE] = $explicitExpiration ?? $this->expiration;
            $items = [];
            $xml = $this->downloader->download($request);

            $doc = new DOMDocument();
            $doc->loadXML($xml);
            foreach ($doc->getElementsByTagName($rootNodeName) as $node) {
                $items[] = XMLParser::parseXMLNode($node, $modelClassName);
            }
            return $items;
        });
    }
}
