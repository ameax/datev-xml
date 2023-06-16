<?php

namespace Ameax\Datev;

use Spatie\TemporaryDirectory\Exceptions\PathAlreadyExists;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use ZipArchive;

class Zip
{
    public TemporaryDirectory $temporaryDirectory;

    public ZipArchive         $zipArchive;

    protected string          $zipPath;

    /**
     * @throws PathAlreadyExists
     */
    public function __construct()
    {
        $this->temporaryDirectory = new TemporaryDirectory();
        $this->temporaryDirectory->name('ameax-datev')->force()->create();
        $this->zipArchive = new ZipArchive();
        $this->zipPath = $this->temporaryDirectory->path('datev.zip');
        $this->zipArchive->open($this->zipPath, ZipArchive::CREATE);
    }

    public function __destruct()
    {
        //        $this->temporaryDirectory->delete();
    }

    public function addFile($path, $pathAndFilenameInZip): static
    {
        $this->zipArchive->addFile($path, $pathAndFilenameInZip);

        return $this;
    }

    public function addFromString($content, $pathAndFilenameInZip): static
    {
        $this->zipArchive->addFromString($pathAndFilenameInZip, $content);

        return $this;
    }

    public function close(): static
    {
        $this->zipArchive->close();

        return $this;
    }

    public function getZipPath(): string
    {
        return $this->zipPath;
    }
}
