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
class modJoomStatsHelper
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
  public static function getList(&$params, &$debugmode)
  {

    // Array for storing the e.g. results and db query clauses
    $elems = array();

    // Retreive parameters and fill the array
    // Get parameters for images and fill array element
    $elems['allpics'] = new stdClass();
    $elems['allpics']->enabled = intval($params->get('all_pics', false));
    if($elems['allpics']->enabled)
    {
      $elems['allpics']->outputtext  = $params->get('all_picstext', '');
      $elems['allpics']->queryselect = 'COUNT(id)';
      $elems['allpics']->queryfrom   = '#__joomgallery';
    }

    // Get parameters for categories and fill array element
    $elems['allcats'] = new stdClass();
    $elems['allcats']->enabled = intval($params->get('all_cats'));
    if($elems['allcats']->enabled)
    {
      $elems['allcats']->outputtext  = $params->get('all_catstext');
      $elems['allcats']->queryselect = 'COUNT(cid)';
      $elems['allcats']->queryfrom   = '#__joomgallery_catg';
    }

    // Get parameters for hits and fill array element
    $elems['allhits'] = new stdClass();
    $elems['allhits']->enabled = intval($params->get('all_hits', false));
    if($elems['allhits']->enabled)
    {
      $elems['allhits']->outputtext  = $params->get('all_hitstext', '');
      $elems['allhits']->queryselect = 'sum(hits)';
      $elems['allhits']->queryfrom   = '#__joomgallery';
    }

    // Get parameters for comments and fill array element
    $elems['allcomments'] = new stdClass();
    $elems['allcomments']->enabled = intval($params->get('all_comments'), false);
    if($elems['allcomments']->enabled)
    {
      $elems['allcomments']->outputtext  = $params->get('all_commentstext', '');
      $elems['allcomments']->queryselect = 'COUNT(cmtid)';
      $elems['allcomments']->queryfrom   = '#__joomgallery_comments';
    }

    // Get parameters for votes and fill array element
    $elems['allvotes'] = new stdClass();
    $elems['allvotes']->enabled = intval($params->get('all_votes', false));
    if($elems['allvotes']->enabled)
    {
      $elems['allvotes']->outputtext  = $params->get('all_votestext', '');
      $elems['allvotes']->queryselect = 'COUNT(voteid)';
      $elems['allvotes']->queryfrom   = '#__joomgallery_votes';
    }

    // Get parameters for nametags and fill array element
    $elems['allnametags'] = new stdClass();
    $elems['allnametags']->enabled = intval($params->get('all_nametags', false));
    if($elems['allnametags']->enabled)
    {
      $elems['allnametags']->outputtext  = $params->get('all_nametagstext', '');
      $elems['allnametags']->queryselect = 'COUNT(nid)';
      $elems['allnametags']->queryfrom   = '#__joomgallery_nameshields';
    }

    // Get instance of database object ans set debug mode if enabled
    $database = JFactory::getDBo();

    // Get an instance of JDatabaseQuery object and clear them initially
    $query = $database->getQuery(true);

    // Set debug mode of database to fill the log array of database object
    // if debug mode enabled
    if($debugmode)
    {
      $database->setdebug(true);
    }

    // Iterate through array
    // reference to element for adding the results (possible since PHP5)
    foreach($elems as &$elem)
    {
      if($elem->enabled)
      {
        // Fill the select and from clause of query
        $query->select($elem->queryselect);
        $query->from($elem->queryfrom);

        // Set the query and get the result from database
        $database->setQuery($query);
        $elem->outputresult = $database->loadResult();
      }
      // Clear the query for next element
      $query->clear();
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