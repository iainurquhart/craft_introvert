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

		$relatedElements = array();
		$relatedCategories = array();


		//  ---------------------------------------------
		//  find our related matrix blocks if they exist
		//  ---------------------------------------------
			$criteria = craft()->elements->getCriteria(ElementType::MatrixBlock);
			$criteria->relatedTo = array('targetElement' => $this->element);
			$relatedMatrixBlocks = craft()->elements->findElements($criteria);

			// create an array of each matrix block's parent elements/entries
			foreach($relatedMatrixBlocks as $block)
			{
				// find the owner entry element
				$owner = craft()->elements->getElementById($block->ownerId, ElementType::Entry);

				// only add to relatedElements if it's within our field's selected sections preference
				if(($this->getSettings()->sections == '*') || ($this->getSettings()->sections != '*' && in_array($owner->sectionId, $this->getSettings()->sections)))
				{
					$relatedElements[$block->ownerId] = $this->_extractEntryVars($owner);
				}
			}
		//  ---------------------------------------------



		//  ---------------------------------------------
		//  get our related entries if they exist:
		//  ---------------------------------------------
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->relatedTo = array('targetElement' => $this->element);
			$criteria->order = "title ASC";
			
			if($this->getSettings()->sections != '*')
			{
				$criteria->sectionId = $this->getSettings()->sections;
			}

			$relatedEntries = craft()->elements->findElements($criteria);

			foreach($relatedEntries as $entry)
			{
				$relatedElements[$entry->id] = $this->_extractEntryVars($entry);
			}
		//  ---------------------------------------------

		//  ---------------------------------------------
		//  Sort our entry results alphabetically by title
		//  ---------------------------------------------
			if($relatedElements)
			{
				$titles = array();
				foreach ($relatedElements as $key => $row)
				{
					$titles[$key] = $row['title'];
				}
				array_multisort($titles, SORT_ASC, $relatedElements);
			}

		//  ---------------------------------------------


		//  ---------------------------------------------
		//  get our related categories if they exist:
		//  ---------------------------------------------
			$criteria = craft()->elements->getCriteria(ElementType::Category);
			$criteria->relatedTo = array('targetElement' => $this->element);

			$categories = craft()->elements->findElements($criteria);
			
			foreach($categories as $category)
			{
				$relatedCategories[$category->id] = $this->_extractCategoryVars($category);
			}

			//  ---------------------------------------------
			//  Sort our category results alphabetically by title
			//  ---------------------------------------------
			if($categories)
			{
				$titles = array();
				foreach ($categories as $key => $row)
				{
					$titles[$key] = $row['title'];
				}
				array_multisort($titles, SORT_ASC, $relatedCategories);
			}
			
		//  ---------------------------------------------


		return craft()->templates->render(
			'introvert/input', array(
				'entries' 	=> $relatedElements,
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


//  ---------------------------------------------
//  Private methods
//  ---------------------------------------------

	private function _extractEntryVars($element)
	{
		return array(
			'id' 		=> $element->id,
			'title' 	=> $element->title,
			'sectionId' => $element->sectionId,
			'section' 	=> $element->section->name,
			'handle' 	=> $element->section->handle,
			'postDate' 	=> $element->postDate,
			'status' 	=> $element->status,
			'cpEditUrl' => $element->cpEditUrl,
			'url' 		=> $element->url,
			'uri'		=> $element->uri,
			'type' 		=> $element->elementType
		);
	}

	private function _extractCategoryVars($element)
	{
		return array(
			'id' 		=> $element->id,
			'title' 	=> $element->title,
			'sectionId' => $element->groupId,
			'section' 	=> $element->group->name,
			'handle' 	=> $element->group->handle,
			'postDate' 	=> $element->dateCreated,
			'status' 	=> $element->status,
			'type' 		=> $element->elementType
		);
	}

/*	
	private function dumper($dump)
	{
		echo "<pre>";
		print_r($dump);
		echo "</pre>";
	}
*/	

}

