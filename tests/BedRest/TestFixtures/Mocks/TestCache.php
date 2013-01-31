<?php

namespace BedRest\TestFixtures\Mocks;

use Doctrine\Common\Cache\Cache;

/**
 * BedRest\TestFixtures\Mocks\TestCache
 *
 * A simple cache, good for testing.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class TestCache implements Cache
{
    /**
     * Records the number of cache hits.
     * @var int
     */
    protected $hits = 0;

    /**
     * Records the number of cache misses.
     * @var int
     */
    protected $misses = 0;

    /**
     * Cache data store.
     * @var array
     */
    protected $data = array();

    /**
     * Fetches an entry from the cache.
     *
     * @param  string $id cache id The id of the cache entry to fetch.
     * @return mixed  The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id)
    {
        if (!isset($this->data[$id])) {
            $this->misses++;

            return false;
        }

        $this->hits++;

        return $this->data[$id];
    }

    /**
     * Test if an entry exists in the cache.
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return isset($this->data[$id]);
    }

    /**
     * Puts data into the cache.
     *
     * @param  string  $id       The cache id.
     * @param  mixed   $data     The cache entry/data.
     * @param  int     $lifeTime Ignored.
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = $data;

        return true;
    }

    /**
     * Deletes a cache entry.
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    public function delete($id)
    {
        unset($this->data[$id]);

        return true;
    }

    /**
     * Returns the cache data.
     *
     * @return array
     */
    public function getCacheData()
    {
        return $this->data;
    }

    /**
     * Retrieves statistics.
     *
     * @return array
     */
    public function getStats()
    {
        return array(
            'hits' => $this->hits,
            'misses' => $this->misses,
            'uptime' => null,
            'memory_usage' => null,
            'memory_available' => null
        );
    }
}
