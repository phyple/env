<?php

namespace Phyple\Env\Interfaces;

interface ParserInterface
{
    /**
     * Load environment variable from file
     *
     * @param string $target_file
     * @return self
     */
    public function loadFromFile(string $target_file): self;
}