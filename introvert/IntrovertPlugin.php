<?php
namespace Craft;

class IntrovertPlugin extends BasePlugin
{

	function getName()
	{
		 return Craft::t('Introvert');
	}

	function getVersion()
	{
		return '0.1.1';
	}

	function getDeveloper()
	{
		return 'Iain Urquhart';
	}

	function getDeveloperUrl()
	{
		return 'http://iain.co.nz';
	}

}