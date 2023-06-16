<?php

namespace Ameax\Datev\Serializers;

use Ameax\Datev\Generators\DatevAccountLedgerXmlGenerator;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

class DatevAccountLedgerRoot implements XmlSerializable
{
    public array $value;

    private DatevAccountLedgerXmlGenerator $generator;

    public function __construct(array $value, DatevAccountLedgerXmlGenerator $generator)
    {
        $this->value = $value;
        $this->generator = $generator;
    }

    public function xmlSerialize(Writer $writer): void
    {
        $writer->writeAttribute($this->generator->xsi.'schemaLocation', 'http://xml.datev.de/bedi/tps/ledger/v060 Belegverwaltung_online_ledger_import_v060.xsd');
        $writer->writeAttribute('version', '6.0');
        $writer->writeAttribute('generator_info', $this->generator->getDatevAccountLedgerData()->generator_info ?? 'ameax');
        $writer->writeAttribute('generating_system', $this->generator->getDatevAccountLedgerData()->generating_system ?? 'ameax Unternehmenssoftware');
        $writer->writeAttribute('xml_data', 'Kopie nur zur Verbuchung berechtigt nicht zum Vorsteuerabzug');
        $writer->write($this->value);
    }
}
