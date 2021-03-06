<?php

namespace Sotbit\Auth\Person\User\AdminInterface\Widget;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use DigitalWand\AdminHelper\Helper\AdminListHelper;
use DigitalWand\AdminHelper\Helper\AdminSectionListHelper;
use Bitrix\Sale\Internals\OrderPropsTable;

Loc::loadMessages(__FILE__);

class OrgWidget extends \DigitalWand\AdminHelper\Widget\HelperWidget
{
	static protected $defaults = array(
		'FILTER' => '%',
		'EDIT_IN_LIST' => true
	);

	/**
	 * @inheritdoc
	 */
	protected function getEditHtml()
	{
		$style = $this->getSettings('STYLE');
		$size = $this->getSettings('SIZE');

		$link = '';
		if ($this->getSettings('TRANSLIT')) {


			$uniqId = get_class($this->entityName) . '_' . $this->getCode();
			$nameId = 'name_link_' . $uniqId;
			$linkedFunctionName = 'set_linked_' . get_class($this->entityName) . '_CODE';

			if (isset($this->entityName->{$this->entityName->pk()})) {
				$pkVal = $this->entityName->{$this->entityName->pk()};
			} else {
				$pkVal = '_new_';
			}

			$nameId .= $pkVal;
			$linkedFunctionName .= $pkVal;

			$link = '<image id="' . $nameId . '" title="' . Loc::getMessage("IBSEC_E_LINK_TIP") . '" class="linked" src="/bitrix/themes/.default/icons/iblock/link.gif" onclick="' . $linkedFunctionName . '()" />';
		}

		return '<input type="text"
					   name="' . $this->getEditInputName() . '"
					   value="' . static::prepareToTagAttr($this->getValue()) . '"
					   size="' . $size . '"
					   style="' . $style . '"/>' . $link;
	}

	protected function getMultipleEditHtml()
	{
		$style = $this->getSettings('STYLE');
		$size = $this->getSettings('SIZE');
		$uniqueId = $this->getEditInputHtmlId();

		$rsEntityData = null;

		if (!empty($this->data['ID'])) {
			$entityName = $this->entityName;
			$rsEntityData = $entityName::getList(array(
				'select' => array('REFERENCE_' => $this->getCode() . '.*'),
				'filter' => array('=ID' => $this->data['ID'])
			));
		}

		ob_start();
		?>

		<div id="<?= $uniqueId ?>-field-container" class="<?= $uniqueId ?>">
		</div>

		<script>
			var multiple = new MultipleWidgetHelper(
				'#<?= $uniqueId ?>-field-container',
				'{{field_original_id}}<input type="text" name="<?= $this->getCode()?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]" style="<?=$style?>" size="<?=$size?>" value="{{value}}">'
			);
			<?
			if ($rsEntityData)
			{
				while($referenceData = $rsEntityData->fetch())
				{
					if (empty($referenceData['REFERENCE_' . $this->getMultipleField('ID')]))
					{
						continue;
					}

					?>
			multiple.addField({
				value: '<?= static::prepareToJs($referenceData['REFERENCE_' . $this->getMultipleField('VALUE')]) ?>',
				field_original_id: '<input type="hidden" name="<?= $this->getCode()?>[{{field_id}}][<?= $this->getMultipleField('ID') ?>]"' +
				' value="<?= $referenceData['REFERENCE_' . $this->getMultipleField('ID')] ?>">',
				field_id: <?= $referenceData['REFERENCE_' . $this->getMultipleField('ID')] ?>
			});
			<?
						   }
					   }
					   ?>

			multiple.addField();
		</script>
		<?
		return ob_get_clean();
	}

	protected function getMultipleValueReadonly()
	{
		$rsEntityData = null;
		if (!empty($this->data['ID'])) {
			$entityName = $this->entityName;
			$rsEntityData = $entityName::getList(array(
				'select' => array('REFERENCE_' => $this->getCode() . '.*'),
				'filter' => array('=ID' => $this->data['ID'])
			));
		}

		$result = '';
		if ($rsEntityData) {
			while ($referenceData = $rsEntityData->fetch()) {
				if (empty($referenceData['REFERENCE_VALUE'])) {
					continue;
				}

				$result .= '<div class="wrap_text" style="margin-bottom: 5px">' .
					static::prepareToOutput($referenceData['REFERENCE_VALUE']) . '</div>';
			}
		}

		return $result;
	}

