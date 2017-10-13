<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use PaulGibbs\WordpressBehatExtension\Context\Awareness\CacheAwareContextTrait;

/**
 * Provides step definitions for managing plugins and themes.
 */
class SiteContext extends RawWordpressContext
{
    use CacheAwareContextTrait;

    /**
     * Clear object cache.
     *
     * Example: When the cache is cleared
     * Example: Given the cache has been cleared
     *
     * @When the cache is cleared
     * @Given the cache has been cleared
     */
    public function cacheIsCleared()
    {
        $this->clearCache();
    }
}
