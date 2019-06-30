<?php
/****************************************************************************************\
**   Module JoomStats for JoomGallery                                                   **
**   By: JoomGallery::ProjectTeam                                                       **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Helper class for module JoomStats
 *
 */
class modJoomStatsHelper extends JoomInterface
{
  /**
   *
   * Get parameters from backend, prepare and execute the database queries,
   * and fill an array for later output in template
   *
   * @param \Joomla\Registry\Registry   &$params    object holding the models parameters$params
   * @param boolean                     $debugmode  true to output the database query and the
   *                                                database error text if existent
	 * @return array   array of objects containing the statitics
   *
   */
  public function getList(&$params, &$debugmode)
  {

    // Array for storing the e.g. results and db query clauses
    $elems    = array();
    // Get instance of user object
    $user     = JFactory::getUser();
    // Get instance of database object
    $database = JFactory::getDBo();

    // Load access view levels
    $authorised_viewlevels = implode(',', $user->getAuthorisedViewLevels());

    // Set debug mode of database to fill the log array of database object
    // if debug mode enabled
    if($debugmode)
    {
      $database->setdebug(true);
    }

    // Retreive parameters and fill the array
    // Get parameters for images and fill array element
    $elems['allpics']           = new stdClass();
    $elems['allpics']->enabled  = intval($params->get('all_pics', false));

    if($elems['allpics']->enabled)
    {
      $elems['allpics']->outputtext  = $params->get('all_picstext', '');
      $elems['allpics']->query       = $database->getQuery(true)
                              ->select('COUNT(a.id)')
                              ->from('#__joomgallery'.' AS a')
                              ->InnerJoin('#__joomgallery_catg'.' AS c ON c.cid = a.catid')
                              ->where('a.published = 1')
                              ->where('a.approved  = 1')
                              ->where('a.hidden = 0')
                              ->where('a.access IN ('.$authorised_viewlevels.')')
                              ->where('c.published = 1')
                              ->where('c.hidden = 0')
                              ->where('c.access IN ('.$authorised_viewlevels.')')
                              ->where('(c.password = '.$database->q('').' OR c.cid IN ('.implode(',', JFactory::getApplication()->getUserState('joom.unlockedCategories', array(0))).'))');
    }

    // Get parameters for categories and fill array element
    $elems['allcats']           = new stdClass();
    $elems['allcats']->enabled  = intval($params->get('all_cats'));

    if($elems['allcats']->enabled)
    {
      $elems['allcats']->outputtext  = $params->get('all_catstext');
      $elems['allcats']->query       = $database->getQuery(true)
                              ->select('COUNT(c.cid)')
                              ->from('#__joomgallery_catg'.' AS c')
                              ->where('c.published = 1')
                              ->where('c.cid != 1')
                              ->where('c.hidden = 0');

      if(!$this->getJConfig('jg_showrestrictedcats', 0))
      {
        $elems['allcats']->query->where('c.access IN ('.$authorised_viewlevels.')');
      }
    }

    // Get parameters for hits and fill array element
    $elems['allhits']           = new stdClass();
    $elems['allhits']->enabled  = intval($params->get('all_hits', false));

    if($elems['allhits']->enabled)
    {
      $elems['allhits']->outputtext  = $params->get('all_hitstext', '');
      $elems['allhits']->query       = $database->getQuery(true)
                              ->select('SUM(a.hits)')
                              ->from('#__joomgallery'.' AS a')
                              ->InnerJoin('#__joomgallery_catg'.' AS c ON c.cid = a.catid')
                              ->where('a.published = 1')
                              ->where('a.approved  = 1')
                              ->where('a.hidden = 0')
                              ->where('a.access IN ('.$authorised_viewlevels.')')
                              ->where('c.published = 1')
                              ->where('c.hidden = 0')
                              ->where('c.access IN ('.$authorised_viewlevels.')')
                              ->where('(c.password = '.$database->q('').' OR c.cid IN ('.implode(',', JFactory::getApplication()->getUserState('joom.unlockedCategories', array(0))).'))');
    }

    // Get parameters for comments and fill array element
    $elems['allcomments']           = new stdClass();
    $elems['allcomments']->enabled  = intval($params->get('all_comments'), false);

    if($elems['allcomments']->enabled)
    {
      $elems['allcomments']->outputtext = $params->get('all_commentstext', '');
      $elems['allcomments']->query      = $database->getQuery(true)
                                  ->select('COUNT(com.cmtid)')
                                  ->from('#__joomgallery_comments'.' AS com')
                                  ->InnerJoin('#__joomgallery'.' AS a ON com.cmtpic = a.id')
                                  ->InnerJoin('#__joomgallery_catg'.' AS c ON c.cid = a.catid')
                                  ->where('com.published = 1')
                                  ->where('com.approved  = 1')
                                  ->where('a.published = 1')
                                  ->where('a.approved  = 1')
                                  ->where('a.hidden = 0')
                                  ->where('a.access IN ('.$authorised_viewlevels.')')
                                  ->where('c.published = 1')
                                  ->where('c.hidden = 0')
                                  ->where('c.access IN ('.$authorised_viewlevels.')')
                                  ->where('(c.password = '.$database->q('').' OR c.cid IN ('.implode(',', JFactory::getApplication()->getUserState('joom.unlockedCategories', array(0))).'))');
    }

    // Get parameters for votes and fill array element
    $elems['allvotes'] = new stdClass();
    $elems['allvotes']->enabled = intval($params->get('all_votes', false));

    if($elems['allvotes']->enabled)
    {
      $elems['allvotes']->outputtext = $params->get('all_votestext', '');
      $elems['allvotes']->query      = $database->getQuery(true)
                               ->select('COUNT(v.voteid)')
                               ->from('#__joomgallery_votes'.' AS v')
                               ->InnerJoin('#__joomgallery'.' AS a ON v.picid = a.id')
                               ->InnerJoin('#__joomgallery_catg'.' AS c ON c.cid = a.catid')
                               ->where('a.published = 1')
                               ->where('a.approved  = 1')
                               ->where('a.hidden = 0')
                               ->where('a.access IN ('.$authorised_viewlevels.')')
                               ->where('c.published = 1')
                               ->where('c.hidden = 0')
                               ->where('c.access IN ('.$authorised_viewlevels.')')
                               ->where('(c.password = '.$database->q('').' OR c.cid IN ('.implode(',', JFactory::getApplication()->getUserState('joom.unlockedCategories', array(0))).'))');
    }

    // Get parameters for nametags and fill array element
    $elems['allnametags'] = new stdClass();
    $elems['allnametags']->enabled = intval($params->get('all_nametags', false));

    if($elems['allnametags']->enabled)
    {
      $elems['allnametags']->outputtext = $params->get('all_nametagstext', '');
      $elems['allnametags']->query      = $database->getQuery(true)
                                  ->select('COUNT(n.nid)')
                                  ->from('#__joomgallery_nameshields'.' AS n')
                                  ->InnerJoin('#__joomgallery'.' AS a ON n.npicid = a.id')
                                  ->InnerJoin('#__joomgallery_catg'.' AS c ON c.cid = a.catid')
                                  ->where('a.published = 1')
                                  ->where('a.approved  = 1')
                                  ->where('a.hidden = 0')
                                  ->where('a.access IN ('.$authorised_viewlevels.')')
                                  ->where('c.published = 1')
                                  ->where('c.hidden = 0')
                                  ->where('c.access IN ('.$authorised_viewlevels.')')
                                  ->where('(c.password = '.$database->q('').' OR c.cid IN ('.implode(',', JFactory::getApplication()->getUserState('joom.unlockedCategories', array(0))).'))');
    }

    // Iterate through array
    // reference to element for adding the results (possible since PHP5)
    foreach($elems as &$elem)
    {
      if($elem->enabled)
      {
        // Set the query and get the result from database
        $database->setQuery($elem->query);
        $elem->outputresult = $database->loadResult();
      }
    }

    if($debugmode)
    {
      // Get the database log array
      $log = $database->getLog();

      // And add the text to elements
      $logcount = 0;

      foreach($elems as &$elem)
      {
        if($elem->enabled)
        {
          $elem->dbquerylog = '&#8226;'.nl2br($log[$logcount], true).'&#8226;<br />';
          $logcount++;
        }
      }

      $database->debug(0);
    }

    return $elems;
  }
}