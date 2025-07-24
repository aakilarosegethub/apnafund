<?php

namespace App\Lib;

use Exception;
use InvalidArgumentException;
use App\Constants\FileDetails;
use Intervention\Image\Facades\Image;

class FileManager
{
    /**
     * The file which will be uploaded
     *
     *
     * @var object
     */
    protected mixed $file;

    /**
     * The path where will be uploaded
     *
     * @var string
     */
    public string $path;

    /**
     * The size, if the file is image
     *
     * @var null|string
     */
    public ?string $size = null;

    /**
     * Check the file is image or not
     *
     * @var boolean
     */
    protected bool $isImage;

    /**
     * Thumbnail version size, if required
     * and if the file is image
     *
     * @var null|string
     */
    public ?string $thumb = null;

    /**
     * Old filename, which will be removed
     *
     * @var null|string
     */
    public ?string $old = null;

    /**
     * Current filename, which is uploading
     *
     * @var string
     */
    public string $filename;

    /**
     * Set the file and file type to properties if exist
     *
     * @param $file
     * @return void
     */
    function __construct($file = null)
    {
        $this->file = $file;

        if ($file) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];

            if (in_array($file->getClientOriginalExtension(), $imageExtensions)) {
                $this->isImage = true;
            } else {
                $this->isImage = false;
            }
        }
    }

    /**
     * File upload process
     *
     * @return void
     * @throws Exception
     */
    function upload(): void
    {
        //create the directory if doesn't exist
        $path = $this->makeDirectory();

        if (!$path) throw new Exception('File could not be created.');

        //remove the old file if exist
        if ($this->old) $this->removeFile();

        //get the filename
        $this->filename = $this->getFileName();

        //upload file or image
        if ($this->isImage) $this->uploadImage();
        else $this->uploadFile();
    }

    /**
     * Upload the file if this is image
     *
     * @return void
     */
    protected function uploadImage(): void
    {
        $image = Image::make($this->file);

        //resize the
        if ($this->size) {
            $size = explode('x', strtolower($this->size));
            $image->resize($size[0], $size[1]);
        }

        //save the image
        $image->save($this->path . '/' . $this->filename);

        //save the image as thumbnail version
        if ($this->thumb) {
            if ($this->old) $this->removeFile($this->path . '/thumb_' . $this->old);

            $thumb = explode('x', $this->thumb);
            Image::make($this->file)->resize($thumb[0], $thumb[1])->save($this->path . '/thumb_' . $this->filename);
        }
    }

    /**
     * Upload the file if this is not an image
     *
     * @return void
     */
    protected function uploadFile(): void
    {
        $this->file->move($this->path, $this->filename);
    }

    /**
     * Make directory doesn't exist
     * Developer can also call this method statically
     *
     * @param $location
     * @return boolean|string
     */
    function makeDirectory($location = null): bool|string
    {
        if (!$location) $location = $this->path;
        if (file_exists($location)) return true;

        return mkdir($location, 0755, true);
    }

    /**
     * Remove all directory inside the location
     * Developer can also call this method statically
     *
     * @param $location
     * @return void
     */
    function removeDirectory($location = null): void
    {
        if (!$location) $location = $this->path;

        if (!is_dir($location)) throw new InvalidArgumentException("$location must be a directory");

        if (substr($location, strlen($location) - 1, 1) != '/') $location .= '/';

        $files = glob($location . '*', GLOB_MARK);

        foreach ($files as $file) {
            if (is_dir($file)) static::removeDirectory($file);
            else unlink($file);
        }

        rmdir($location);
    }

    /**
     * Remove the file if exists
     * Developer can also call this method statically
     *
     * @param $path
     * @return void
     */
    function removeFile($path = null): void
    {
        if (!$path) $path = $this->path . '/' . $this->old;

        file_exists($path) && is_file($path) && @unlink($path);

        if ($this->thumb) {
            if (!$path) $path = $this->path . '/thumb_' . $this->old;

            file_exists($path) && is_file($path) && @unlink($path);
        }
    }

    /**
     * Generating the filename which is uploading
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return uniqid() . time() . '.' . $this->file->getClientOriginalExtension();
    }

    /**
     * Get access of array from fileInfo method as non-static method.
     * Also get some others method
     *
     * @return string|void
     */
    function __call($method, $args)
    {
        $fileInfo  = new FileDetails;
        $filePaths = $fileInfo->fileDetails();

        if (array_key_exists($method, $filePaths)) {
            return json_decode(json_encode($filePaths[$method]));
        } else {
            $this->$method(...$args);
        }
    }

    /**
     * Get access some non-static method as static method
     *
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        $selfClass = new FileManager;
        $selfClass->$method(...$args);
    }
}