	public function generateRow(&$row, $data)
	{
		if ($this->getSettings('MULTIPLE')) {
		} else {
			if ($this->getSettings('EDIT_LINK') || $this->getSettings('SECTION_LINK')) {
				$pk = $this->helper->pk();

				if ($this->getSettings('SECTION_LINK')) {
					$params = $this->helper->isPopup() ? $_GET : array();
					$params['ID'] = $this->data[$pk];
					$listHelper = $this->helper->getHelperClass($this->helper->isPopup() ? AdminSectionListHelper::className() : AdminListHelper::className());
					$pageUrl = $listHelper::getUrl($params);
					$value = '<span class="adm-submenu-item-link-icon adm-list-table-icon iblock-section-icon"></span>';
				} else {
					$editHelper = $this->helper->getHelperClass(AdminEditHelper::className());
					$pageUrl = $editHelper::getUrl(array(
						'ID' => $this->data[$pk]
					));
				}

				$value .= '<a href="' . $pageUrl . '">' . static::prepareToOutput($this->getValue()) . '</a>';
			} else {

				$values = [];
				$data = $this->getValue();
				if($data['PERSON_TYPE'] > 0)
				{
					$codes = array_keys($data['ORDER_FIELDS']);
					$rs = OrderPropsTable::getList([
						'filter' => [
							'CODE' => $codes,
							'PERSON_TYPE_ID' =>
								$data['PERSON_TYPE']
						],
						'select' => ['CODE','NAME']
					]);
					while($prop = $rs->fetch())
					{
						$values[$prop['CODE']] = $prop['NAME'];
					}
				}
				$value = '';
				foreach($values as $c => $n)
				{
					$value .= $n.': '.$data['ORDER_FIELDS'][$c].'<br>'."\n\r";
				}
			}

			if ($this->getSettings('EDIT_IN_LIST') AND !$this->getSettings('READONLY')) {
				$row->AddInputField($this->getCode(), array('style' => 'width:90%'));
			}

			$row->AddViewField($this->getCode(), $value);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function showFilterHtml()
	{
		if ($this->getSettings('MULTIPLE')) {
		} else {
			print '<tr>';
			print '<td>' . $this->getSettings('TITLE') . '</td>';
			if ($this->isFilterBetween()) {
				list($from, $to) = $this->getFilterInputName();
				print '<td>
			<div class="adm-filter-box-sizing">
				<span style="display: inline-block; left: 11px; top: 5px; position: relative;">'.Loc::getMessage('FROM').':</span>
				<div class="adm-input-wrap" style="display: inline-block">
					<input type="text" class="adm-input" name="' . $from . '" value="' . $$from . '">
				</div>
				<span style="display: inline-block; left: 11px; top: 5px; position: relative;">'.Loc::getMessage('TO').':</span>
				<div class="adm-input-wrap" style="display: inline-block">
					<input type="text" class="adm-input" name="' . $to . '" value="' . $$to . '">
				</div>
			</div>
			</td> ';
			} else {
				print '<td><input type="text" name="' . $this->getFilterInputName() . '" size="47" value="' . $this->getCurrentFilterValue() . '"></td>';
			}
			print '</tr>';
		}
	}

	protected function getValueReadonly()
	{
		$values = [];
		$data = $this->getValue();
		if($data['PERSON_TYPE'] > 0)
		{
			$codes = array_keys($data['ORDER_FIELDS']);
			$rs = OrderPropsTable::getList([
				'filter' => [
					'CODE' => $codes,
					'PERSON_TYPE_ID' =>
						$data['PERSON_TYPE']
				],
				'select' => ['CODE','NAME']
			]);
			while($prop = $rs->fetch())
			{
				$values[$prop['CODE']] = $prop['NAME'];
			}
		}
		$return = '';
		foreach($values as $c => $n)
		{
			$return .= $n.': '.$data['ORDER_FIELDS'][$c].'<br>'."\n\r";
		}
		return $return;
	}
}