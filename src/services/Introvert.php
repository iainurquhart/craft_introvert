<?php
namespace iainurquhart\introvert\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\elements\Category;

class Introvert extends Component
{
	var $type;
	var $element;
	var $sections;

	public function getRelationships($type, $element, $sections = '*')
	{
		$this->type 	= $type;
		$this->element 	= $element;
		$this->sections = $sections;

		// no relationships if element is new
		if($this->element->id === null)
		{
			return array();
		}

		switch($type)
		{
			case 'craft\elements\MatrixBlock';
				$elements = MatrixBlock::find()->relatedTo(['targetElement' => $element])->all();	
				return $this->_parseMatrixBlocks($elements);
				break;

				case 'craft\elements\Entry';
				$elements = Entry::find()->relatedTo(['targetElement' => $element])->all();	
				return $this->_parseEntries($elements);
				break;

				case 'craft\elements\Category';
				$elements = Category::find()->relatedTo(['targetElement' => $element])->all();	
				return $this->_parseCategories($elements);
				break;
		}

	}

	// -------------------------------------------------------------

	public function sortRelArray(&$array, $sortKey = 'title')
	{
		$values = array();
		foreach ($array as $key => $row)
		{
			$values[$key] = $row[$sortKey];
		}
		array_multisort($values, SORT_ASC, $array);
	}

	// -------------------------------------------------------------

	private function _parseCategories($elements)
	{
		$relatedCategories = array();
		foreach($elements as $category)
		{
			$relatedCategories[$category->id] = $this->_extractCategoryVars($category);
		}
		return $relatedCategories;
	}

	// -------------------------------------------------------------

	private function _parseEntries($elements)
	{
		$relatedElements = array();
		foreach($elements as $entry)
		{
			if($this->_inSelectedSections($entry))
			{
				$relatedElements[$entry->id] = $this->_extractEntryVars($entry);
			}
		}
		return $relatedElements;
	}

	// -------------------------------------------------------------

	private function _parseMatrixBlocks($elements)
	{

		$relatedElements = array();
		foreach($elements as $childElement)
		{
			// find the owner entry element
			$owner = Craft::$app->elements->getElementById($childElement->ownerId, ElementType::Entry);

			// only add to relatedElements if it's within our field's selected sections preference
			if($this->_inSelectedSections($owner))
			{
				$relatedElements[$childElement->ownerId] = $this->_extractEntryVars($owner);
			}
		}
		return $relatedElements;
	}

	// -------------------------------------------------------------

	private function _inSelectedSections($element)
	{	
		return (($this->sections == '*') || ($this->sections != '*' && in_array($element->sectionId, $this->sections)))
			? TRUE : FALSE;
	}

	// -------------------------------------------------------------

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
			//'type' 		=> $element->elementType
		);
	}

	// -------------------------------------------------------------

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
			//'type' 		=> $element->elementType
		);
	}

	


}