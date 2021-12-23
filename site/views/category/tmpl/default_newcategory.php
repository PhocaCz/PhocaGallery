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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

echo '<div id="phocagallery-category-creating">';

echo '<h4>' . Text::_('COM_PHOCAGALLERY_CREATE') . '</h4>';

echo '<form action="' . htmlspecialchars($this->t['action']) . '" name="phocagallerycreatesubcatform" id="phocagallery-create-subcat-form" method="post" >';

?>
<table>
    <tr>
        <td><strong><?php echo Text::_('COM_PHOCAGALLERY_SUBCATEGORY'); ?>:</strong></td>
        <td><input type="text" id="subcategoryname" name="subcategoryname" maxlength="255" class="form-control comment-input" value=""/></td>
    </tr>
    <tr>
        <td><strong><?php echo Text::_('COM_PHOCAGALLERY_DESCRIPTION'); ?>:</strong></td>
        <td><textarea id="phocagallery-create-subcat-description" name="phocagallerycreatesubcatdescription" onkeyup="countCharsCreateSubCat();" cols="30" rows="10" class="form-control comment-input"></textarea></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><?php echo Text::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN'); ?>
            <input name="phocagallerycreatesubcatcountin" value="0" readonly="readonly" class="form-control comment-input2"/>
            <?php echo Text::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION'); ?>
            <input name="phocagallerycreatesubcatcountleft" value="<?php echo $this->t['max_create_cat_char']; ?>" readonly="readonly" class="form-control comment-input2"/></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="right"><input type="submit" onclick="return(checkCreateSubCatForm());" id="phocagallerycreatesubcatsubmit" class="btn btn-primary" value="<?php echo Text::_('COM_PHOCAGALLERY_CREATE_SUBCATEGORY'); ?>"/></td>
    </tr>
</table>

<?php echo HTMLHelper::_('form.token'); ?>
<input type="hidden" name="task" value="createsubcategory"/>
<input type="hidden" name="controller" value="category"/><input type="hidden" name="view" value="category"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['createsubcategory']; ?>"/>
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="catid" value="<?php echo $this->category->slug ?>"/>
<input type="hidden" name="parentcategoryid" value="<?php echo $this->category->slug ?>"/></form>

<?php echo '</div>'; ?>
