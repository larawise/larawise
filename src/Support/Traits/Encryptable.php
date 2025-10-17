<?php

namespace Larawise\Support\Traits;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Str;

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
trait Encryptable
{
    /**
     * The encrypter instance.
     *
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * The encrypt state.
     *
     * @var bool
     */
    protected $encrypt = false;

    /**
     * Keys that should be encrypted.
     *
     * @var array<string>
     */
    protected $encryptOnly = [];

    /**
     * Decrypt a given payload using encrypter.
     *
     * @param string $payload
     * @param bool $unserialize
     *
     * @return mixed
     */
    protected function decrypt($payload, $unserialize = true)
    {
        return $this->getEncrypter()->decrypt($payload, $unserialize);
    }

    /**
     * Encrypt a given value using encrypter.
     *
     * @param mixed $value
     * @param bool $serialize
     *
     * @return string
     */
    protected function encrypt($value, $serialize = true)
    {
        return $this->getEncrypter()->encrypt($value, $serialize);
    }

    /**
     * Determine whether the given identifier should be encrypted.
     *
     * @param string $identifier
     *
     * @return bool
     */
    protected function shouldBeEncrypted($identifier)
    {
        // If no patterns are defined, encrypt everything
        if (empty($this->encryptOnly)) {
            return true;
        }

        foreach ($this->encryptOnly as $pattern) {
            // Check if the identifier matches the current pattern (supports wildcards)
            if (Str::is($pattern, $identifier)) {
                // Match found — encryption should be applied
                return true;
            }
        }

        // No match — skip encryption for this identifier
        return false;
    }

    /**
     * Conditionally decrypt the given value if encryption is enabled.
     *
     * @param string $identifier
     * @param mixed $value
     * @param bool $unserialize
     *
     * @return mixed
     */
    protected function performDecrypt($identifier, $value, $unserialize = true)
    {
        return $this->encrypt && $this->shouldBeEncrypted($identifier)
            ? $this->performSafeDecrypt($value, $unserialize)
            : $value;
    }

    /**
     * Conditionally encrypt the given value if encryption is enabled.
     *
     * @param string $identifier
     * @param mixed $value
     * @param bool $serialize
     *
     * @return mixed
     */
    protected function performEncrypt($identifier, $value, $serialize = true)
    {
        return $this->encrypt && $this->shouldBeEncrypted($identifier)
            ? $this->encrypt($value)
            : $value;
    }

    /**
     * Attempt to decrypt the given value safely.
     *
     * @param mixed $value
     * @param bool $unserialize
     *
     * @return mixed
     */
    protected function performSafeDecrypt($value, $unserialize)
    {
        try {
            return $this->decrypt($value, $unserialize);
        } catch (DecryptException $e) {
            // Invalid payload — fallback to original value
            return $value;
        }
    }

    /**
     * Get the current encrypter instance.
     *
     * @return Encrypter
     */
    public function getEncrypter()
    {
        return $this->encrypter;
    }

    /**
     * Set the encrypter instance.
     *
     * @param Encrypter $encrypter
     *
     * @return void
     */
    public function setEncrypter($encrypter)
    {
        $this->encrypter = $encrypter;
    }
}
