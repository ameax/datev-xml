<?php

namespace Ameax\Datev\DataObjects;

class DatevRepositoryData
{
    protected array $replacementArray = [];

    public function __construct(
        public string $level1 = 'ameax',
        public string $level2 = 'Import {year} {typeShort}',
        public string $level3 = '{month}',
    ) {
    }

    public static function make(
        string $level1 = 'ameax',
        string $level2 = 'Import {year} {typeShort}',
        string $level3 = '{month}'
    ): DatevRepositoryData {
        return new DatevRepositoryData($level1, $level2, $level3);
    }

    public function getLevel(int $level, array $data): string
    {
        $string = match ($level) {
            1 => $this->level1,
            2 => $this->level2,
            3 => $this->level3,
            default => ''
        };
        $this->buildReplacementArray($data);

        return $this->replace($string);
    }

    private function replace(string $string): string
    {
        return str_replace($this->getReplacementKeys(), $this->getReplacementValues(), $string);
    }

    private function getReplacementKeys(): array
    {
        $keys = [];
        foreach (array_keys($this->replacementArray) as $key) {
            $keys[] = '{'.$key.'}';
        }

        return $keys;
    }

    private function getReplacementValues(): array
    {
        return array_values($this->replacementArray);
    }

    private function buildReplacementArray(array $data): void
    {
        $this->replacementArray = [
            'year' => $data['date']->format('Y'),
            'month' => $data['date']->format('m'),
            'type' => DatevRepositoryData::getTypeLabel($data['type']),
            'typeShort' => DatevRepositoryData::getTypeShortLabel($data['type']),
        ];
    }

    public static function getTypeLabel(string $type): string
    {
        return match ($type) {
            DatevDocumentData::TYPE_ACCOUNTS_PAYABLE_LEDGER => 'Eingangsrechnungen',
            DatevDocumentData::TYPE_ACCOUNTS_RECEIVABLE_LEDGER => 'Ausgangsrechnungen',
            DatevDocumentData::TYPE_CASH_LEDGER => 'Kasse',
            DatevDocumentData::TYPE_FILE => 'Dateien',
            DatevDocumentData::TYPE_SEPA_FILE => 'Sepa-Dateien',
            default => ''
        };
    }

    public static function getTypeShortLabel(string $type): string
    {
        return match ($type) {
            DatevDocumentData::TYPE_ACCOUNTS_PAYABLE_LEDGER => 'RE',
            DatevDocumentData::TYPE_ACCOUNTS_RECEIVABLE_LEDGER => 'RA',
            DatevDocumentData::TYPE_CASH_LEDGER => 'Kasse',
            DatevDocumentData::TYPE_FILE => 'Dateien',
            DatevDocumentData::TYPE_SEPA_FILE => 'Sepa',
            default => ''
        };
    }
}
