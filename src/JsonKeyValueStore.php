<?php

namespace JordJD\JsonKeyValueStore;

class JsonKeyValueStore
{
    /** @var string */
    private $file;

    /** @var mixed */
    private $content;

    /**
     * Constructor.
     *
     * @param string $file
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->load();
    }

    /**
     * Create empty store.
     *
     * @return void
     */
    private function createEmptyStore()
    {
        $this->content = new \stdClass();
        $this->save();
    }

    /**
     * Load contents from store.
     *
     * @throws Exception
     *
     * @return void
     */
    private function load()
    {
        if (!file_exists($this->file)) {
            $this->createEmptyStore();
        }

        $fh = fopen($this->file, 'r+');

        if (flock($fh, LOCK_SH)) {
            $rawContent = fread($fh, filesize($this->file));
            fflush($fh);
            flock($fh, LOCK_UN);
            fclose($fh);
        } else {
            throw new \Exception('Could not acquire shared file lock.');
        }

        $this->content = json_decode(gzdecode($rawContent));

        if ($this->content === null) {
            throw new \Exception('Invalid store content');
        }
    }

    /**
     * Save contents to store.
     *
     * @throws Exception
     *
     * @return void
     */
    private function save()
    {
        $encodedContent = gzencode(json_encode($this->content));

        if (!file_exists($this->file)) {
            touch($this->file);
        }

        $fh = fopen($this->file, 'r+');

        if (flock($fh, LOCK_EX)) {
            ftruncate($fh, 0);
            fwrite($fh, $encodedContent);
            fflush($fh);
            flock($fh, LOCK_UN);
            fclose($fh);
        } else {
            throw new \Exception('Could not acquire exclusive file lock.');
        }
    }

    /**
     * Get file path.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set content with key and value.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->content->$key = $value;
        $this->save();
    }

    /**
     * Get value with specific key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->content->$key)) {
            return;
        }

        return $this->content->$key;
    }

    /**
     * Delete value with specific key.
     *
     * @return null
     */
    public function delete($key)
    {
        unset($this->content->$key);
    }
}
