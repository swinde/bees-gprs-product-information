<?php declare(strict_types=1);

namespace BeesGprsProductInformation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;


class BeesGprsProductInformation extends Plugin
{
	public function activate(Plugin\Context\ActivateContext $activateContext): void
	{
		/** @var EntityRepository<CustomFieldSetEntity> $customFieldSetRepository */
		$customFieldSetRepository = $this->container->get('custom_field_set.repository');
		/** @var EntityRepository<CustomFieldEntity> $customFieldRepository */
		$customFieldRepository = $this->container->get('custom_field.repository');
		$context = $activateContext->getContext();

		$customFields = [
			[
				'name' => 'contact_info',
				'active' => true,
				'config' => [
					'label' => [
						'en-GB' => 'Produktinformationen (GPRS)',
						'de-DE' => 'Produktinformationen (GPRS)'
					],
				],
				'customFields' => [
					[
						'name' => 'name',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'Name oder Firmenname',
								'de-DE' => 'Name oder Firmenname',
							]
						]
					],
					[
						'name' => 'street',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'Street',
								'de-DE' => 'Straße',
							]
						]
					],
					[
						'name' => 'postalCode',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'postalCode',
								'de-DE' => 'Postleitzahl',
							]
						]
					],
					[
						'name' => 'city',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'City',
								'de-DE' => 'Stadt',
							]
						]
					],
			,
					[
						'name' => 'land',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'Land',
								'de-DE' => 'Land',
							]
						]
					],

					[
						'name' => 'email_address',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'Mail address',
								'de-DE' => 'Mail Adresse',
							]
						]
					],
					[
						'name' => 'phone_number',
						'type' => CustomFieldTypes::TEXT,
						'config' => [
							'label' => [
								'en-GB' => 'Phone number',
								'de-DE' => 'Telefonnummer',
							]
						]
					]
				],
				'relations' => [
					[
						'entityName' => 'product'
					]
				]
			]
		];

		foreach ($customFields as $customFieldSet) {
			$criteria = (new Criteria())->addFilter(new EqualsFilter('name', $customFieldSet['name']));
			$customFieldSetId = $customFieldSetRepository
				->search($criteria, $context)
				->first()
				?->getUniqueIdentifier();

			if ($customFieldSetId === null) {
				$customFieldSetRepository->upsert([$customFieldSet], $context);
			} else {
				foreach ($customFieldSet['customFields'] as $customField) {
					$criteria = (new Criteria())
						->addFilter(new EqualsFilter('customFieldSetId', $customFieldSetId))
						->addFilter(new EqualsFilter('name', $customField['name']));

					$customFieldId = $customFieldRepository
						->search($criteria, $context)
						->first()
						?->getUniqueIdentifier();

					if ($customFieldId !== null) {
						$customField['id'] = $customFieldId;
					}

					$customField['customFieldSetId'] = $customFieldSetId;
					$customFieldRepository->upsert([$customField], $context);
				}
			}
		}
	}
}
