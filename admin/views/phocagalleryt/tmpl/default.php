<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
Factory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) {
	if (task == "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById("phocagalleryt-form"))) {
		Joomla.submitform(task, document.getElementById("phocagalleryt-form"));
	} else {
		return false;
	}
}'

);


echo '<div>' .  Text::_( 'COM_PHOCAGALLERY_CURRENT_THEME' ) .' : <b>' .  $this->theme_name . '</b></div>';
echo '<p>&nbsp;</p>';
echo '<div>' .  Text::_( 'COM_PHOCAGALLERY_CURRENT_THEME_INSTALLATION_PROCESS' ) .'</div>';

?>

<form action="index.php" method="post" name="adminForm" id="phocagalleryt-form" class="form-validate">
<input type="hidden" name="type" value="" />
<input type="hidden" name="option" value="com_phocagallery" />
<input type="hidden" name="task" value="phocagalleryt.themeinstall" />
<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>

<?php
echo '<h4>'.Text::_('COM_PHOCAGALLERY_EDIT_CSS_FILES'). '</h4>';
echo '<ul class="nav nav-tabs nav-stacked"><li><a href="index.php?option=com_phocagallery&view=phocagalleryefs"><i class="icon-edit"></i> '.Text::_('COM_PHOCAGALLERY_EDIT_CSS_FILES'). '</a></li></ul>';
?>

<p>&nbsp;</p><div class="btn-group"><a class="btn btn-large btn-primary" href="https://www.phoca.cz/themes/" target="_blank"><i class="icon-grid-view-2 icon-white"></i>&nbsp;&nbsp;<?php echo Text::_('COM_PHOCAGALLERY_NEW_THEME_DOWNLOAD'); ?></a></div>

