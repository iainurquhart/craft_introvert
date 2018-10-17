<?php
namespace iainurquhart\introvert;

use Craft;
use craft\base\Plugin;
use yii\base\Event;
use craft\services\Fields;
use iainurquhart\introvert\fields\Introvert_ReverseRelatedEntries;
use craft\events\RegisterComponentTypesEvent;

class Introvert extends Plugin
{

	public static $plugin;

	function getName()
	{
		 return Craft::t('Introvert');
	}

	function getVersion()
	{
		return '2.0';
	}

	function getDeveloper()
	{
		return 'Iain Urquhart';
	}

	function getDeveloperUrl()
	{
		return 'http://iain.co.nz';
	}

	public function init()
	{
		parent::init();
		self::$plugin = $this;
		
		Event::on(Fields::className(), Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = Introvert_ReverseRelatedEntries::class;
        });
	}

}