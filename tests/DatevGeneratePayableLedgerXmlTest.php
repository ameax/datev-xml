<?php

use Ameax\Datev\DataObjects\DatevAccountLedgerData;
use Carbon\Carbon;

it(/**
 * @throws Exception
 */ 'can generate a datev account ledger xml for tax free credit note', function () {

    $ledgerData = new DatevAccountLedgerData(
        consolidatedDate        : new Carbon('2023-01-12'),
        consolidatedDeliveryDate: new Carbon('2023-01-09'),
        consolidatedInvoiceId   : 'GS-2023-101',
        supplierName            : 'Mustermann',
        supplierCity            : 'Musterort',
        shipFromCountry         : 'DE',
        partyId                 : 'K999',
        iban                    : 'DE33762500000121540061',
        dueDate                 : new Carbon('2023-01-26'),
        bpAccountNo             : 10100,
        costCategoryId          : 10,
        costCategoryId2         : 20
    );

    $ledgerData->addAccountsPayableLedger(
        amount     : 123.45,
        accountNo  : 4760,
        buCode     : 0,
        information: 'Provisionsgutschrift',
        tax        : 19,
        bookingText: 'Provision, Art-Nr. Art123',
    );
    $xml = $ledgerData->generateXml();

    //        file_put_contents('tests/fixtures/Provisionsgutschrift_GS-2023-101.xml',$xml);
    expect($xml)->toEqual(file_get_contents('tests/fixtures/Provisionsgutschrift_GS-2023-101.xml'));
});
