# Package to generate DATEV xml files

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ameax/datev-xml.svg?style=flat-square)](https://packagist.org/packages/ameax/datev-xml)
[![Tests](https://img.shields.io/github/actions/workflow/status/ameax/datev-xml/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ameax/datev-xml/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ameax/datev-xml.svg?style=flat-square)](https://packagist.org/packages/ameax/datev-xml)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require ameax/datev-xml
```

## Usage

```php
    use Ameax\Datev\DataObjects\DatevAccountLedgerData;
    use Ameax\Datev\DataObjects\DatevDocumentData;
    use Ameax\Datev\DataObjects\DatevRepositoryData;
    use Ameax\Datev\Zip;
    use Carbon\Carbon;

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

    $datevDocumentData->buildAccountsPayableLedger(
        datevAccountLedgerData: $ledgerData,
        filePaths             : ['path-to-invoice.pdf']
    );

    $datevRepositoryData = new DatevRepositoryData();

    $datevDocumentData->addSEPAFile(
        nameWithExtension  : 'sepa-2023-12345.xml',
        filePath           : 'path-to-sepafile.xml',
        date               : new Carbon('2023-05-01'),
        datevRepositoryData: $datevRepositoryData);

    $zipPath = $datevDocumentData->generateZip();
```



## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Schmidt](https://github.com/ameax)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
