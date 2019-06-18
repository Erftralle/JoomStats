<?php
/****************************************************************************************\
**   Module JoomStats for JoomGallery                                                   **
**   By: JoomGallery::ProjectTeam                                                       **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/
defined('_JEXEC') or die('Restricted access');

$jg_installed  = null;
$jg_ifpath     = JPATH_ROOT.'/components/com_joomgallery/interface.php';
$jg_minversion = '3.0';

if(file_exists($jg_ifpath))
{
  // Include JoomGallery's interface class
  require_once $jg_ifpath;

  // Include the helper functions only once
  require_once dirname(__FILE__).'/helper.php';

  // Create an instance of the helper object
  $helperObject = new modJoomStatsHelper();

  // Check gallery version
  if(version_compare($helperObject->getGalleryVersion(), $jg_minversion, '>='))
  {
    // Correct version of JoomGallery seems to be installed
    $jg_installed = true;

    $debugmode  = $params->get('debug', 0);
    $list       = $helperObject->getList($params, $debugmode);
  }
}

require JModuleHelper::getLayoutPath('mod_joomstats', $params->get('layout', 'default'));