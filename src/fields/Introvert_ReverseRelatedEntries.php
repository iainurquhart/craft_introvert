<?php
namespace iainurquhart\introvert\fields;

use Craft;

use craft\base\Field;
use craft\base\ElementInterface;
use craft\elements\MatrixBlock;
use craft\elements\Entry;
use craft\elements\Category;

use iainurquhart\introvert\Introvert;
use iainurquhart\introvert\services\Introvert as IntrovertService;


class Introvert_ReverseRelatedEntries extends Field
{

	public $sections;

	public function getName()
	{
		return Craft::t('Reverse Related Entries');
	}

	public function getInputHtml($value, ElementInterface $element = null): string
	{

		$relatedElements = $relatedCategories = array();
		$allowedSections = $this->sections;

		// these are all we're looking up for now.
		// Users will come later. Tags too I guess.
		$elementTypes = array(
			MatrixBlock::class => '',
			Entry::class => '',
			Category::class => ''
		);

		// shortcut
		$introvert = new IntrovertService();

		foreach($elementTypes as $key => $val)
		{
			$relatedElements[$key] = $introvert->getRelationships($key, $element, $allowedSections);
		}

		//Craft::dd($relatedElements);

		// combine entries and matrix entries
		$relatedEntries = $relatedElements['craft\elements\Entry'] + $relatedElements['craft\elements\MatrixBlock'];
		$relatedCategories = $relatedElements['craft\elements\Category'];

		// sort our entries by title
		$introvert->sortRelArray( $relatedEntries );
		$introvert->sortRelArray( $relatedCategories );

		return Craft::$app->view->renderTemplate(
			'introvert/input', array(
				'entries' 	=> $relatedEntries,
				'categories' => $relatedCategories,
				'elementType' => get_class($element)
			)
		);

	}

	protected function defineSettings()
	{
		return array(
			'sections' => AttributeType::Mixed
		);
	}

	public function getSettingsHtml()
	{

		$sections = Craft::$app->sections->getAllSections();
		
		$sectionOptions = array();
		foreach($sections as $section)
		{
			$sectionOptions[ $section->id ] = $section->name;
		}

		return Craft::$app->view->renderTemplate(
			'introvert/settings', array(
				'settings' 			=> $this->getSettings(),
				'sectionOptions' 	=> $sectionOptions
			)
		);
	}

	public function getTableAttributeHtml($value, craft\base\ElementInterface $element): string
	{
		$relatedElements = $relatedCategories = array();
		$allowedSections = $this->getSettings()->sections;

		// these are all we're looking up for now.
		// Users will come later. Tags too I guess.
		$elementTypes = array(
			ElementType::MatrixBlock => '',
			ElementType::Entry => '',
			ElementType::Category => ''
		);

		// shortcut
		$introvert = Craft::$app->introvert_relationship;

		foreach($elementTypes as $key => $value)
		{
			$relatedElements[$key] = $introvert->getRelationships($key, $this->element, $allowedSections);
		}

		// combine entries and matrix entries
		$relatedEntries = $relatedElements[ElementType::Entry] + $relatedElements[ElementType::MatrixBlock];
		$relatedCategories = $relatedElements[ElementType::Category];

		// sort our entries by title
		$introvert->sortRelArray( $relatedEntries );
		$introvert->sortRelArray( $relatedCategories );

		return Craft::$app->view->renderTemplate(
			'introvert/table', array(
				'entries' 	=> $relatedEntries,
				'categories' => $relatedCategories,
				'elementType' => $this->element->elementType
			)
		);
	}
}

