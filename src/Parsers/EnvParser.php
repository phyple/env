<?php

namespace Phyple\Env\Parsers;

use Phyple\Env\Exceptions\EnvironmentFileNotFoundException;
use Phyple\Env\Facades\Env;
use Phyple\Env\Interfaces\ParserInterface;

class EnvParser implements ParserInterface
{
    /**
     * Delimiter to open variable references in the values.
     *
     * @var string $variable_delimiter_open
     */
    protected string $variable_delimiter_open = '${';

    /**
     * Delimiter to close variable references in the values.
     *
     * @var string $variable_delimiter_close
     */
    protected string $variable_delimiter_close = '}';

    /**
     * Token to tell the parser that the line is a comment
     *
     * @var string $comment_token
     */
    protected string $comment_token = '#';

    /**
     * Character used to trimming key and value
     *
     * @var string $trim_character
     */
    protected string $trim_character = " '\"\t\n\r\0\x0B";

    /**
     * Load environment variable from file
     *
     * @param string $target_file
     * @return self
     */
    public function loadFromFile(string $target_file): self
    {
        if (!is_readable($target_file)) {
            throw new EnvironmentFileNotFoundException("Environment File Not Found !: $target_file");
        }

        return $this->readEnvironmentFile($target_file);
    }

    /**
     * Read given environment file and parse it into variable
     *
     * @param string $target_file
     * @return $this
     */
    protected function readEnvironmentFile(string $target_file): self
    {
        $contents = file_get_contents($target_file);
        $lines = explode("\n", $contents);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($this->isLineEmptyOrCommented($line)) continue;
            list($key, $value) = $this->translateLineToVariableKeyAndValue($line);
            Env::addEnv($key, $value);
        }

        return $this;
    }

    /**
     * Check if the line is empty or a comment
     * @param string $line
     * @return bool
     */
    protected function isLineEmptyOrCommented(string $line): bool
    {
        return empty($line) || str_starts_with($line, $this->comment_token);
    }

    /**
     * Translate given line into variable key and value
     *
     * @param string $line
     * @return array
     */
    protected function translateLineToVariableKeyAndValue(string $line): array
    {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key, $this->trim_character);
        $value = trim($value, $this->trim_character);

        if ($this->isVariableReference($value)) {
            $value = $this->translateVariableReference($value);
        } else {
            $json_decoded_value = json_decode($value, true);
            $value = is_null($json_decoded_value) ? $value : $json_decoded_value;
        }

        return [$key, $value];
    }

    /**
     * Check if this variable is a reference to another variable
     *
     * @param string $value
     * @return bool
     */
    protected function isVariableReference(string $value): bool
    {
        return str_contains($value, $this->variable_delimiter_open);
    }

    /**
     * Translate if this variable is a reference to another variable
     *
     * @param string $value
     * @return string
     */
    protected function translateVariableReference(string $value): mixed
    {
        preg_match_all($this->getVariableReferencePattern(), $value, $matches);

        foreach ($matches[1] as $match) {
            $value = Env::getEnv($match);
        }

        return $value;
    }

    /**
     * Get Variable Reference Pattern
     *
     * @return string
     */
    protected function getVariableReferencePattern(): string
    {
        return '/\\' . $this->variable_delimiter_open . '([A-Za-z0-9_]+)' . $this->variable_delimiter_close . '/';
    }

    /**
     * Set Delimiter to open and close the variable references in the values.
     *
     * @param string $opening
     * @param string $closing
     * @return $this
     */
    public function setVariableDelimiter(string $opening, string $closing): self
    {
        $this->variable_delimiter_open = $opening;
        $this->variable_delimiter_close = $closing;
        return $this;
    }

    /**
     * Set new trim character property
     *
     * @param string $trim_character
     * @return $this
     */
    public function setTrimCharacter(string $trim_character): self
    {
        $this->trim_character = $trim_character;

        return $this;
    }

    /**
     * Set new comment token property
     *
     * @param string $comment_token
     * @return $this
     */
    public function setCommentToken(string $comment_token): self
    {
        $this->comment_token = $comment_token;

        return $this;
    }
}