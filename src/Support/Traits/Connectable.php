<?php

namespace Larawise\Support\Traits;

use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface as ConnectionResolver;
use Predis\Client;
use Throwable;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
trait Connectable
{
    /**
     * The config repository instance.
     *
     * @var ConnectionResolver
     */
    protected $db;

    /**
     * The redis factory instance.
     *
     * @var RedisFactory
     */
    protected $redis;

    /**
     * The database connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * The database connection state of the settings.
     *
     * @var bool
     */
    protected $connected;

    /**
     * Check if the database connection is valid and the target table exists.
     *
     * @return bool
     */
    protected function isConnected()
    {
        try {
            // If Redis is not set, assume we're using a database driver
            if (! $this->redis) {
                // Attempt to get a PDO instance from the database connection
                // If successful, the connection is considered valid
                return $this->db()->getPdo();
            }

            // Otherwise, we're using Redis — send a ping command
            $response = $this->redis()->ping();

            // Acceptable responses: true, "PONG", or casted "PONG" string
            return $response === true || $response === 'PONG' || (string) $response === 'PONG';
        } catch (Throwable) {
            // If any exception occurs (e.g. connection failure, misconfiguration),
            // treat the connection as invalid
            return false;
        }
    }

    /**
     * Get the current connection.
     *
     * @return ConnectionInterface
     */
    public function db()
    {
        return $this->db->connection($this->connection);
    }

    /**
     * Get the current Redis connection.
     *
     * @return Client
     */
    public function redis()
    {
        return $this->redis->connection($this->connection)->client();
    }

    /**
     * Get the current connection name.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * Set the current connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setConnectionName($name)
    {
        $this->connection = $name;
    }

    /**
     * Get the current event dispatcher instance.
     *
     * @return ConnectionResolver
     */
    public function getConnectionResolver()
    {
        return $this->db;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param ConnectionResolver $connectionResolver
     *
     * @return void
     */
    public function setConnectionResolver($connectionResolver)
    {
        $this->db = $connectionResolver;
    }

    /**
     * Get the Redis prefix option.
     *
     * @return string
     */
    public function getRedisPrefix()
    {
        return $this->config->get('database.redis.options.prefix');
    }

    /**
     * Get the Redis factory instance.
     *
     * @return RedisFactory
     */
    public function getRedisFactory()
    {
        return $this->redis;
    }

    /**
     * Set the Redis factory instance.
     *
     * @param RedisFactory $factory
     *
     * @return void
     */
    public function setRedisFactory($factory)
    {
        $this->redis = $factory;
    }
}
