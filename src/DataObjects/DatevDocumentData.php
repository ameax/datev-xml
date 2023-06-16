<?php

namespace Ameax\Datev\DataObjects;

use Ameax\Datev\Generators\DatevDocumentXmlGenerator;
use Ameax\Datev\Zip;
use Carbon\Carbon;

class DatevDocumentData
{
    public const TYPE_ACCOUNTS_PAYABLE_LEDGER = 'accountsPayableLedger';

    public const TYPE_ACCOUNTS_RECEIVABLE_LEDGER = 'accountsReceivableLedger';

    public const TYPE_CASH_LEDGER = 'cashLedger';

    public const TYPE_FILE = 'File';

    public const TYPE_SEPA_FILE = 'SEPAFile';

    protected array $data = [];

    protected ?string $description;

    protected Zip $zip;

    public function __construct(
        public ?Carbon $date = null
    ) {
        if (! isset($this->date)) {
            $this->date = Carbon::make('now');
        }
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    public function addAccountsPayableLedger(
        string $nameWithoutExtension,
        string $xmlContent,
        Carbon $date,
        array $filePaths = [],
        ?DatevRepositoryData $datevRepositoryData = null,
    ): self {

        $document = [
            'type' => self::TYPE_ACCOUNTS_PAYABLE_LEDGER,
            'name' => $nameWithoutExtension,
            'xml' => $xmlContent,
            'date' => $date,
            'filePaths' => $filePaths,
            'datevRepositoryData' => $datevRepositoryData,
        ];

        $this->data[] = $document;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function buildAccountsPayableLedger(
        DatevAccountLedgerData $datevAccountLedgerData,
        array $filePaths = [],
        ?DatevRepositoryData $datevRepositoryData = null,
    ): self {
        $xmlContent = $datevAccountLedgerData->generateXml();

        $document = [
            'type' => self::TYPE_ACCOUNTS_PAYABLE_LEDGER,
            'name' => $datevAccountLedgerData->consolidatedInvoiceId,
            'xml' => $xmlContent,
            'date' => $datevAccountLedgerData->consolidatedDate,
            'filePaths' => $filePaths,
            'datevRepositoryData' => $datevRepositoryData,
        ];

        $this->data[] = $document;

        return $this;
    }

    public function addAccountsReceivableLedger(
        string $nameWithoutExtension,
        string $xmlContent,
        Carbon $date,
        array $filePaths = [],
        ?DatevRepositoryData $datevRepositoryData = null
    ): self {

        $document = [
            'type' => self::TYPE_ACCOUNTS_RECEIVABLE_LEDGER,
            'name' => $nameWithoutExtension,
            'xml' => $xmlContent,
            'date' => $date,
            'filePaths' => $filePaths,
            'datevRepositoryData' => $datevRepositoryData,
        ];

        $this->data[] = $document;

        return $this;
    }

    public function addCashLedger(
        string $nameWithoutExtension,
        string $xmlContent,
        Carbon $date,
        string $cashAccountNumber,
        array $filePaths = [],
        ?DatevRepositoryData $datevRepositoryData = null
    ): self {
        $document = [
            'type' => self::TYPE_CASH_LEDGER,
            'name' => $nameWithoutExtension,
            'xml' => $xmlContent,
            'date' => $date,
            'cashAccountNumber' => $cashAccountNumber,
            'filePaths' => $filePaths,
            'datevRepositoryData' => $datevRepositoryData,
        ];

        $this->data[] = $document;

        return $this;
    }

    public function addFile(
        string $nameWithExtension,
        string $filePath,
        ?Carbon $date,
        ?DatevRepositoryData $datevRepositoryData = null
    ): self {
        $document = [
            'type' => self::TYPE_FILE,
            'name' => $nameWithExtension,
            'date' => $date ?? new Carbon('now'),
            'filePath' => $filePath,
            'datevRepositoryData' => $datevRepositoryData,
        ];

        $this->data[] = $document;

        return $this;
    }

    public function addSEPAFile(
        string $nameWithExtension,
        string $filePath,
        ?Carbon $date,
        ?DatevRepositoryData $datevRepositoryData = null
    ): self {
        $document = [
            'type' => self::TYPE_SEPA_FILE,
            'name' => $nameWithExtension,
            'date' => $date ?? new Carbon('now'),
            'filePath' => $filePath,
            'datevRepositoryData' => $datevRepositoryData,
        ];
        $this->data[] = $document;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @throws \Exception
     */
    public function generateXml(): string
    {
        $generator = new DatevDocumentXmlGenerator();
        $generator->setDatevDocumentData($this);
        $generator->generate();
        $generator->validate();

        return $generator->getXmlString();
    }

    public function generateZip()
    {
        $this->zip = new Zip();
        $this->zip->addFromString($this->generateXml(), 'document.xml');
        foreach ($this->data as $dataRow) {
            switch ($dataRow['type']) {
                case self::TYPE_ACCOUNTS_RECEIVABLE_LEDGER:
                case self::TYPE_ACCOUNTS_PAYABLE_LEDGER:
                case self::TYPE_CASH_LEDGER:
                    $this->zip->addFromString($dataRow['xml'], $dataRow['name'].'.xml');
                    $this->addAdditionalFiles($dataRow['filePaths']);
                    break;
                case self::TYPE_SEPA_FILE:
                case self::TYPE_FILE:
                    $this->zip->addFile($dataRow['filePath'], $dataRow['name']);
                    break;
            }
        }
        $this->zip->close();

        return $this->zip->getZipPath();
    }

    private function addAdditionalFiles($filePaths): void
    {
        foreach ($filePaths as $filePath) {
            $this->zip->addFile($filePath, basename($filePath));
        }
    }
}
