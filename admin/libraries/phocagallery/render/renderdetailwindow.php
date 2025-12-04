<?php
/**
 * @package   Phoca Gallery
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

class PhocaGalleryRenderDetailWindow
{

	public $b1;// Image
	public $b2;// Zoom Icon
	public $b3;// Map, Exif, ...

	public $popupHeight;
	public $popupWidth;
	public $mbOverlayOpacity; // Modal Box
	public $sbSlideshowDelay; // Shadowbox
	public $sbSettings;
	public $hsSlideshow;      // Highslide
	public $hsClass;
	public $hsOutlineType;
	public $hsOpacity;
	public $hsCloseButton;
	public $jakDescHeight;	  // JAK
	public $jakDescWidth;
	public $jakOrientation;
	public $jakSlideshowDelay;
	public $bpBautocenter;     // boxplus
	public $bpAutofit;
	public $bpSlideshow;
	public $bpLoop;
	public $bpCaptions;
	public $bpThumbs;
	public $bpDuration;
	public $bpTransition;
	public $bpContextmenu;
	public $extension;
	public $jakRandName;
	public $articleId;
	public $backend;


	public function __construct() {}

	public function setButtons($method = 0, $libraries = array(), $library = array()) {

		$app      	= Factory::getApplication();
		$document	= $app->getDocument();
		$paramsC 	= ComponentHelper::getParams('com_phocagallery') ;
		$wa 		= $app->getDocument()->getWebAssetManager();

		/*$this->b1 = new stdClass();
		$this->b1->name = 'image';
		$this->b1->options = '';

		$this->b2 = new stdClass();
		$this->b2->name = 'icon';
		$this->b2->options = '';

		$this->b3 = new stdClass();
		$this->b3->name = 'other';
		$this->b3->options = '';
		$this->b3->optionsrating = '';

		$path = Uri::base(true);
		if ($this->backend == 1) {
			$path = Uri::root(true);
		}*/


		switch($method) {

			case 1:
			//STANDARD JS POPUP
			/*$this->b1->methodname 		= 'pg-js-nopopup-button';
			$this->b1->options 			= "window.open(this.href,'win2','width=".$this->popupWidth.",height=".$this->popupHeight.",scrollbars=yes,menubar=no,resizable=yes'); return false;";
			$this->b1->optionsrating 	= "window.open(this.href,'win2','width=".$this->popupWidth.",height=".$this->popupHeight.",scrollbars=yes,menubar=no,resizable=yes'); return false;";

			$this->b2->methodname 		= $this->b1->methodname;
			$this->b2->options 			= $this->b1->options;
			$this->b3->methodname  		= $this->b1->methodname;
			$this->b3->options 			= $this->b1->options;
			$this->b3->optionsrating 	= $this->b1->optionsrating;*/
			break;

			case 0:
			// BOOTSTRAP MODAL
			/*$this->b1->name 			= 'image';
			$this->b1->methodname 		= 'pg-bs-modal-button';
			$this->b1->options			= '';
			$this->b1->optionsrating	= '';


			$this->b2->methodname 	= $this->b1->methodname;
			$this->b2->options		= '';
			$this->b2->optionsrating= '';
			$this->b3->methodname  	= $this->b1->methodname;
			$this->b3->options		= '';
			$this->b3->optionsrating= '';*/

			break;

			case 7:
			// NO POPUP
			/*$this->b1->methodname 	= 'pg-no-popup';
			$this->b2->methodname 	= $this->b1->methodname;
			$this->b3->methodname 	= $this->b1->methodname;*/

			break;

			case 12:
				// MAGNIFIC

				HTMLHelper::_('jquery.framework', true);

				$oLang   = array(
                    'COM_PHOCAGALLERY_LOADING' => Text::_('COM_PHOCAGALLERY_LOADING'),
                    'COM_PHOCAGALLERY_CLOSE' => Text::_('COM_PHOCAGALLERY_CLOSE'),
                    'COM_PHOCAGALLERY_PREVIOUS' => Text::_('COM_PHOCAGALLERY_PREVIOUS'),
                    'COM_PHOCAGALLERY_NEXT' => Text::_('COM_PHOCAGALLERY_NEXT'),
                    'COM_PHOCAGALLERY_MAGNIFIC_CURR_OF_TOTAL' => Text::_('COM_PHOCAGALLERY_MAGNIFIC_CURR_OF_TOTAL'),
                    'COM_PHOCAGALLERY_IMAGE_NOT_LOADED' => Text::_('COM_PHOCAGALLERY_IMAGE_NOT_LOADED')

                );

                $document->addScriptOptions('phLangPG', $oLang);


				//$document->addScript(Uri::base(true).'/media/com_phocagallery/js/magnific/jquery.magnific-popup.min.js');
				//$document->addScript(Uri::base(true).'/media/com_phocagallery/js/magnific/magnific-initialize.js');
				//$document->addStyleSheet(Uri::base(true).'/media/com_phocagallery/js/magnific/magnific-popup.css');

				$wa->registerAndUseScript('com_phocagallery.magnific.js', 'media/com_phocagallery/js/magnific/jquery.magnific-popup.min.js', ['version' => 'auto']);
				$wa->registerAndUseScript('com_phocagallery.magnific.initialize.js', 'media/com_phocagallery/js/magnific/magnific-initialize.js', ['version' => 'auto']);
				$wa->registerAndUseStyle('com_phocagallery.magnific', 'media/com_phocagallery/js/magnific/magnific-popup.css', array('version' => 'auto'));

			break;

			case 14:

				// PHOTOSWIPE
				HTMLHelper::_('jquery.framework', true);

			/*$this->b1->methodname 	= 'pg-photoswipe-button';
			$this->b1->options		= 'itemprop="contentUrl"';
			$this->b2->methodname 	= 'pg-photoswipe-button-copy';
			$this->b2->options		= $this->b1->options;

			$this->b3->methodname	= 'pg-ps-modal-button';
			$this->b3->options		= '';
			$this->b3->optionsrating= '';*/



			// If standard window, change:
			// FROM: return ' rel="'.$buttonOptions.'"'; TO: return ' onclick="'.$buttonOptions.'"';
			// in administrator\components\com_phocagallery\libraries\phocagallery\render\renderfront.php
			// method: renderAAttributeTitle detailwindow = 14


			if ( isset($libraries['pg-group-photoswipe']->value) && $libraries['pg-group-photoswipe']->value == 0 ) {

				//$document->addStyleSheet(Uri::base(true).'/media/com_phocagallery/js/photoswipe/css/photoswipe.css');
				//$document->addStyleSheet(Uri::base(true).'/media/com_phocagallery/js/photoswipe/css/default-skin/default-skin.css');
				//$document->addStyleSheet(Uri::base(true).'/media/com_phocagallery/js/photoswipe/css/photoswipe-style.css');

				$wa->registerAndUseStyle('com_phocagallery.photoswipe', 'media/com_phocagallery/js/photoswipe/css/photoswipe.css', array('version' => 'auto'));
				$wa->registerAndUseStyle('com_phocagallery.photoswipe.skin', 'media/com_phocagallery/js/photoswipe/css/default-skin/default-skin.css', array('version' => 'auto'));
				$wa->registerAndUseStyle('com_phocagallery.photoswipe.style', 'media/com_phocagallery/js/photoswipe/css/photoswipe-style.css', array('version' => 'auto'));
			}

			// LoadPhotoSwipeBottom must be loaded at the end of document
			break;


			default:
			break;
		}
	}


	public function getB1() {
		return $this->b1;
	}
	public function getB2() {
		return $this->b2;
	}
	public function getB3() {

		return $this->b3;
	}

	public static function loadPhotoswipeBottom($forceSlideshow = 0, $forceSlideEffect = 0) {

		$paramsC 				= ComponentHelper::getParams('com_phocagallery') ;
		$photoswipe_slideshow	= $paramsC->get( 'photoswipe_slideshow', 1 );
		$photoswipe_slide_effect= $paramsC->get( 'photoswipe_slide_effect', 0 );


		if ($forceSlideshow == 1) {
            $photoswipe_slideshow = 1;
        }
		if ($forceSlideEffect == 1) {
		    $photoswipe_slide_effect = 1;
        }


		$o = '<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

    <!-- Background of PhotoSwipe.
         It\'s a separate element, as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">

        <!-- Container that holds slides. PhotoSwipe keeps only 3 slides in DOM to save memory. -->
        <!-- don\'t modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">

            <div class="pswp__top-bar">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class="pswp__counter"></div>

                <button class="pswp__button pswp__button--close" title="'.Text::_('COM_PHOCAGALLERY_CLOSE').'"></button>

                <button class="pswp__button pswp__button--share" title="'.Text::_('COM_PHOCAGALLERY_SHARE').'"></button>

                <button class="pswp__button pswp__button--fs" title="'.Text::_('COM_PHOCAGALERY_TOGGLE_FULLSCREEN').'"></button>

                <button class="pswp__button pswp__button--zoom" title="'.Text::_('COM_PHOCAGALLERY_ZOOM_IN_OUT').'"></button>';

				if ($photoswipe_slideshow == 1) {
					$o .= '<!-- custom slideshow button: -->
					<button class="pswp__button pswp__button--playpause" title="'.Text::_('COM_PHOCAGALLERY_PLAY_SLIDESHOW').'"></button>
					<span id="phTxtPlaySlideshow" style="display:none">'.Text::_('COM_PHOCAGALLERY_PLAY_SLIDESHOW').'</span>
					<span id="phTxtPauseSlideshow" style="display:none">'.Text::_('COM_PHOCAGALLERY_PAUSE_SLIDESHOW').'</span>';
				}

                $o .= '<!-- Preloader -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div> 
            </div>

            <button class="pswp__button pswp__button--arrow--left" title="'.Text::_('COM_PHOCAGALLERY_PREVIOUS').'">
            </button>

            <button class="pswp__button pswp__button--arrow--right" title="'.Text::_('COM_PHOCAGALLERY_NEXT').'">
            </button>

            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>

          </div>

        </div>

</div>';
/*
$o .=   '<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe.min.js"></script>'. "\n"
		.'<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe-ui-default.min.js"></script>'. "\n";

if ($photoswipe_slide_effect == 1) {
	$o .= '<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe-initialize-ratio.js"></script>'. "\n";
} else {
	$o .= '<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe-initialize.js"></script>'. "\n";
}*/
		$wa = Factory::getDocument()->getWebAssetManager();
                $wa->registerAndUseScript('plg_content_phocagallery.photoswipe', 'media/com_phocagallery/js/photoswipe/js/photoswipe.min.js', array('version' => 'auto'), ['defer' => true]);
                $wa->registerAndUseScript('plg_content_phocagallery.photoswipe.default', 'media/com_phocagallery/js/photoswipe/js/photoswipe-ui-default.min.js', array('version' => 'auto'), ['defer' => true]);


//$o .=   '<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe.min.js"></script>'. "\n"
//		.'<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe-ui-default.min.js"></script>'. "\n";

if ($photoswipe_slide_effect == 1) {
	//$o .= '<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe-initialize-ratio.js"></script>'. "\n";
    $wa->registerAndUseScript('plg_content_phocagallery.photoswipe.initialize.ratio', 'media/com_phocagallery/js/photoswipe/js/photoswipe-initialize-ratio.js', array('version' => 'auto'), ['defer' => true]);
} else {
	//$o .= '<script src="'.Uri::root(true).'/media/com_phocagallery/js/photoswipe/js/photoswipe-initialize.js"></script>'. "\n";
    $wa->registerAndUseScript('plg_content_phocagallery.photoswipe.initialize.ratio', 'media/com_phocagallery/js/photoswipe/js/photoswipe-initialize.js', array('version' => 'auto'), ['defer' => true]);
}

		return $o;
	}

}
?>
