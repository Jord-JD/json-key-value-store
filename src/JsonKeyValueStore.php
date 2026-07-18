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

        if ($fh === false) {
            throw new \Exception('Could not open store for reading.');
        }

        if (flock($fh, LOCK_SH)) {
            $rawContent = stream_get_contents($fh);
            fflush($fh);
            flock($fh, LOCK_UN);
            fclose($fh);
        } else {
            fclose($fh);
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
        $json = json_encode($this->content);

        if ($json === false) {
            throw new \Exception('Could not encode store content as JSON.');
        }

        $encodedContent = gzencode($json);

        if ($encodedContent === false) {
            throw new \Exception('Could not compress store content.');
        }

        if (!file_exists($this->file)) {
            $directory = dirname($this->file);

            if (!is_dir($directory)) {
                throw new \Exception('Store directory does not exist.');
            }

            if (!touch($this->file)) {
                throw new \Exception('Could not create store file.');
            }
        }

        $fh = fopen($this->file, 'r+');

        if ($fh === false) {
            throw new \Exception('Could not open store for writing.');
        }

        if (flock($fh, LOCK_EX)) {
            ftruncate($fh, 0);
            fwrite($fh, $encodedContent);
            fflush($fh);
            flock($fh, LOCK_UN);
            fclose($fh);
        } else {
            fclose($fh);
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
        $this->save();
    }
}
