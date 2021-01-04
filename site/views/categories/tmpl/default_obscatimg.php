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
echo "\n\n";
for ($i = 0; $i < $this->t['countcategories']; $i++) {
	if ( (int)$this->t['categoriescolumns'] == 1 ) {
		echo '<table border="0">'."\n";
	} else {
		$float = 0;
		foreach ($this->t['begin'] as $k => $v) {
			if ($i == $v) {
				$float = 1;
			}
		}
		if ($float == 1) {		
			echo '<div style="'.$this->t['fixedwidthstyle1'].'" class="pg-cats-box-float"><table>'."\n";
		}
	}

	echo '<tr>'."\n";		
	echo '<td align="center" valign="middle" style="'.$this->t['imagebg'].';text-align:center;"><div class="pg-imgbg"><a href="'.$this->categories[$i]->link.'">';

	if (isset($this->categories[$i]->extpic) && $this->categories[$i]->extpic) {
		$correctImageRes = PhocaGalleryPicasa::correctSizeWithRate($this->categories[$i]->extw, $this->categories[$i]->exth, $this->t['picasa_correct_width'], $this->t['picasa_correct_height']);
		echo Joomla\CMS\HTML\HTMLHelper::_( 'image', $this->categories[$i]->linkthumbnailpath, str_replace('&raquo;', '-',$this->categories[$i]->title), array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height'], 'style' => ''));
	} else {
		echo Joomla\CMS\HTML\HTMLHelper::_( 'image', $this->categories[$i]->linkthumbnailpath, str_replace('&raquo;','-',$this->categories[$i]->title),array('style' => ''));
	}
	
	echo '</a></div></td>';
	echo '<td><a href="'.$this->categories[$i]->link.'">'.$this->categories[$i]->title.'</a>&nbsp;';
	
	if ($this->categories[$i]->numlinks > 0) {echo '<span class="small">('.$this->categories[$i]->numlinks.')</span>';}
	
	echo '</td>';
	echo '</tr>'."\n";
	
	if ( (int)$this->t['categoriescolumns'] == 1 ) {
		echo '</table>'."\n";
	} else {
		if ($i == $this->t['endfloat']) {
			echo '</table></div><div style="clear:both"></div>'."\n";
		} else {
			$float = 0;
			foreach ($this->t['end'] as $k => $v)
			{
				if ($i == $v) {
					$float = 1;
				}
			}
			if ($float == 1) {		
				echo '</table></div>'."\n";
			}
		}
	}
}
echo "\n";
?>
