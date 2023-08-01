<?php

namespace Ameax\Datev\DataObjects;

use Ameax\Datev\Generators\DatevAccountLedgerXmlGenerator;
use Carbon\Carbon;

class DatevAccountLedgerData
{
    protected array $accountsReceivableLedgers = [];

    protected array $accountsPayableLedgers = [];

    protected string $mode;

    const MODE_PAYABLE_LEDGER = 'payable';

    const MODE_RECEIVABLE_LEDGER = 'receivable';

    /**
     * consolidatedAmount is calculated by the AccountsReceivableLedgers
     */
    public float $consolidatedAmount = 0.0;

    public function __construct(
        public Carbon $consolidatedDate,
        public Carbon $consolidatedDeliveryDate,
        public string $consolidatedInvoiceId,
        public string $consolidatedCurrencyCode = 'EUR',
        public ?string $consolidatedOrderId = null,

        public ?string $customerName = null,
        public ?string $customerCity = null,
        public ?string $supplierName = null,
        public ?string $supplierCity = null,
        public ?string $ownVatId = null,
        public ?string $shipFromCountry = null,
        public ?string $partyId = null, //customer number
        public ?Carbon $paidAt = null,
        public ?string $internalInvoiceId = null,
        public ?string $vatId = null,
        public ?string $shipToCountry = null,
        public ?string $bankAccount = null,
        public ?string $bankCountry = null,
        public ?string $iban = null,
        public ?string $swiftCode = null,
        public ?string $accountName = null,
        public ?string $paymentConditionsId = null,
        public ?bool $paymentOrder = null,
        public ?Carbon $dueDate = null,
        public ?string $bpAccountNo = null,
        public ?string $costCategoryId = null,
        public ?string $costCategoryId2 = null,
        public ?string $generator_info = null,
        public ?string $generating_system = 'ameax\datev'
    ) {

    }

    public function addAccountsReceivableLedger(
        float $amount,
        string $accountNo,
        string $buCode = null,
        string $information = null,
        float $tax = null,
        Carbon $date = null,
        string $bookingText = null,
        string $currencyCode = null,
        float $exchangeRate = null,
        string $typeOfReceivable = null,
        float $costAmount = null,
        string $costCategoryId = null,
        string $costCategoryId2 = null,
        float $discountAmount = null,
        float $discountPercentage = null,
        Carbon $discountPaymentDate = null,
        Carbon $discountAmount2 = null,
        float $discountPercentage2 = null,
        Carbon $discountPaymentDate2 = null,
        Carbon $deliveryDate = null,
        string $orderId = null,

    ): self {
        $this->mode = self::MODE_RECEIVABLE_LEDGER;
        $this->consolidatedAmount += $amount;

        $this->accountsReceivableLedgers[] = [
            'amount' => $amount,
            'accountNo' => $accountNo,
            'buCode' => $buCode,
            'information' => $information,
            'tax' => $tax,
            'date' => $date,
            'bookingText' => $bookingText,
            'currencyCode' => $currencyCode,
            'typeOfReceivable' => $typeOfReceivable,
            'costAmount' => $costAmount,
            'costCategoryId' => $costCategoryId,
            'costCategoryId2' => $costCategoryId2,
            'discountAmount' => $discountAmount,
            'discountPercentage' => $discountPercentage,
            'discountPaymentDate' => $discountPaymentDate,
            'discountAmount2' => $discountAmount2,
            'discountPercentage2' => $discountPercentage2,
            'discountPaymentDate2' => $discountPaymentDate2,
            'deliveryDate' => $deliveryDate,
            'orderId' => $orderId,
            'exchangeRate' => $exchangeRate,
        ];

        return $this;
    }

    public function getAccountsReceivableLedgers(): array
    {
        return $this->accountsReceivableLedgers;
    }

    public function addAccountsPayableLedger(
        float $amount,
        string $accountNo,
        string $buCode = null,
        string $information = null,
        float $tax = null,
        Carbon $date = null,
        string $bookingText = null,
        string $currencyCode = null,
        float $exchangeRate = null,
        string $typeOfReceivable = null,
        float $costAmount = null,
        string $costCategoryId = null,
        string $costCategoryId2 = null,
        float $discountAmount = null,
        float $discountPercentage = null,
        Carbon $discountPaymentDate = null,
        Carbon $discountAmount2 = null,
        float $discountPercentage2 = null,
        Carbon $discountPaymentDate2 = null,
        Carbon $deliveryDate = null,
        string $orderId = null,

    ): self {
        $this->mode = self::MODE_PAYABLE_LEDGER;
        $this->consolidatedAmount += $amount;

        $this->accountsPayableLedgers[] = [
            'amount' => $amount,
            'accountNo' => $accountNo,
            'buCode' => $buCode,
            'information' => $information,
            'tax' => $tax,
            'date' => $date,
            'bookingText' => $bookingText,
            'currencyCode' => $currencyCode,
            'typeOfReceivable' => $typeOfReceivable,
            'costAmount' => $costAmount,
            'costCategoryId' => $costCategoryId,
            'costCategoryId2' => $costCategoryId2,
            'discountAmount' => $discountAmount,
            'discountPercentage' => $discountPercentage,
            'discountPaymentDate' => $discountPaymentDate,
            'discountAmount2' => $discountAmount2,
            'discountPercentage2' => $discountPercentage2,
            'discountPaymentDate2' => $discountPaymentDate2,
            'deliveryDate' => $deliveryDate,
            'orderId' => $orderId,
            'exchangeRate' => $exchangeRate,
        ];

        return $this;
    }

    public function getAccountsPayableLedgers(): array
    {
        return $this->accountsPayableLedgers;
    }

    /**
     * @throws \Exception
     */
    public function generateXml(): string
    {
        $generator = new DatevAccountLedgerXmlGenerator();
        $generator->setDatevAccountLedgerData($this);
        $generator->generate();
        $generator->validate();

        return $generator->getXmlString();
    }

    public function getMode(): string
    {
        return $this->mode;
    }
}
