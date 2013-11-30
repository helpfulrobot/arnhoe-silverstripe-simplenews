<?php
/**
 * original code part of github.com/dospuntocero/doarticles
 *
 * @package arnhoe/silverstripe-simplenews
 * @author Arno Poot <mail@arnop.nl>
 *
 */
class NewsURLSegment extends DataExtension {

	private static $db = array(
		"URLSegment" => "Varchar(255)"
	);

	private static $indexes = array(
		"URLSegment" => true
	);

	function onBeforeWrite() {
		$this->owner->URLSegment = singleton("SiteTree")->generateURLSegment($this->owner->Title);
	}
}
