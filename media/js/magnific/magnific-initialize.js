/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @extension Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */


jQuery(document).ready(function() {

    var phLang		= Joomla.getOptions('phLangPG');

	jQuery('.pg-msnr-container').magnificPopup({

		tLoading: phLang['COM_PHOCAGALLERY_LOADING'],
		tClose: phLang['COM_PHOCAGALLERY_CLOSE'],
		delegate: 'a.pg-magnific-button',
		type: 'image',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			tPrev: phLang['COM_PHOCAGALLERY_PREVIOUS'],
			tNext: phLang['COM_PHOCAGALLERY_NEXT'],
			tCounter: phLang['COM_PHOCAGALLERY_MAGNIFIC_CURR_OF_TOTAL']
		},
		image: {
			titleSrc: function(item) {
				return item.el.attr('title');
			},
			tError: phLang['COM_PHOCAGALLERY_IMAGE_NOT_LOADED']
		}
	});

/*	
    /* Will be managed through onclick click() method - copy of first method */
    jQuery('a.pg-magnific2-button').magnificPopup({
		type: 'image',
		mainClass: 'mfp-img-mobile',
/*		preloader: false,
		fixedContentPos: false,*//*
		image: {
			tError: phLang['COM_PHOCAGALLERY_IMAGE_NOT_LOADED']
		}
	});*/

	jQuery('a.pg-magnific3-button').magnificPopup({
		type: 'iframe',
		mainClass: 'mfp-img-mobile',
		preloader: false,
		fixedContentPos: false,
/*		image: {
			tError: phLang['COM_PHOCAGALLERY_IMAGE_NOT_LOADED']
		}*/
	});

});