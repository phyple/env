<?php

namespace Phyple\Env;

use Phyple\Env\Exceptions\ImmutableKeyException;
use Phyple\Env\Interfaces\ParserInterface;
use Phyple\Env\Parsers\EnvParser;
use Phyple\Essential\Patterns\Singleton;

class Env extends Singleton
{
    /**
     * File parser for this library
     *
     * @var ParserInterface $parser
     */
    protected ParserInterface $parser;

    /**
     * Set whether the value that has been defined can be changed
     *
     * @var bool $immutable
     */
    protected bool $immutable = true;
    /**
     * Flag to indicate whether the 'get' method should only use local environment variables or not.
     *
     * @var bool $local_only
     */
    protected bool $local_only = true;

    /**
     * Class constructor
     *
     * Set env parser as default parser
     */
    protected function __construct()
    {
        parent::__construct();

        $this->parser = new EnvParser();
    }

    /**
     * Get current parser. If parser given, change the parser and return it
     *
     * @param string|null $parser
     * @return ParserInterface
     */
    public function parser(?string $parser = null): ParserInterface
    {
        if (!is_null($parser)) {
            $this->parser = new $parser;
        }

        return $this->parser;
    }

    /**
     * local only property setter
     *
     * @param bool $local_only
     * @return $this
     */
    public function local_only(bool $local_only = true): self
    {
        $this->local_only = $local_only;
        return $this;
    }

    /**
     * immutable property setter
     *
     * @param bool $immutable
     * @return $this
     */
    public function immutable(bool $immutable = true): self
    {
        $this->immutable = $immutable;
        return $this;
    }

    /**
     * Add new key to environment value
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addEnv(string $key, mixed $value): self
    {
        if ($this->immutable && $this->getEnv($key)) {
            throw new ImmutableKeyException();
        }

        $_ENV[$key] = $value;

        return $this;
    }

    /**
     * Get environment value
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getEnv(string $key, mixed $default = null): mixed
    {
        return getenv($key, $this->local_only) ?: $_ENV[$key] ?? $default;
    }
}