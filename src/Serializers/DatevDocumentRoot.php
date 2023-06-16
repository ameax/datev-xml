<?php

namespace Ameax\Datev\Serializers;

use Ameax\Datev\Generators\DatevDocumentXmlGenerator;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

class DatevDocumentRoot implements XmlSerializable
{
    public array $value;

    private DatevDocumentXmlGenerator $wrapper;

    public function __construct(array $value, DatevDocumentXmlGenerator $wrapper)
    {
        $this->value = $value;
        $this->wrapper = $wrapper;
    }

    public function xmlSerialize(Writer $writer): void
    {
        $writer->writeAttribute($this->wrapper->xsi.'schemaLocation', 'http://xml.datev.de/bedi/tps/document/v06.0 Document_v060.xsd');
        $writer->writeAttribute('version', '6.0');
        $writer->writeAttribute('generatingSystem', 'ameax Unternehmenssoftware');
        $writer->write($this->value);
    }
}
