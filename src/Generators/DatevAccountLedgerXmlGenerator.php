<?php

namespace Ameax\Datev\Generators;

use Ameax\Datev\DataObjects\DatevAccountLedgerData;
use Ameax\Datev\DatevHelpers;
use Ameax\Datev\Serializers\DatevAccountLedgerRoot;

class DatevAccountLedgerXmlGenerator extends AbstractXmlGenerator
{
    const MODE_PAYABLE_LEDGER = 'payable';

    const MODE_RECEIVABLE_LEDGER = 'receivable';

    public string $ns = '{http://xml.datev.de/bedi/tps/ledger/v060}';

    public string $mode;

    protected DatevAccountLedgerData $datevAccountLedgerData;

    public function setDatevAccountLedgerData(DatevAccountLedgerData $datevAccountLedgerData): self
    {
        $this->datevAccountLedgerData = $datevAccountLedgerData;
        $this->mode = $this->datevAccountLedgerData->getMode();

        return $this;
    }

    public function getDatevAccountLedgerData(): DatevAccountLedgerData
    {
        return $this->datevAccountLedgerData;
    }

    protected function getXsdPath(): string
    {
        return __DIR__.'/../../xsds/Belegverwaltung_online_ledger_import_v060.xsd';
    }

    public function generate(): self
    {
        $attributes = [
            'consolidatedAmount' => DatevHelpers::formatAmount($this->datevAccountLedgerData->consolidatedAmount),
            'consolidatedDate' => $this->datevAccountLedgerData->consolidatedDate->format('Y-m-d'),
            'consolidatedInvoiceId' => $this->datevAccountLedgerData->consolidatedInvoiceId,
            'consolidatedDeliveryDate' => $this->datevAccountLedgerData->consolidatedDeliveryDate->format('Y-m-d'),
            'consolidatedCurrencyCode' => $this->datevAccountLedgerData->consolidatedCurrencyCode,
        ];
        if (isset($this->datevAccountLedgerData->consolidatedOrderId)) {
            $attributes['consolidatedOrderId'] = $this->datevAccountLedgerData->consolidatedOrderId;
        }

        $root = new DatevAccountLedgerRoot(
            [
                'consolidate' => [
                    'attributes' => $attributes,
                    'value' => $this->mode === self::MODE_RECEIVABLE_LEDGER ? $this->getAccountsReceivableLedgerElements() : $this->getAccountsPayableLedgerElements(),
                ],
            ],
            $this
        );

        $this->xml = $this->service->write('LedgerImport', $root);

        return $this;
    }

    private function maxLength(?string $string, int $maxLength): ?string
    {
        if (! $string) {
            return $string;
        }

        return mb_substr($string, 0, $maxLength);
    }

    private function upper(?string $string): ?string
    {
        if (! $string) {
            return $string;
        }

        return mb_strtoupper($string);
    }

    private function removeSpaces(?string $string): ?string
    {
        if (! $string) {
            return $string;
        }

        return preg_replace("/\s/", '', $string);
    }

