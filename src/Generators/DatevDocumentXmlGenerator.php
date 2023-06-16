<?php

namespace Ameax\Datev\Generators;

use Ameax\Datev\DataObjects\DatevDocumentData;
use Ameax\Datev\DataObjects\DatevRepositoryData;
use Ameax\Datev\Serializers\DatevDocumentRoot;

class DatevDocumentXmlGenerator extends AbstractXmlGenerator
{
    public string               $ns = '{http://xml.datev.de/bedi/tps/document/v06.0}';

    protected DatevDocumentData $datevDocumentData;

    public function setDatevDocumentData(DatevDocumentData $datevDocumentData): self
    {
        $this->datevDocumentData = $datevDocumentData;

        return $this;
    }

    protected function getXsdPath(): string
    {
        return 'xsds/Document_v060.xsd';
    }

    public function generate(): self
    {
        $root = new DatevDocumentRoot(
            [
                'header' => [
                    'date' => $this->datevDocumentData->date?->format('Y-m-d\TH:i:s'),
                ],
                'content' => $this->getContent(),
            ],
            $this
        );

        $this->xml = $this->service->write('archive', $root);

        return $this;
    }

    private function getLedgerMainExtensionElement(array $documentData): array
    {
        return [
            'extension' => [
                'attributes' => [
                    $this->xsi.'type' => $documentData['type'],
                    'datafile' => $documentData['name'].'.xml',
                ],
                'value' => [[
                    'property' => [
                        'attributes' => [
                            'key' => '1',
                            'value' => $documentData['date']->format('Y-m'), // Rechnungsmonat Y-m
                        ],
                    ],
                ],
                    [
                        'property' => [
                            'attributes' => [
                                'key' => '3',
                                'value' => $this->getPropertyLevel3ByType($documentData['type']),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getPropertyLevel3ByType(string $type): string
    {
        return DatevRepositoryData::getTypeLabel($type);
    }

    private function getFileMainExtensionElement(array $documentData, string $fileAttribute = 'datafile'): array
    {
        return [
            'extension' => [
                'attributes' => [
                    $this->xsi.'type' => $documentData['type'],
                    $fileAttribute => $fileAttribute === 'datafile' ? basename($documentData['filePath']) : $documentData['name'],
                ],
            ],
        ];
    }

    private function getRepositoryElement(array $documentData): array
    {
        /** @var DatevRepositoryData $datevRepositoryData */
        $datevRepositoryData = $documentData['datevRepositoryData'];

        return
            [
                'repository' => [
                    'level' => [
                        'attributes' => [
                            'id' => '1',
                            'name' => $datevRepositoryData->getLevel(1, $documentData),
                        ],
                    ], [
                        'level' => [
                            'attributes' => [
                                'id' => '2',
                                'name' => $datevRepositoryData->getLevel(2, $documentData),
                            ],
                        ]], [
                            'level' => [
                                'attributes' => [
                                    'id' => '3',
                                    'name' => $datevRepositoryData->getLevel(3, $documentData),
                                ],
                            ],
                        ],
                ],
            ];
    }

    private function getAdditionalFilesExtensionElements(array $documentData): array
    {
        //<extension xsi:type="File" name="Rechnungsbild_RA_R-2023-101.pdf"/>
        $extensionElements = [];
        foreach ($documentData['filePaths'] as $filePath) {
            $extensionElements[] = [
                'extension' => [
                    'attributes' => [
                        $this->xsi.'type' => 'File',
                        'name' => basename($filePath),
                    ],
                ],
            ];
        }

        return $extensionElements;

    }

    private function getContent(): array
    {
        $output = [];

        foreach ($this->datevDocumentData->getData() as $documentData) {
            $output[] = match ($documentData['type']) {
                DatevDocumentData::TYPE_FILE, DatevDocumentData::TYPE_SEPA_FILE => $this->getDocumentFile($documentData),
                default => $this->getDocumentLedger($documentData),
            };
        }

        return $output;
    }

    private function getDocumentLedger(array $documentData): array
    {
        $documentElement = [];
        $documentElement[] = $this->getLedgerMainExtensionElement($documentData);
        if (! empty($documentData['filePaths'])) {
            $documentElement[] = $this->getAdditionalFilesExtensionElements($documentData);
        }
        if (isset($documentData['datevRepositoryData'])) {
            $documentElement[] = $this->getRepositoryElement($documentData);
        }

        return ['document' => $documentElement];
    }

    private function getDocumentFile(array $documentData): array
    {
        $documentElement = [];
        $documentElement[] = $this->getFileMainExtensionElement($documentData, 'name');
        if (isset($documentData['datevRepositoryData'])) {
            $documentElement[] = $this->getRepositoryElement($documentData);
        }

        return ['document' => $documentElement];
    }
}
