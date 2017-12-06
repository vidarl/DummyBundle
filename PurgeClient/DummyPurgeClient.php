<?php

namespace Vidarl\DummyBundle\PurgeClient;

use FOS\HttpCacheBundle\CacheManager;
use eZ\Publish\Core\MVC\Symfony\Cache\PurgeClientInterface;

class DummyPurgeClient// implements PurgeClientInterface
{
    public function __construct()
    {
    }

    public function purge($tags)
    {
        var_dump("DummyPurgeClient::purge()", $tags);
        if (empty($tags)) {
            return;
        }

        // As key only support one tag being invalidated at a time, we loop.
        // These will be queued by HttpCache\ProxyClient and handled on kernel.terminate.
        foreach (array_unique((array)$tags) as $tag) {
            if (is_numeric($tag)) {
                $tag = 'location-' . $tag;
            }

            $headers = ['Fastly-Soft-Purge' => '1'];
            $fastlyKey = 'DummyPurgeClientKey';
            if ($fastlyKey !== null ) {
                $headers['Fastly-Key'] = $fastlyKey;
            }

            /*$this->cacheManager->invalidatePath(
                "/service/DummyPurgeClientServiceId/purge/$tag",
                $headers
            );*/
        }
    }

    public function purgeAll()
    {
        $headers = ['Fastly-Soft-Purge' => '1'];
        $fastlyKey = 'DummyPurgeClientKey';
        if ($fastlyKey !== null ) {
            $headers['Fastly-Key'] = $fastlyKey;
        }

        /*$this->cacheManager->invalidatePath(
            '/service/DummyPurgeClientServiceId/purge_all',
            $headers
        );*/
    }

}
