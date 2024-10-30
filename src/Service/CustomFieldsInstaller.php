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
				],
				'customFieldPosition' => 0
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
			[
				'name' => 'bees_gprs_info_street',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'label' => [
						'en-GB' => 'Street',
						'de-DE' => 'Straße',
					],
					'customFieldPosition' => 2
				]
			],
			[
				'name' => 'bees_gprs_info_postalCode',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'label' => [
						'en-GB' => 'postalCode',
						'de-DE' => 'Postleitzahl',
					],
					'customFieldPosition' => 3
				]
			],
			[
				'name' => 'bees_gprs_info_city',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'label' => [
						'en-GB' => 'City',
						'de-DE' => 'Stadt',
					],
					'customFieldPosition' => 4
				]
			],
			[
				'name' => 'bees_gprs_info_land',
				'type' => CustomFieldTypes::SELECT,
				'config' => [
					'label' => [
						'en-GB' => 'Land',
						'de-DE' => 'Land',
					],
					'customFieldPosition' => 5
				]
			],
			[
				'name' => 'bees_gprs_info_email_address',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'label' => [
						'en-GB' => 'Mail address',
						'de-DE' => 'Mail Adresse',
					],
					'customFieldPosition' => 6
				]
			],
			[
				'name' => 'bees_gprs_info_phone_number',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'label' => [
						'en-GB' => 'Phone number',
						'de-DE' => 'Telefonnummer',
					],
					'customFieldPosition' => 7
				]
			]
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
                'entityName' => 'product.manufacturer',
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