    private function getAccountsReceivableLedgerElements(): array
    {
        $output = [];

        foreach ($this->datevAccountLedgerData->getAccountsReceivableLedgers() as $ledger) {

            $data = [
                'date' => isset($ledger['date']) ? $ledger['date']->format('Y-m-d') : $this->datevAccountLedgerData->consolidatedDate->format('Y-m-d'),
                'amount' => DatevHelpers::formatAmount($ledger['amount']),
                'discountAmount' => DatevHelpers::formatAmount($ledger['discountAmount']),
                'accountNo' => $ledger['accountNo'],
                'buCode' => $ledger['buCode'],
                'costAmount' => DatevHelpers::formatAmount($ledger['costAmount']),
                'costCategoryId' => $ledger['costCategoryId'] ?? $this->datevAccountLedgerData->costCategoryId ?? null,
                'costCategoryId2' => $ledger['costCategoryId2'] ?? $this->datevAccountLedgerData->costCategoryId2 ?? null,
                'tax' => DatevHelpers::formatAmount($ledger['tax']),
                'information' => $this->maxLength($ledger['information'] ?? null, 120),
                'currencyCode' => $ledger['currencyCode'] ?? $this->datevAccountLedgerData->consolidatedCurrencyCode,
                'invoiceId' => $this->datevAccountLedgerData->consolidatedInvoiceId,
                'bookingText' => $this->maxLength($ledger['bookingText'], 60),
                'typeOfReceivable' => $ledger['typeOfReceivable'],
                'ownVatId' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->ownVatId)),
                'shipFromCountry' => $this->datevAccountLedgerData->shipFromCountry,
                'partyId' => $this->datevAccountLedgerData->partyId,
                'paidAt' => $this->datevAccountLedgerData->paidAt?->format('Y-m-d'),
                'internalInvoiceId' => $this->datevAccountLedgerData->internalInvoiceId,
                'vatId' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->vatId)),
                'shipToCountry' => $this->datevAccountLedgerData->shipToCountry,
                'exchangeRate' => DatevHelpers::formatAmount($ledger['exchangeRate']),
                'bankAccount' => $this->datevAccountLedgerData->bankAccount,
                'bankCountry' => $this->datevAccountLedgerData->bankCountry,
                'iban' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->iban)),
                'swiftCode' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->swiftCode)),
                'accountName' => $this->datevAccountLedgerData->accountName,
                'paymentConditionsId' => $this->datevAccountLedgerData->paymentConditionsId,
                'paymentOrder' => $this->datevAccountLedgerData->paymentOrder,
                'discountPercentage' => $ledger['discountPercentage'],
                'discountPaymentDate' => $ledger['discountPaymentDate'],
                'discountAmount2' => $ledger['discountAmount2'],
                'discountPercentage2' => $ledger['discountPercentage2'],
                'discountPaymentDate2' => $ledger['discountPaymentDate2'],
                'dueDate' => $this->datevAccountLedgerData->dueDate?->format('Y-m-d'),
                'bpAccountNo' => $this->datevAccountLedgerData->bpAccountNo,
                'deliveryDate' => isset($ledger['deliveryDate']) ? $ledger['deliveryDate']->format('Y-m-d') : null,
                'orderId' => $ledger['orderId'],
                'customerName' => $this->maxLength($this->datevAccountLedgerData->customerName, 50),
                'customerCity' => $this->maxLength($this->datevAccountLedgerData->customerCity, 30),
            ];

            $output[] = ['accountsReceivableLedger' => DatevHelpers::clearNullValues($data)];
        }

        return $output;
    }

    private function getAccountsPayableLedgerElements(): array
    {
        $output = [];

        foreach ($this->datevAccountLedgerData->getAccountsPayableLedgers() as $ledger) {

            $data = [
                'date' => isset($ledger['date']) ? $ledger['date']->format('Y-m-d') : $this->datevAccountLedgerData->consolidatedDate->format('Y-m-d'),
                'amount' => DatevHelpers::formatAmount($ledger['amount']),
                'discountAmount' => DatevHelpers::formatAmount($ledger['discountAmount']),
                'accountNo' => $ledger['accountNo'],
                'buCode' => $ledger['buCode'],
                'costAmount' => DatevHelpers::formatAmount($ledger['costAmount']),
                'costCategoryId' => $ledger['costCategoryId'] ?? $this->datevAccountLedgerData->costCategoryId ?? null,
                'costCategoryId2' => $ledger['costCategoryId2'] ?? $this->datevAccountLedgerData->costCategoryId2 ?? null,
                'tax' => DatevHelpers::formatAmount($ledger['tax']),
                'information' => $this->maxLength($ledger['information'] ?? null, 120),
                'currencyCode' => $ledger['currencyCode'] ?? $this->datevAccountLedgerData->consolidatedCurrencyCode,
                'invoiceId' => $this->datevAccountLedgerData->consolidatedInvoiceId,
                'bookingText' => $this->maxLength($ledger['bookingText'], 60),
                'typeOfReceivable' => $ledger['typeOfReceivable'],
                'ownVatId' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->ownVatId)),
                'shipFromCountry' => $this->datevAccountLedgerData->shipFromCountry,
                'partyId' => $this->datevAccountLedgerData->partyId,
                'paidAt' => $this->datevAccountLedgerData->paidAt?->format('Y-m-d'),
                'internalInvoiceId' => $this->datevAccountLedgerData->internalInvoiceId,
                'vatId' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->vatId)),
                'shipToCountry' => $this->datevAccountLedgerData->shipToCountry,
                'exchangeRate' => DatevHelpers::formatAmount($ledger['exchangeRate']),
                'bankAccount' => $this->datevAccountLedgerData->bankAccount,
                'bankCountry' => $this->datevAccountLedgerData->bankCountry,
                'iban' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->iban)),
                'swiftCode' => $this->upper($this->removeSpaces($this->datevAccountLedgerData->swiftCode)),
                'accountName' => $this->datevAccountLedgerData->accountName,
                'paymentConditionsId' => $this->datevAccountLedgerData->paymentConditionsId,
                'paymentOrder' => $this->datevAccountLedgerData->paymentOrder,
                'discountPercentage' => $ledger['discountPercentage'],
                'discountPaymentDate' => $ledger['discountPaymentDate'],
                'discountAmount2' => $ledger['discountAmount2'],
                'discountPercentage2' => $ledger['discountPercentage2'],
                'discountPaymentDate2' => $ledger['discountPaymentDate2'],
                'dueDate' => $this->datevAccountLedgerData->dueDate?->format('Y-m-d'),
                'bpAccountNo' => $this->datevAccountLedgerData->bpAccountNo,
                'deliveryDate' => isset($ledger['deliveryDate']) ? $ledger['deliveryDate']->format('Y-m-d') : null,
                'orderId' => $ledger['orderId'],
                'supplierName' => $this->maxLength($this->datevAccountLedgerData->supplierName, 50),
                'supplierCity' => $this->maxLength($this->datevAccountLedgerData->supplierCity, 30),
            ];

            $output[] = ['accountsPayableLedger' => DatevHelpers::clearNullValues($data)];
        }

        return $output;
    }
}
