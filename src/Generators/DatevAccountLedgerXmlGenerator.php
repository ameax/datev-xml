<?php

namespace Ameax\Datev\Generators;

use Ameax\Datev\DataObjects\DatevAccountLedgerData;
use Ameax\Datev\DatevHelpers;
use Ameax\Datev\Serializers\DatevAccountLedgerRoot;

class DatevAccountLedgerXmlGenerator extends AbstractXmlGenerator
{
    public string               $ns = '{http://xml.datev.de/bedi/tps/ledger/v060}';

    protected DatevAccountLedgerData $datevAccountLedgerData;

    public function setDatevAccountLedgerData(DatevAccountLedgerData $datevAccountLedgerData): self
    {
        $this->datevAccountLedgerData = $datevAccountLedgerData;

        return $this;
    }

    public function getDatevAccountLedgerData(): DatevAccountLedgerData
    {
        return $this->datevAccountLedgerData;
    }

    protected function getXsdPath(): string
    {
        return 'xsds/Belegverwaltung_online_ledger_import_v060.xsd';
    }

    public function generate(): self
    {
        $root = new DatevAccountLedgerRoot(
            [
                'consolidate' => [
                    'attributes' => [
                        'consolidatedAmount' => DatevHelpers::formatAmount($this->datevAccountLedgerData->consolidatedAmount),
                        'consolidatedDate' => $this->datevAccountLedgerData->consolidatedDate->format('Y-m-d'),
                        'consolidatedInvoiceId' => $this->datevAccountLedgerData->consolidatedInvoiceId,
                        'consolidatedCurrencyCode' => $this->datevAccountLedgerData->consolidatedCurrencyCode,
                    ],
                    'value' => $this->getAccountsReceivableLedgerElements(),
                ],
            ],
            $this
        );

        $this->xml = $this->service->write('LedgerImport', $root);

        return $this;
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
                'information' => $ledger['information'] ?? null,
                'currencyCode' => $ledger['currencyCode'] ?? $this->datevAccountLedgerData->consolidatedCurrencyCode,
                'invoiceId' => $this->datevAccountLedgerData->consolidatedInvoiceId,
                'bookingText' => $ledger['bookingText'],
                'typeOfReceivable' => $ledger['typeOfReceivable'],
                'ownVatId' => $this->datevAccountLedgerData->ownVatId,
                'shipFromCountry' => $this->datevAccountLedgerData->shipFromCountry,
                'partyId' => $this->datevAccountLedgerData->partyId,
                'paidAt' => $this->datevAccountLedgerData->paidAt?->format('Y-m-d'),
                'internalInvoiceId' => $this->datevAccountLedgerData->internalInvoiceId,
                'vatId' => $this->datevAccountLedgerData->vatId,
                'shipToCountry' => $this->datevAccountLedgerData->shipToCountry,
                'exchangeRate' => DatevHelpers::formatAmount($ledger['exchangeRate']),
                'bankAccount' => $this->datevAccountLedgerData->bankAccount,
                'bankCountry' => $this->datevAccountLedgerData->bankCountry,
                'iban' => $this->datevAccountLedgerData->iban,
                'swiftCode' => $this->datevAccountLedgerData->swiftCode,
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
                'customerName' => $this->datevAccountLedgerData->customerName,
                'customerCity' => $this->datevAccountLedgerData->customerCity,
            ];

            $output[] = ['accountsReceivableLedger' => DatevHelpers::clearNullValues($data)];
        }

        return $output;
    }
}
