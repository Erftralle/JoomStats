<?php
defined('_JEXEC') or die('Restricted access');
?>
<ul class="joomstats_<?php echo $params->get('moduleclass_sfx'); ?>">
<?php
// Number of items
foreach($list as $listelem):
  if($listelem->enabled):
?>
  <li>
<?php
    // Output of resolved query db and error text if debug mode enabled
    if($debugmode):
?>
    <span class="joomstats_dbquery"><?php echo $listelem->dbquerylog;?></span>
<?php
    endif;
?>
    <span class="joomstats_text"><?php echo $listelem->outputtext;?></span>
    <span class="joomstats_result"><?php echo $listelem->outputresult;?></span>
  </li>
<?php
  endif;
endforeach;
?>
</ul>