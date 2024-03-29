<?php


namespace CaptainKant\Generics\Core;


use CaptainKant\Generics\Exceptions\GenericPathNotFoundException;

/**
 * @noinspection PhpUnused
 */
class GenericFile
{
    private $filePath;

    /**
     * @throws GenericPathNotFoundException
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new GenericPathNotFoundException("file $filePath not found");
        }
        $this->filePath = $filePath;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getExtension()
    {
        $tabPathInfo = pathinfo($this->filePath);

        if (isset($tabPathInfo['extension'])) {
            return $tabPathInfo['extension'] ;
        }
        return '';
    }

    public function isExtension($extension): bool
    {
        return strtoupper($extension) === strtoupper($this->getExtension());
    }

    /**
     * @noinspection PhpUnused
     */
    public function getFullPath(): string
    {
        return $this->filePath;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getBaseName(): string
    {
        $tabPathInfo = pathinfo($this->filePath);
        return $tabPathInfo['basename'];
    }

}
