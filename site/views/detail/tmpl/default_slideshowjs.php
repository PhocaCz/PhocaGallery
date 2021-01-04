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
$document			= JFactory::getDocument();
//$document->addScript(JURI::base(true).'/media/com_phocagallery/js/jquery/jquery-1.6.4.min.js');
Joomla\CMS\HTML\HTMLHelper::_('jquery.framework', false);// Load it here because of own nonConflict method (nonconflict is set below)
$document->addScript(JURI::base(true).'/media/com_phocagallery/js/fadeslideshow/fadeslideshow.js');

if($this->t['responsive'] == 1) {
	$iW = '\'100%\'';
	$iH = '\'100%\''; // DOES NOT WORK IN FADESLIDESHOW
	//$iH = $this->t['largeheight'];
} else {
	$iW = $this->t['largewidth'];
	$iH = $this->t['largeheight'];
}

?><script type="text/javascript">
/***********************************************
* Ultimate Fade In Slideshow v2.0- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
***********************************************/
var phocagallery=new fadeSlideShow({
	wrapperid: "phocaGallerySlideshowC",
	dimensions: [<?php echo $iW; ?>, <?php echo $iH; ?>],
	imagearray: [<?php echo $this->item->slideshowfiles ;?>],
	displaymode: {type:'auto', pause: <?php echo $this->t['slideshow_pause'] ?>, cycles:0, wraparound:false, randomize: <?php echo $this->t['slideshowrandom'] ?>},
	persist: false,
	fadeduration: <?php echo $this->t['slideshow_delay'] ?>,
	descreveal: "<?php echo $this->t['slideshow_description'] ?>",
	togglerid: "",
})
</script>
