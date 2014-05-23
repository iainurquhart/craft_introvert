<?php
namespace Craft;

class Introvert_ReverseRelatedEntriesFieldType extends BaseFieldType
{

	public function getName()
	{
		return Craft::t('Reverse Related Entries');
	}

	public function getInputHtml($name, $value)
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
		$introvert = craft()->introvert_relationship;

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

		return craft()->templates->render(
			'introvert/input', array(
				'entries' 	=> $relatedEntries,
				'categories' => $relatedCategories,
				'elementType' => $this->element->elementType
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

		$sections = craft()->sections->getAllSections();
		
		$sectionOptions = array();
		foreach($sections as $section)
		{
			$sectionOptions[ $section->id ] = $section->name;
		}

		return craft()->templates->render(
			'introvert/settings', array(
				'settings' 			=> $this->getSettings(),
				'sectionOptions' 	=> $sectionOptions
			)
		);
	}


}

