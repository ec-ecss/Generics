<?php

namespace CaptainKant\Generics\Cleaners;

use CaptainKant\Generics\Exceptions\GenericXmlStringHandlerException;
use CaptainKant\Generics\Interfaces\GenericAutowiringServiceInterface;
use CaptainKant\Generics\Traits\GenericAutowiringServiceTrait;
use LibXMLError;
use SimpleXMLElement;

class GenericXmlStringHandler implements GenericAutowiringServiceInterface
{
    use GenericAutowiringServiceTrait;

    /**
     * @var SimpleXMLElement
     */
    private $sxDocument;

    /**
     * @var string
     */
    private $errors = '';

    /**
     * @return SimpleXMLElement|null
     */
    public function getSxDocument()
    {
        return $this->sxDocument;
    }

    public function loadFile(string $fileUri)
    {
        $this->load(file_get_contents($fileUri));
    }

    /**
     * Extract valid xml in parasites
     * @param string $strXml
     * @return string
     * @throws GenericXmlStringHandlerException
     */
    public function load(string $strXml = null)
    {
        if (false !== ($okXml = $this->loadXmlContents($strXml))) {
            return $strXml;
        } else if ($this->wasErrorNotUTF8()) {
            if (false !== ($okXml = $this->loadXmlContents($this->forceToUTF8($strXml)))) {
                return $okXml;
            }
            throw new GenericXmlStringHandlerException($this->errors);
        } else if ($this->wasErrorOpeningTag()) {
            if (false !== ($okXml = $this->loadXmlContents($this->extractInnerTag($strXml)))) {
                return $okXml;
            }
            throw new GenericXmlStringHandlerException($this->errors);
        }
        throw new GenericXmlStringHandlerException($this->errors);
    }

    /**
     * @param $tentativeXml
     * @return false|string
     */
    private function loadXmlContents($tentativeXml)
    {
        libxml_use_internal_errors(false);
        libxml_use_internal_errors(true);
        $tentativeXml = utf8_encode($tentativeXml);
        if ($this->sxDocument = @simplexml_load_string($tentativeXml)) {
            return $tentativeXml;
        }
        $this->errors .= implode(',', array_map(function (LibXMLError $error) {
            return $error->message;
        }, libxml_get_errors()));
        return false;
    }

    private function wasErrorNotUTF8()
    {
        return false !== strpos((string)$this->errors, 'Input is not proper UTF-8');
    }

    /**
     * @param $tentativeXml
     * @return string
     */
    private function forceToUTF8($tentativeXml)
    {
        return utf8_encode($tentativeXml);
    }

    private function wasErrorOpeningTag()
    {
        return false !== strpos((string)$this->errors, 'Start tag expected');
    }

    /**
     * @param $tentativeXml
     * @return string
     */
    private function extractInnerTag($tentativeXml)
    {
        $messageRaw = strstr($tentativeXml, '<?xml');
        $closingTagPos = strrpos($messageRaw, '>', -1);
        return substr($messageRaw, 0, $closingTagPos + 1);
    }

    /**
     * @param string|null $strXml
     * @return string
     * @throws GenericXmlStringHandlerException
     */
    public function __invoke(string $strXml = null)
    {
        return $this->load($strXml);
    }


}