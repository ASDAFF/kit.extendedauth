<?php

namespace Kit\Auth\Person\Buyer\AdminInterface;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\DateTimeWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use DigitalWand\AdminHelper\Widget\UserWidget;

Loc::loadMessages(__FILE__);

class ListAdminInterface extends AdminInterface
{
	/**
	 * @inheritdoc
	 */
	public function dependencies()
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public function fields()
	{
		return [
			'MAIN' => [
				'NAME' => Loc::getMessage('TAB_TITLE'),
				'FIELDS' => [
					'ID' => [
						'WIDGET' => new NumberWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_ID'),
						'READONLY' => true,
						'FILTER' => true,
						'HEADER' => true,
					],
					'ID_USER' => [
						'WIDGET' => new UserWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_USER_ID'),
						'READONLY' => true,
						'FILTER' => 'BETWEEN',
						'HEADER' => true,
						'STYLE' => 'height: 200px;'
					],
					'EMAIL' => [
						'WIDGET' => new StringWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_EMAIL'),
						'READONLY' => true,
						'FILTER' => true,
						'HEADER' => true,
					],
					'INN' =>[
						'WIDGET' => new StringWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_INN'),
						'READONLY' => true,
						'FILTER' => true,
						'HEADER' => true,
					],
					'FIELDS' => [
						'WIDGET' => new Widget\OrgWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_FIELDS'),
						'READONLY' => true,
						'HEADER' => true,
						'FILTER' => false,
					],
					'DATE_CREATE' => [
						'WIDGET' => new DateTimeWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_DATE_CREATE'),
						'HEADER' => true,
						'READONLY' => false,
						'FILTER' => true,
					],
					'DATE_UPDATE' => [
						'WIDGET' => new DateTimeWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_DATE_UPDATE'),
						'HEADER' => true,
						'READONLY' => false,
						'FILTER' => true,
					],
					'STATUS' => [
						'WIDGET' => new Widget\StatusWidget(),
						'TITLE' => Loc::getMessage('KIT_AUTH_STATUS'),
						'READONLY' => true,
						'HEADER' => true,
						'READONLY' => false,
						'FILTER' => true,
					],
				]
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function helpers()
	{
		return [
			'\Kit\Auth\Person\Buyer\AdminInterface\ListHelper' => [
				'BUTTONS' => [
					'LIST_CREATE_NEW' => [
						'TEXT' => Loc::getMessage('DEMO_AH_NEWS_BUTTON_ADD_NEWS'),
					],
					'LIST_CREATE_NEW_SECTION' => [
						'TEXT' => Loc::getMessage('DEMO_AH_NEWS_BUTTON_ADD_CATEGORY'),
					]
				]
			],
			'\Kit\Auth\Person\Buyer\AdminInterface\EditHelper' => [
				'BUTTONS' => [
					'ADD_ELEMENT' => [
						'TEXT' => Loc::getMessage('DEMO_AH_NEWS_BUTTON_ADD_NEWS')
					],
					'DELETE_ELEMENT' => [
						'TEXT' => Loc::getMessage('DEMO_AH_NEWS_BUTTON_DELETE')
					]
				]
			]
		];
	}
}