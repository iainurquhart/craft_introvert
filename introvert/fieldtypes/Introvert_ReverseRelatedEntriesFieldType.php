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

		// get our related entries if they exist:
		$criteria = craft()->elements->getCriteria(ElementType::Entry);
		$criteria->relatedTo = array('targetElement' => $this->element);
		$criteria->order = "title DESC";
		
		if($this->getSettings()->sections != '*')
		{
			$criteria->sectionId = $this->getSettings()->sections;
		}

		$relatedEntries = craft()->elements->findElements($criteria);

		return craft()->templates->render(
			'introvert/input', array(
				'entries' 	=> $relatedEntries
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

