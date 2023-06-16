<?php

namespace Ameax\Datev\Serializers;

use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

class DatevDocumentRoot implements XmlSerializable
{
    public $value;

    private $wrapper;

    public function __construct($value, $wrapper)
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
