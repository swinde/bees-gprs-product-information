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
	private const CUSTOM_FIELDSET_NAME = 'contact_info';

	private const CUSTOM_FIELDSET = [
		'name' => self::CUSTOM_FIELDSET_NAME,
		'config' => [
			'label' => [
				'en-GB' => 'Produktinformationen (GPRS)',
				'de-DE' => 'Produktinformationen (GPRS)',
				Defaults::LANGUAGE_SYSTEM => 'Mention the fallback label here'
			],
			'customFieldPosition' => 1
		],
		'customFields' => [
			[
				'name' => 'name',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'componentName' => 'sw-field',
					'customFieldType' => "text",
					'customFieldPosition' => 1,
					'label' => [
						'en-GB' => 'Name oder Firmenname',
						'de-DE' => 'Name oder Firmenname',
						Defaults::LANGUAGE_SYSTEM => 'Mention the fallback label here'
					],
				]
			],
			[
				'name' => 'street',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'componentName' => 'sw-field',
					'customFieldType' => "text",
					'customFieldPosition' => 2,
					'label' => [
						'en-GB' => 'Street',
						'de-DE' => 'Straße',
					],
				]
			],
			[
				'name' => 'postalCode',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'componentName' => 'sw-field',
					'customFieldType' => "text",
					'customFieldPosition' => 3,
					'label' => [
						'en-GB' => 'postalCode',
						'de-DE' => 'Postleitzahl',
					],
				]
			],
			[
				'name' => 'city',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'componentName' => 'sw-field',
					'customFieldType' => "text",
					'customFieldPosition' => 4,
					'label' => [
						'en-GB' => 'City',
						'de-DE' => 'Stadt',
					],
				]
			],
			[
				'name' => 'land',
				'type' => CustomFieldTypes::ENTITY,
				'config' => [
					'componentName' => 'sw-entity-single-select',
					'customFieldType' => "entity",
					'entity' => "country",
					'customFieldPosition' => 5,
					'label' => [
						'en-GB' => 'Land',
						'de-DE' => 'Land',
					],
				]
			],
			[
				'name' => 'bees_gprs_info_email_address',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'componentName' => 'sw-field',
					'customFieldType' => "text",
					'customFieldPosition' => 6,
					'label' => [
						'en-GB' => 'Mail address',
						'de-DE' => 'Mail Adresse',
					],

				]
			],
			[
				'name' => 'phone_number',
				'type' => CustomFieldTypes::TEXT,
				'config' => [
					'componentName' => 'sw-field',
					'customFieldType' => "text",
					'customFieldPosition' => 7,
					'label' => [
						'en-GB' => 'Phone number',
						'de-DE' => 'Telefonnummer',
					],
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