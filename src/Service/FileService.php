<?php

namespace Larawise\Service;

use Illuminate\Filesystem\FilesystemAdapter;

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
class FileService
{
    /**
     * Create a new file service instance.
     *
     * @param FilesystemAdapter $storage
     */
    public function __construct(
        protected FilesystemAdapter $storage
    ) { }

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param mixed $content
     * @param bool $json
     *
     * @return bool
     */
    public function put($path, $content, $json = true)
    {
        return $this->storage->put($path, $json
            ? json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL
            : $content);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @param bool $convert
     *
     * @return mixed
     */
    public function read($path, $convert = true)
    {
        // Retrieve the file contents
        $content = $this->storage->get($path);

        // If the file is empty, return an empty array or null
        if (empty($content)) {
            return $convert ? [] : null;
        }

        // Return decoded JSON or raw content based on $convert flag
        return $convert ? json_decode($content, true) : $content;
    }

    /**
     * Delete the file at a given path.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return $this->storage->delete($path);
    }

    /**
     * Move a file to a new location.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function move($from, $to)
    {
        return $this->storage->move($from, $to);
    }

    /**
     * Determine if a file or directory exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return $this->storage->exists($path);
    }
}
