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
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
phocagalleryimport('phocagallery.render.renderprocess');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.image.image');

register_shutdown_function(function(){
	$error = error_get_last();

	if(null !== $error) {
		if (isset($error['type']) && $error['type'] == 1) {
			$app		= Factory::getApplication();
			$app->redirect('index.php?option=com_phocagallery&view=phocagalleryfe&error=1');
			return;
		}
	}
});

class PhocaGalleryImageMagic
{
	/**
	* need GD library (first PHP line WIN: dl("php_gd.dll"); UNIX: dl("gd.so");
	* www.boutell.com/gd/
	* interval.cz/clanky/php-skript-pro-generovani-galerie-obrazku-2/
	* cz.php.net/imagecopyresampled
	* www.linuxsoft.cz/sw_detail.php?id_item=871
	* www.webtip.cz/art/wt_tech_php/liquid_ir.html
	* php.vrana.cz/zmensovani-obrazku.php
	* diskuse.jakpsatweb.cz/
	*
	* @param string $fileIn Vstupni soubor (mel by existovat)
	* @param string $fileOut Vystupni soubor, null ho jenom zobrazi (taky kdyz nema pravo se zapsat :)
	* @param int $width Vysledna sirka (maximalni)
	* @param int $height Vysledna vyska (maximalni)
	* @param bool $crop Orez (true, obrazek bude presne tak velky), jinak jenom Resample (udane maximalni rozmery)
	* @param int $typeOut IMAGETYPE_type vystupniho obrazku
	* @return bool Chyba kdyz vrati false
	*/
	public static function imageMagic($fileIn, $fileOut = null, $width = null, $height = null, $crop = null, $typeOut = null, $watermarkParams = array(), $frontUpload = 0, &$errorMsg = '') {

		$params 		= ComponentHelper::getParams('com_phocagallery') ;
		$jfile_thumbs	=	$params->get( 'jfile_thumbs', 1 );
		$jpeg_quality	= $params->get( 'jpeg_quality', 85 );
		$exif_rotate	= $params->get( 'exif_rotate', 0 );
		$jpeg_quality	= PhocaGalleryImage::getJpegQuality($jpeg_quality);
		$webp_quality	= $params->get( 'webp_quality', 80 );
		$webp_quality	= PhocaGalleryImage::getJpegQuality($webp_quality);
		$avif_quality	= $params->get( 'avif_quality', 80 );
		$avif_quality	= PhocaGalleryImage::getJpegQuality($avif_quality);

		$fileWatermark = '';

		// While front upload we don't display the process page
		if ($frontUpload == 0) {

			$stopText = PhocaGalleryRenderProcess::displayStopThumbnailsCreating('processpage');
			echo $stopText;
		}
		// Memory - - - - - - - -
		/*$memory = 8;
		$memoryLimitChanged = 0;
		$memory = (int)ini_get( 'memory_limit' );
		if ($memory == 0) {
			$memory = 8;
		}*/
		// - - - - - - - - - - -

		if ($fileIn !== '' && File::exists($fileIn)) {

			// array of width, height, IMAGETYPE, "height=x width=x" (string)
	        list($w, $h, $type) = GetImageSize($fileIn);


			// Read EXIF data from image file to get the Orientation flag
			$exif = null;
			if ($exif_rotate == 1) {
				if (function_exists('exif_read_data') && $type == IMAGETYPE_JPEG ) {
					$exif = @exif_read_data($fileIn);
				}
				// GetImageSize returns an array of width, height, IMAGETYPE, "height=x width=x" (string)
				// The EXIF Orientation flag is examined to determine if width and height need to be swapped, i.e. if the image will be rotated in a subsequent step
				if(isset($exif['Orientation']) && !empty($exif['Orientation'])) {

					switch($exif['Orientation']) {
					   case 8: // will need to be rotated 90 degrees left, so swap order of width and height
						  list($h, $w, $type) = GetImageSize($fileIn);
						  break;
					   case 3: // will need to be rotated 180 degrees so don't swap order of width and height
						  list($w, $h, $type) = GetImageSize($fileIn);
						  break;
					   case 6:   // will need to be rotated 90 degrees right, so swap order of width and height
						  list($h, $w, $type) = GetImageSize($fileIn);
						  break;
					}
				}
			}

			if ($w > 0 && $h > 0) {// we got the info from GetImageSize

		        // size of the image
		        if ($width == null || $width == 0) { // no width added
		            $width = $w;
		        }
				else if ($height == null || $height == 0) { // no height, adding the same as width
		            $height = $width;
		        }
				if ($height == null || $height == 0) { // no height, no width
		            $height = $h;
		        }

		        // miniaturizing
		        if (!$crop) { // new size - nw, nh (new width/height)

					$scale = (($width / $w) < ($height / $h)) ? ($width / $w) : ($height / $h); // smaller rate
					//$scale = $height / $h;

		            $src = array(0,0, $w, $h);
		            $dst = array(0,0, floor($w*$scale), floor($h*$scale));
		        }
		        else { // will be cropped
		            $scale = (($width / $w) > ($height / $h)) ? ($width / $w) : ($height / $h); // greater rate
		            $newW = $width/$scale;    // check the size of in file
		            $newH = $height/$scale;

		            // which side is larger (rounding error)
		            if (($w - $newW) > ($h - $newH)) {
		                $src = array(floor(($w - $newW)/2), 0, floor($newW), $h);
		            }
		            else {
		                $src = array(0, floor(($h - $newH)/2), $w, floor($newH));
		            }

		            $dst = array(0,0, floor($width), floor($height));
		        }

				// Watermark - - - - - - - - - - -
				if (!empty($watermarkParams) && ($watermarkParams['create'] == 1 || $watermarkParams['create'] == 2)) {

					$thumbnailSmall		= false;
					$thumbnailMedium	= false;
					$thumbnailLarge		= false;

					$thumbnailMedium	= preg_match("/phoca_thumb_m_/i", $fileOut);
					$thumbnailLarge 	= preg_match("/phoca_thumb_l_/i", $fileOut);

					$path				= PhocaGalleryPath::getPath();
					$fileName 			= PhocaGalleryFile::getTitleFromFile($fileIn, 1);

					// Which Watermark will be used
					// If watermark is in current directory use it else use Default
					$fileWatermarkMedium		= false;
					$fileWatermarkLarge			= false;
					$fileWatermarkMediumPng  	= str_replace($fileName, 'watermark-medium.png', $fileIn);
					$fileWatermarkLargePng  	= str_replace($fileName, 'watermark-large.png', $fileIn);
					$fileWatermarkMediumWebp  	= str_replace($fileName, 'watermark-medium.webp', $fileIn);
					$fileWatermarkLargeWebp  	= str_replace($fileName, 'watermark-large.webp', $fileIn);
					$fileWatermarkMediumAvif  	= str_replace($fileName, 'watermark-medium.avif', $fileIn);
					$fileWatermarkLargeAvif  	= str_replace($fileName, 'watermark-large.avif', $fileIn);

					$fileWatermarkMediumRoot		= false;
					$fileWatermarkLargeRoot			= false;
					$fileWatermarkMediumPngRoot  	= $path->image_abs . 'watermark-medium.png';
					$fileWatermarkLargePngRoot  	= $path->image_abs . 'watermark-large.png';
					$fileWatermarkMediumWebpRoot  	= $path->image_abs . 'watermark-medium.webp';
					$fileWatermarkLargeWebpRoot  	= $path->image_abs . 'watermark-large.webp';
					$fileWatermarkMediumAvifRoot  	= $path->image_abs . 'watermark-medium.avif';
					$fileWatermarkLargeAvifRoot  	= $path->image_abs . 'watermark-large.avif';

					if ($type == IMAGETYPE_WEBP) {
						if (File::exists($fileWatermarkMediumWebp)) {
							$fileWatermarkMedium = $fileWatermarkMediumWebp;
						} else if (File::exists($fileWatermarkMediumPng)) {
							$fileWatermarkMedium = $fileWatermarkMediumPng;
						} else if (File::exists($fileWatermarkMediumAvif)) {
							$fileWatermarkMedium = $fileWatermarkMediumAvif;
						}

						if (File::exists($fileWatermarkMediumWebpRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumWebpRoot;
						} else if (File::exists($fileWatermarkMediumPngRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumPngRoot;
						} else if (File::exists($fileWatermarkMediumAvifRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumAvifRoot;
						}

						if (File::exists($fileWatermarkLargeWebp)) {
							$fileWatermarkLarge = $fileWatermarkLargeWebp;
						} else if (File::exists($fileWatermarkLargePng)) {
							$fileWatermarkLarge = $fileWatermarkLargePng;
						} else if (File::exists($fileWatermarkLargeAvif)) {
							$fileWatermarkLarge = $fileWatermarkLargeAvif;
						}

						if (File::exists($fileWatermarkLargeWebpRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargeWebpRoot;
						} else if (File::exists($fileWatermarkLargePngRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargePngRoot;
						} else if (File::exists($fileWatermarkLargeAvifRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargeAvifRoot;
						}

					} else if ($type ==  IMAGETYPE_AVIF){
						if (File::exists($fileWatermarkMediumAvif)) {
							$fileWatermarkMedium = $fileWatermarkMediumAvif;
						} else if (File::exists($fileWatermarkMediumPng)) {
							$fileWatermarkMedium = $fileWatermarkMediumPng;
						} else if (File::exists($fileWatermarkMediumWebp)) {
							$fileWatermarkMedium = $fileWatermarkMediumWebp;
						}

						if (File::exists($fileWatermarkMediumAvifRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumAvifRoot;
						} else if (File::exists($fileWatermarkMediumPngRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumPngRoot;
						} else if (File::exists($fileWatermarkMediumWebpRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumWebpRoot;
						}

						if (File::exists($fileWatermarkLargeAvif)) {
							$fileWatermarkLarge = $fileWatermarkLargeAvif;
						} else if (File::exists($fileWatermarkLargePng)) {
							$fileWatermarkLarge = $fileWatermarkLargePng;
						} else if (File::exists($fileWatermarkLargeWebp)) {
							$fileWatermarkLarge = $fileWatermarkLargeWebp;
						}

						if (File::exists($fileWatermarkLargeAvifRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargeAvifRoot;
						} else if (File::exists($fileWatermarkLargePngRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargePngRoot;
						} else if (File::exists($fileWatermarkLargeWebpRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargeWebpRoot;
						}

					} else {
						if (File::exists($fileWatermarkMediumPng)) {
							$fileWatermarkMedium = $fileWatermarkMediumPng;
						} else if (File::exists($fileWatermarkMediumWebp)) {
							$fileWatermarkMedium = $fileWatermarkMediumWebp;
						} else if (File::exists($fileWatermarkMediumAvif)) {
							$fileWatermarkMedium = $fileWatermarkMediumAvif;
						}

						if (File::exists($fileWatermarkMediumPngRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumPngRoot;
						} else if (File::exists($fileWatermarkMediumWebpRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumWebpRoot;
						} else if (File::exists($fileWatermarkMediumAvifRoot)) {
							$fileWatermarkMediumRoot = $fileWatermarkMediumAvifRoot;
						}

						if (File::exists($fileWatermarkLargePng)) {
							$fileWatermarkLarge = $fileWatermarkLargePng;
						} else if (File::exists($fileWatermarkLargeWebp)) {
							$fileWatermarkLarge = $fileWatermarkLargeWebp;
						} else if (File::exists($fileWatermarkLargeAvif)) {
							$fileWatermarkLarge = $fileWatermarkLargeAvif;
						}

						if (File::exists($fileWatermarkLargePngRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargePngRoot;
						} else if (File::exists($fileWatermarkLargeWebpRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargeWebpRoot;
						} else if (File::exists($fileWatermarkLargeAvifRoot)) {
							$fileWatermarkLargeRoot = $fileWatermarkLargeAvifRoot;
						}
					}


					clearstatcache();

					// Which Watermark will be used
					if ($thumbnailMedium) {
						if ($fileWatermarkMedium) {
								$fileWatermark  = $fileWatermarkMedium;
						} else {
							if ($watermarkParams['create'] == 2 && $fileWatermarkMediumRoot) {
								$fileWatermark  = $fileWatermarkMediumRoot;
							} else {
								$fileWatermark	= '';
							}
						}
					} else if ($thumbnailLarge) {
						if ($fileWatermarkLarge) {
								$fileWatermark  = $fileWatermarkLarge;
						} else {
							if ($watermarkParams['create'] == 2 && $fileWatermarkLargeRoot) {
								$fileWatermark  = $fileWatermarkLargeRoot;
							} else {
								$fileWatermark	= '';
							}
						}
					} else {
							$fileWatermark  = '';
					}


					if (!File::exists($fileWatermark)) {
						$fileWatermark = '';
					}

					if ($fileWatermark != '') {
						list($wW, $hW, $typeW)	= GetImageSize($fileWatermark);


						switch ($watermarkParams['x']) {
							case 'left':
								$locationX	= 0;
							break;

							case 'right':
								$locationX	= $dst[2] - $wW;
							break;

							case 'center':
							Default:
								$locationX	= ($dst[2] / 2) - ($wW / 2);
							break;
						}

						switch ($watermarkParams['y']) {
							case 'top':
								$locationY	= 0;
							break;

							case 'bottom':
								$locationY	= $dst[3] - $hW;
							break;

							case 'middle':
							Default:
								$locationY	= ($dst[3] / 2) - ($hW / 2);
							break;
						}
					}
				} else {
					$fileWatermark = '';
				}
			}



			/*if ($memory < 50) {
				ini_set('memory_limit', '50M');
				$memoryLimitChanged = 1;
			}*/
			// Resampling
			// in file

			// Watemark
			if ($fileWatermark != '') {

				$ext = File::getExt($fileWatermark);

				if ($ext == 'webp') {
					if (!function_exists('ImageCreateFromWEBP')) {
						$errorMsg = 'ErrorNoWEBPFunction';
						return false;
					}
					$waterImage1=ImageCreateFromWEBP($fileWatermark);
					//imagealphablending($waterImage1, false);
					//imagesavealpha($waterImage1, true);
				} else if ($ext == 'avif') {
					if (!function_exists('ImageCreateFromAVIF')) {
						$errorMsg = 'ErrorNoAVIFFunction';
						return false;
					}
					$waterImage1=ImageCreateFromAVIF($fileWatermark);
					//imagealphablending($waterImage1, false);
					//imagesavealpha($waterImage1, true);
				} else {
					if (!function_exists('ImageCreateFromPNG')) {
						$errorMsg = 'ErrorNoPNGFunction';
						return false;
					}
					$waterImage1=ImageCreateFromPNG($fileWatermark);
					//imagealphablending($waterImage1, false);
					//imagesavealpha($waterImage1, true);
				}

			}

			// End Watermark - - - - - - - - - - - - - - - - - -

	        switch($type) {
	            case IMAGETYPE_JPEG:
					if (!function_exists('ImageCreateFromJPEG')) {
						$errorMsg = 'ErrorNoJPGFunction';
						return false;
					}
					//$image1 = ImageCreateFromJPEG($fileIn);
					try {
						$image1 = ImageCreateFromJPEG($fileIn);
					} catch(\Exception $exception) {
						$errorMsg = 'ErrorJPGFunction';
						return false;
					}

				break;
	            case IMAGETYPE_PNG :
					if (!function_exists('ImageCreateFromPNG')) {
						$errorMsg = 'ErrorNoPNGFunction';
						return false;
					}
					//$image1 = ImageCreateFromPNG($fileIn);
					try {
						$image1 = ImageCreateFromPNG($fileIn);
					} catch(\Exception $exception) {
						$errorMsg = 'ErrorPNGFunction';
						return false;
					}
				break;
	            case IMAGETYPE_GIF :
					if (!function_exists('ImageCreateFromGIF')) {
						$errorMsg = 'ErrorNoGIFFunction';
						return false;
					}
					//$image1 = ImageCreateFromGIF($fileIn);
					try {
						$image1 = ImageCreateFromGIF($fileIn);
					} catch(\Exception $exception) {
						$errorMsg = 'ErrorGIFFunction';
						return false;
					}
				break;
				 case IMAGETYPE_WEBP:
					if (!function_exists('ImageCreateFromWEBP')) {
						$errorMsg = 'ErrorNoWEBPFunction';
						return false;
					}
					//$image1 = ImageCreateFromGIF($fileIn);
					try {
						$image1 = ImageCreateFromWEBP($fileIn);
					} catch(\Exception $exception) {
						$errorMsg = 'ErrorWEBPFunction';
						return false;
					}
				break;
					case IMAGETYPE_AVIF:
					if (!function_exists('imagecreatefromavif')) {
						$errorMsg = 'ErrorNoAVIFFunction';
						return false;
					}
					//$image1 = ImageCreateFromGIF($fileIn);
					try {
						$image1 = imagecreatefromavif($fileIn);
					} catch(\Exception $exception) {
						$errorMsg = 'ErrorAVIFFunction';
						return false;
					}
				break;
	            case IMAGETYPE_WBMP:
					if (!function_exists('ImageCreateFromWBMP')) {
						$errorMsg = 'ErrorNoWBMPFunction';
						return false;
					}
					//$image1 = ImageCreateFromWBMP($fileIn);
					try {
						$image1 = ImageCreateFromWBMP($fileIn);
					} catch(\Exception $exception) {
						$errorMsg = 'ErrorWBMPFunction';
						return false;
					}
					break;
	            Default:
					$errorMsg = 'ErrorNotSupportedImage';
					return false;
					break;
	        }

			if ($image1) {

				$image2 = @ImageCreateTruecolor($dst[2], $dst[3]);
				if (!$image2) {
					$errorMsg = 'ErrorNoImageCreateTruecolor';
					return false;
				}

				switch($type) {
					case IMAGETYPE_PNG:
					case IMAGETYPE_WEBP:
					case IMAGETYPE_AVIF:
						//imagealphablending($image1, false);
						@imagealphablending($image2, false);
						//imagesavealpha($image1, true);
						@imagesavealpha($image2, true);
					break;
				}

				if ($exif_rotate == 1) {
					// Examine the EXIF Orientation flag (read earlier) to determine if the image needs to be rotated prior to the ImageCopyResampled call
					// Use the imagerotate() function to perform the rotation, if required
					if(isset($exif['Orientation']) && !empty($exif['Orientation'])) {
						switch($exif['Orientation']) {
						   case 8:
								 $image1 = imagerotate($image1,90,0);
								// @imagealphablending($image1, false);
								// @imagesavealpha($image1, true);
								 break;
						   case 3:
								 $image1 = imagerotate($image1,180,0);
								// @imagealphablending($image1, false);
								// @imagesavealpha($image1, true);
								 break;
						   case 6:
								 $image1 = imagerotate($image1,-90,0);
								// @imagealphablending($image1, false);
								// @imagesavealpha($image1, true);
								 break;
						}
					}
				}

				ImageCopyResampled($image2, $image1, $dst[0],$dst[1], $src[0],$src[1], $dst[2],$dst[3], $src[2],$src[3]);

				// Watermark - - - - - -
				if ($fileWatermark != '') {


					//imagecolortransparent($waterImage1, imagecolorallocate($waterImage1, 0, 0, 0));
					//imagepalettetotruecolor($waterImage1);
                	//imagealphablending($waterImage1, true);
                	//imagesavealpha($waterImage1, true);
					//imagecolortransparent($image2, imagecolorallocate($image2, 0, 0, 0));
					//imagepalettetotruecolor($image2);
                	imagealphablending($image2, true);// Needed for webp and avif transparency
                	//imagesavealpha($image2, true);
					ImageCopy($image2, $waterImage1, (int)$locationX, (int)$locationY, 0, 0, (int)$wW, (int)$hW);
				}
				// End Watermark - - - -


	            // Display the Image - not used
	            if ($fileOut == null) {
	                header("Content-type: ". image_type_to_mime_type($typeOut));
	            }

				// Create the file
		        if ($typeOut == null) {    // no bitmap
		            $typeOut = ($type == IMAGETYPE_WBMP) ? IMAGETYPE_PNG : $type;
		        }

				switch($typeOut) {
		            case IMAGETYPE_JPEG:
						if (!function_exists('ImageJPEG')) {
							$errorMsg = 'ErrorNoJPGFunction';
							return false;
						}

						if ($jfile_thumbs == 1) {
							ob_start();
							if (!@ImageJPEG($image2, NULL, $jpeg_quality)) {
								ob_end_clean();
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
							$imgJPEGToWrite = ob_get_contents();
							ob_end_clean();

							if(!File::write( $fileOut, $imgJPEGToWrite)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						} else {
							if (!@ImageJPEG($image2, $fileOut, $jpeg_quality)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						}
					break;

					case IMAGETYPE_PNG :
						if (!function_exists('ImagePNG')) {
							$errorMsg = 'ErrorNoPNGFunction';
							return false;
						}

						if ($jfile_thumbs == 1) {
							ob_start();
							if (!@ImagePNG($image2, NULL)) {
								ob_end_clean();
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
							$imgPNGToWrite = ob_get_contents();
							ob_end_clean();

							if(!File::write( $fileOut, $imgPNGToWrite)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						} else {
							if (!@ImagePNG($image2, $fileOut)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						}
					break;

					case IMAGETYPE_GIF :
						if (!function_exists('ImageGIF')) {
							$errorMsg = 'ErrorNoGIFFunction';
							return false;
						}

						if ($jfile_thumbs == 1) {
							ob_start();
							if (!@ImageGIF($image2, NULL)) {
								ob_end_clean();
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
							$imgGIFToWrite = ob_get_contents();
							ob_end_clean();

							if(!File::write( $fileOut, $imgGIFToWrite)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						} else {
							if (!@ImageGIF($image2, $fileOut)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						}
					break;

					case IMAGETYPE_WEBP :
						if (!function_exists('ImageWEBP')) {
							$errorMsg = 'ErrorNoWEBPFunction';
							return false;
						}

						if ($jfile_thumbs == 1) {
							ob_start();
							if (!@imagewebp($image2, NULL, $webp_quality)) {
								ob_end_clean();
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
							$imgWEBPToWrite = ob_get_contents();
							ob_end_clean();

							if(!File::write( $fileOut, $imgWEBPToWrite)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						} else {
							if (!@imagewebp($image2, $fileOut, $webp_quality)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						}
					break;

					case IMAGETYPE_AVIF :
						if (!function_exists('ImageAVIF')) {
							$errorMsg = 'ErrorNoAVIFFunction';
							return false;
						}

						if ($jfile_thumbs == 1) {
							ob_start();
							if (!@imageavif($image2, NULL, $avif_quality)) {
								ob_end_clean();
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
							$imgAVIFToWrite = ob_get_contents();
							ob_end_clean();

							if(!File::write( $fileOut, $imgAVIFToWrite)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						} else {
							if (!@imageavif($image2, $fileOut, $avif_quality)) {
								$errorMsg = 'ErrorWriteFile';
								return false;
							}
						}
					break;

					Default:
						$errorMsg = 'ErrorNotSupportedImage';
						return false;
						break;
				}

				// free memory
				ImageDestroy($image1);
	            ImageDestroy($image2);
				if (isset($waterImage1)) {
					ImageDestroy($waterImage1);
				}

				/*if ($memoryLimitChanged == 1) {
					$memoryString = $memory . 'M';
					ini_set('memory_limit', $memoryString);
				}*/
	             $errorMsg = ''; // Success
				 return true;
	        } else {
				$errorMsg = 'Error1';
				return false;
			}
			/*if ($memoryLimitChanged == 1) {
				$memoryString = $memory . 'M';
				ini_set('memory_limit', $memoryString);
			}*/
	    }
		$errorMsg = 'Error2';
		return false;
	}
}
?>
