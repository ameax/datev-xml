<?php

use Ameax\Datev\DataObjects\DatevAccountLedgerData;
use Ameax\Datev\DataObjects\DatevDocumentData;
use Ameax\Datev\DataObjects\DatevRepositoryData;
use Carbon\Carbon;

it('can generate a datev document xml', function () {

    DatevRepositoryData::make('ameax Unternehmensoftware');

    $datevDocumentData = new DatevDocumentData(Carbon::make('2023-06-16T08:30:06'));
    $datevDocumentData->addAccountsPayableLedger(
        nameWithoutExtension: 'Eingangsrechnung 123',
        xmlContent          : '<xml>',
        date                : Carbon::make('2023-06-01'),
        filePaths           : ['test1.pdf', 'test2.pdf'],
        datevRepositoryData : DatevRepositoryData::make('ameax Unternehmensoftware')
    );
    $datevDocumentData->addAccountsReceivableLedger(
        nameWithoutExtension: 'Ausgangsrechnung 345',
        xmlContent          : '<xml>',
        date                : Carbon::make('2023-06-01'),
        filePaths           : [],
        datevRepositoryData : DatevRepositoryData::make('test vendor')
    );
    $datevDocumentData->addSEPAFile(
        nameWithExtension  : 'sepa-12345.xml',
        filePath           : 'tests/fixtures/sepa12345.xml',
        date               : Carbon::make('2023-06-01'),
        datevRepositoryData: DatevRepositoryData::make()
    );
    $datevDocumentData->addFile(
        nameWithExtension  : 'Contract-999.pdf',
        filePath           : 'tests/fixtures/small.pdf',
        date               : Carbon::make('2023-06-01'),
        datevRepositoryData: DatevRepositoryData::make()
    );

    $xml = $datevDocumentData->generateXml();

    expect($xml)->toEqual(file_get_contents('tests/fixtures/document.xml'));
});

it(/**
 * @throws Exception
 */ 'can generate a datev account ledger xml file with EU intra-community supply of goods', function () {

    $ledgerData = new DatevAccountLedgerData(
        consolidatedDate        : new Carbon('2023-01-12'),
        consolidatedDeliveryDate: new Carbon('2023-01-09'),
        consolidatedInvoiceId   : 'R-2023-101-EU',
        customerName            : 'MusterkundeEU',
        customerCity            : 'MusterortEU',
        ownVatId                : 'DE333456789',
        partyId                 : 'K999',
        vatId                   : 'FRXX999456789',
        dueDate                 : new Carbon('2023-01-26'),
        bpAccountNo             : 20100,
    );

    $ledgerData->addAccountsReceivableLedger(
        amount     : 1000.00,
        accountNo  : 8200,
        buCode     : 231,
        information: 'Ausgangsrechnung, steuerfreie innergemeinschaftliche Lieferung',
        tax        : 0,
        bookingText: 'Produkt B, Art-Nr. Art456',
    );

    $xml = $ledgerData->generateXml();
    expect($xml)->toEqual(file_get_contents('tests/fixtures/Rechnungsdaten_RA_R-2023-101-EU.xml'));
});
