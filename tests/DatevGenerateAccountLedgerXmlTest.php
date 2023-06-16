<?php

use Ameax\Datev\DataObjects\DatevAccountLedgerData;
use Carbon\Carbon;

it(/**
 * @throws Exception
 */ 'can generate a datev account ledger xml file with 1 position', function () {

    $ledgerData = new DatevAccountLedgerData(
        consolidatedDate: new Carbon('2023-01-12'),
        consolidatedDeliveryDate: new Carbon('2023-01-09'),
        consolidatedInvoiceId: 'R-2023-101',
        customerName: 'Musterkunde',
        customerCity: 'Musterort',
        shipFromCountry: 'DE',
        dueDate: new Carbon('2023-01-26'),
        bpAccountNo: 10100,
        costCategoryId: 10,
        costCategoryId2: 20,
        partyId: 'K999',
        vatId: 'DE999456789',
        iban: 'DE33762500000121540061'
    );

    $ledgerData->addAccountsReceivableLedger(
        amount: 1190.00,
        accountNo: 8400,
        tax: 19,
        information: 'Ausgangsrechnung, noch nicht bezahlt und Kontierung',
        bookingText: 'Produkt B, Art-Nr. Art456',
    );
    $xml = $ledgerData->generateXml();

    expect($xml)->toEqual(file_get_contents('tests/fixtures/Rechnungsdaten_RA_R-2023-101.xml'));
});

it(/**
 * @throws Exception
 */ 'can generate a datev account ledger xml file with multiple positions', function () {

    $ledgerData = new DatevAccountLedgerData(
        consolidatedDate: new Carbon('2023-02-05'),
        consolidatedDeliveryDate: new Carbon('2023-02-02'),
        consolidatedInvoiceId: 'R-2023-204',
        customerName: 'Musterkunde',
        customerCity: 'Musterort',
        shipFromCountry: 'DE',
        dueDate: new Carbon('2023-02-19'),
        bpAccountNo: 10100,
        costCategoryId: 10,
        costCategoryId2: 20,
        partyId: 'K999',
        vatId: 'DE999456789',
        iban: 'DE33762500000121540061',
    );

    $ledgerData->addAccountsReceivableLedger(
        amount: 1190.00,
        accountNo: 8400,
        tax: 19,
        information: 'Ausgangsrechnung, drei Steuersätze und Kontierung',
        bookingText: 'Produkt "ABC",Art-Nr. Art123',
        orderId: '5KK77592Y773816',
    );
    $ledgerData->addAccountsReceivableLedger(
        amount: 1190.00,
        accountNo: 8400,
        tax: 19,
        information: 'Ausgangsrechnung, drei Steuersätze und Kontierung',
        bookingText: 'Produkt B, Art-Nr. Art456',
        orderId: '5KK77592Y773816',
    );

    $ledgerData->addAccountsReceivableLedger(
        amount: 107.00,
        accountNo: 8300,
        tax: 19,
        information: 'Ausgangsrechnung, drei Steuersätze und Kontierung',
        bookingText: 'Produkt C, Art-Nr. Art789',
        orderId: '5KK77592Y773816',
    );

    $ledgerData->addAccountsReceivableLedger(
        amount: 100.00,
        accountNo: 8100,
        tax: 0,
        information: 'Ausgangsrechnung, drei Steuersätze und Kontierung',
        bookingText: 'Produkt D, Art-Nr. ArtD',
        orderId: '5KK77592Y773816',
    );
    $xml = $ledgerData->generateXml();

    expect($xml)->toEqual(file_get_contents('tests/fixtures/Rechnungsdaten_RA_R-2023-204.xml'));
});

it(/**
 * @throws Exception
 */ 'can generate a datev account ledger xml file with EU intra-community supply of goods', function () {

    $ledgerData = new DatevAccountLedgerData(
        consolidatedDate: new Carbon('2023-01-12'),
        consolidatedDeliveryDate: new Carbon('2023-01-09'),
        consolidatedInvoiceId: 'R-2023-101-EU',
        customerName: 'MusterkundeEU',
        customerCity: 'MusterortEU',
        dueDate: new Carbon('2023-01-26'),
        bpAccountNo: 20100,
        partyId: 'K999',
        vatId: 'FRXX999456789',
        ownVatId: 'DE333456789',
    );

    $ledgerData->addAccountsReceivableLedger(
        amount: 1000.00,
        accountNo: 8200,
        buCode: 231,
        tax: 0,
        information: 'Ausgangsrechnung, steuerfreie innergemeinschaftliche Lieferung',
        bookingText: 'Produkt B, Art-Nr. Art456',
    );

    $xml = $ledgerData->generateXml();
    //       file_put_contents('tests/fixtures/Rechnungsdaten_RA_R-2023-101-EU.xml',$xml);
    expect($xml)->toEqual(file_get_contents('tests/fixtures/Rechnungsdaten_RA_R-2023-101-EU.xml'));
});
