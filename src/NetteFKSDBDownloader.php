<?php

declare(strict_types=1);

namespace Fykosak\NetteFKSDBDownloader;

use Fykosak\FKSDBDownloaderCore\FKSDBDownloader;
use Fykosak\FKSDBDownloaderCore\Requests\Request;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Nette\SmartObject;

final class NetteFKSDBDownloader
{
    use SmartObject;

    private FKSDBDownloader $downloader;
    private string $username;
    private string $password;
    private string $expiration;
    private string $jsonApiUrl;
    private Cache $cache;

    public function __construct(
        string $jsonApiUrl,
        string $username,
        string $password,
        string $expiration,
        Storage $storage
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->username = $username;
        $this->password = $password;
        $this->jsonApiUrl = $jsonApiUrl;
        $this->expiration = $expiration;
    }

    public function getDownloader(): FKSDBDownloader
    {
        if (!isset($this->downloader)) {
            $this->downloader = new FKSDBDownloader("", $this->username, $this->password, $this->jsonApiUrl);
        }
        return $this->downloader;
    }

    /**
     * @throws \Throwable
     */
    public function download(Request $request, ?string $explicitExpiration = null): string
    {
        return $this->cache->load(
            $request->getCacheKey() . '-json',
            function (&$dependencies) use ($request, $explicitExpiration): string {
                $dependencies[Cache::EXPIRE] = $explicitExpiration ?? $this->expiration;
                return $this->getDownloader()->downloadJSON($request);
            }
        );
    }
}
