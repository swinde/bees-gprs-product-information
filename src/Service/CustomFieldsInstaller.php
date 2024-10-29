<?php declare(strict_types=1);

namespace BeesGprsProductInformation\Service;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldsInstaller
{
    private const CUSTOM_FIELDSET_NAME = 'bees_gprs_info_set';

    private const CUSTOM_FIELDSET = [
        'name' => self::CUSTOM_FIELDSET_NAME,
        'config' => [
            'label' => [
                'en-GB' => 'Produktinformationen (GPRS)',
                'de-DE' => 'Produktinformationen (GPRS)',
                Defaults::LANGUAGE_SYSTEM => 'Mention the fallback label here'
            ]
        ],
        'customFields' => [
            [
                'name' => 'bees_gprs_info_name',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Name oder Firmenname',
                        'de-DE' => 'Name oder Firmenname',
                        Defaults::LANGUAGE_SYSTEM => 'Mention the fallback label here'
                    ],
                    'customFieldPosition' => 1
                ]
            ],
        ]
    ];

    public function __construct(
        private readonly EntityRepository $customFieldSetRepository,
        private readonly EntityRepository $customFieldSetRelationRepository
    ) {
    }

    public function install(Context $context): void
    {
        $this->customFieldSetRepository->upsert([
            self::CUSTOM_FIELDSET
        ], $context);
    }

    public function addRelations(Context $context): void
    {
        $this->customFieldSetRelationRepository->upsert(array_map(function (string $customFieldSetId) {
            return [
                'customFieldSetId' => $customFieldSetId,
                'entityName' => 'product',
            ];
        }, $this->getCustomFieldSetIds($context)), $context);
    }

    /**
     * @return string[]
     */
    private function getCustomFieldSetIds(Context $context): array
    {
        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
    }
}
