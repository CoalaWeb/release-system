<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/**
 * This file is only necessary for the backend dispatcher not to load
 */
class ArsDispatcher extends FOFDispatcher
{
	public $defaultView = 'browse';

	private $allowedViews = array(
		'browses','categories','downloads','latests','releases','updates',
		'items','dlidlabels'
	);

	public function onBeforeDispatch() {
		$result = parent::onBeforeDispatch();
		if($result) {
			// Load Akeeba Strapper
			include_once JPATH_ROOT.'/media/akeeba_strapper/strapper.php';
			AkeebaStrapper::bootstrap();
			AkeebaStrapper::jQueryUI();
			AkeebaStrapper::addCSSfile('media://com_ars/css/backend.css');

			// Default to the "browses" view
			$view = $this->input->getCmd('view',$this->defaultView);
			if(empty($view) || ($view == 'cpanel')) {
				$view = 'browses';
			}

			// Set the view, if it's allowed
			$this->input->set('view',$view);
			if(!in_array(FOFInflector::pluralize($view), $this->allowedViews)) $result = false;

			if( ($this->input->getCmd('format','html') == 'html') &&
				(FOFInflector::pluralize($view) == 'items') ) {
				$result = false;
			} elseif(FOFInflector::pluralize($view) == 'items') {
				// Require admin
				if(!JFactory::getUser()->authorise('core.manage','com_ars')) {
					JError::raiseError(403, 'Forbidden');
					$result = false;
				}
			}
		}

		return $result;
	}
}