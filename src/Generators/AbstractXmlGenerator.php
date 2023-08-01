<?php

namespace Ameax\Datev\Generators;

use Ameax\Datev\DatevHelpers;
use Ameax\XmlValidator\XmlValidator;
use Sabre\Xml\Service;

abstract class AbstractXmlGenerator
{
    protected Service $service;

    protected string $xml;

    public string $xsi = '{http://www.w3.org/2001/XMLSchema-instance}';

    public string $ns = '';

    public function __construct()
    {
        $this->service = new Service();
        $this->service->namespaceMap[DatevHelpers::cleanXmlNamespace($this->ns)] = '';
        $this->service->namespaceMap[DatevHelpers::cleanXmlNamespace($this->xsi)] = 'xsi';
    }

    /**
     * @throws \Exception
     */
    public function validate(): bool
    {
        $validator = XmlValidator::validateString($this->xml, $this->getXsdPath());
        $validator->throwExceptionOnErrors();

        return $validator->isValid();
    }

    public function getXmlString(): string
    {
        return $this->xml;
    }

    abstract public function generate(): self;

    abstract protected function getXsdPath(): string;
}
