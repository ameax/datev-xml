<?php

use Ameax\Datev\DataObjects\DatevAccountLedgerData;
use Ameax\Datev\DataObjects\DatevDocumentData;
use Ameax\Datev\DataObjects\DatevRepositoryData;
use Ameax\Datev\Zip;
use Carbon\Carbon;

it( /**
 * @throws Exception
 */ 'can generate a zip file', function () {

    $zip = new Zip();
    $path = $zip
        ->addFile(
            path                : 'tests/fixtures/Rechnungsdaten_RA_R-2023-101.xml',
            pathAndFilenameInZip: 'Rechnungsdaten_RA_R-2023-101.xml'
        )
        ->addFile(
            path                : 'tests/fixtures/small.pdf',
            pathAndFilenameInZip: 'Rechnungsdaten_RA_R-2023-101.pdf'
        )
        ->addFile(
            path                : 'tests/fixtures/Rechnungsdaten_RA_R-2023-101.xml',
            pathAndFilenameInZip: 'Rechnungsdaten_RA_R-2023-102.xml'
        )
        ->addFile(
            path                : 'tests/fixtures/small.pdf',
            pathAndFilenameInZip: 'Rechnungsdaten_RA_R-2023-102.pdf'
        )
        ->close()
        ->getZipPath();

    expect($path)->toBeFile();

});

it( /**
 * @throws Exception
 */ 'can generates multiple Files in a zip file', closure: function () {

    $datevRepositoryData = DatevRepositoryData::make('ameax');

    $datevDocumentData = new DatevDocumentData();

    $ledgerData = new DatevAccountLedgerData(
        consolidatedDate: new Carbon('2023-06-14'),
        consolidatedDeliveryDate: new Carbon('2023-06-14'),
        consolidatedInvoiceId: 'RE12345',
        customerName: 'ARANES',
        customerCity: 'Aufhausen',
        shipFromCountry: 'DE',
        accountName: 'ARANES Aufhausen',
        dueDate: new Carbon('2023-07-01'),
        bpAccountNo: '12345'
    );
    $ledgerData->addAccountsReceivableLedger(
        amount: 119.00,
        accountNo: '8400',
        information: 'Software',
        bookingText: 'Umsatz 19%'
    );
    $ledgerData->addAccountsReceivableLedger(
        amount: 214.00,
        accountNo: '8300',
        information: 'Bücher',
        bookingText: 'Umsatz 7%'
    );

    $datevDocumentData->buildAccountsReceivableLedger(
        datevAccountLedgerData: $ledgerData,
        filePaths             : ['tests/fixtures/small.pdf'],
        datevRepositoryData: $datevRepositoryData
    );

    $datevDocumentData->addSEPAFile(
        nameWithExtension  : 'sepa-2023-12345.xml',
        filePath           : 'tests/fixtures/sepa12345.xml',
        date               : new Carbon('2023-05-01'),
        datevRepositoryData: $datevRepositoryData);

    $zipPath = $datevDocumentData->generateZip();
//    file_put_contents(__DIR__.'/fixtures/export.zip',file_get_contents($zipPath));
    expect($zipPath)->toBeFile();

});
