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
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Filesystem\File;
jimport( 'joomla.filesystem.file' );

class PhocaGalleryYoutube
{
	/**
	 * A YouTube video ID is always exactly 11 characters of [A-Za-z0-9_-].
	 */
	public static function isValidCode($code) {
		return is_string($code) && preg_match('/^[A-Za-z0-9_-]{11}$/', $code) === 1;
	}

	public static function displayVideo($videoCode, $view = 0, $ywidth = 0, $yheight = 0) {

		$o = '';

		// Always re-derive and re-validate the ID, never trust the stored value
		// (rows saved before this fix may contain attacker-controlled data)
		$code = self::getCode($videoCode);

		if ($code !== '') {
			$o .= '<iframe src="https://www.youtube.com/embed/' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '" frameborder="0" allowfullscreen></iframe>';
		}

		return $o;
	}

	/**
	 * Extract a validated video ID from a YouTube URL.
	 * Returns '' when the URL is not a recognised YouTube URL or the ID is not valid.
	 */
	public static function getCode($url) {

		$url = trim((string)$url);

		if ($url === '' || !PhocaGalleryUtils::isURLAddress($url)) {
			return '';
		}

		$parts = parse_url($url);
		if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
			return '';
		}

		if (!in_array(strtolower($parts['scheme']), array('http', 'https'), true)) {
			return '';
		}

		$host = strtolower($parts['host']);
		$host = preg_replace('/^(www\.|m\.|music\.)/', '', $host);
		$path = isset($parts['path']) ? $parts['path'] : '';
		$code = '';

		if ($host === 'youtu.be') {
			// https://youtu.be/VIDEOID
			$code = trim($path, '/');

		} else if ($host === 'youtube.com' || $host === 'youtube-nocookie.com') {

			if ($path === '/watch') {
				// https://www.youtube.com/watch?v=VIDEOID&...
				$query = array();
				parse_str(isset($parts['query']) ? $parts['query'] : '', $query);
				if (isset($query['v']) && is_string($query['v'])) {
					$code = $query['v'];
				}
			} else if (preg_match('#^/(?:embed|v|shorts|live)/([^/?\#]+)#', $path, $m)) {
				// https://www.youtube.com/embed/VIDEOID
				$code = $m[1];
			}
		}

		return self::isValidCode($code) ? $code : '';
	}

	public static function importYtb($ytbLink, $folder, &$errorMsg = '') {

		// getCode() returns only a validated 11-character video ID (or '')
		$ytbCode 	= PhocaGalleryYoutube::getCode(strip_tags($ytbLink));

		$ytb				= array();
		$ytb['title']		= '';
		$ytb['desc']		= '';
		$ytb['filename']	= '';
		// Store a canonical link built from the validated ID, never the raw user input
		$ytb['link']		= $ytbCode !== '' ? 'https://www.youtube.com/watch?v=' . $ytbCode : '';


		if(!function_exists("curl_init")){
			$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_NOT_LOADED_CURL');
			return false;
		} else if ($ytbCode == '') {
			$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_URL_NOT_CORRECT');
			return false;
		} else {

			$paramsC 	= ComponentHelper::getParams('com_phocagallery');
			$key 		= $paramsC->get( 'youtube_api_key', '' );
			$ssl 		= $paramsC->get( 'youtube_api_ssl', 1 );

			// Data
			//$cUrl		= curl_init("http://gdata.youtube.com/feeds/api/videos/".strip_tags($ytbCode));
			$cUrl 		= curl_init('https://www.googleapis.com/youtube/v3/videos?id='.rawurlencode($ytbCode).'&part=snippet&key='.rawurlencode((string)$key));


			if ($ssl == 0) {
				curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, false);
			} else {
				curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, true);
			}

			curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
            $json		= curl_exec($cUrl);

            curl_close($cUrl);



			$o 	= json_decode($json);


			if (!empty($o) && isset($o->error->message)) {
				$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_ERROR_IMPORTING_DATA') . '('.strip_tags($o->error->message).')';
				return false;
			} else if (!empty($o) && isset($o->items[0]->snippet)) {
				$oS = $o->items[0]->snippet;

				if (isset($oS->title)) {
					$ytb['title'] = (string)$oS->title;
				}

				if ($ytb['title'] == '' && isset($oS->localized->title)) {
					$ytb['title'] = (string)$oS->localized->title;
				}

				if (isset($oS->description)) {
					$ytb['desc'] = (string)$oS->description;
				}

				if ($ytb['desc'] == '' && isset($oS->localized->description)) {
					$ytb['desc'] = (string)$oS->localized->description;
				}

				// Text coming from a third party API is stored and later printed by the detail view
				// without escaping - strip any markup (defense in depth)
				$ytb['title'] = strip_tags($ytb['title']);
				$ytb['desc']  = strip_tags($ytb['desc']);

				$img = '';

				if (isset($oS->thumbnails->standard->url)) {
					$cUrl		= curl_init(strip_tags((string)$oS->thumbnails->standard->url));
				} else if (isset($oS->thumbnails->default->url)) {
					$cUrl		= curl_init(strip_tags((string)$oS->thumbnails->default->url));
				}
				if (isset($cUrl) && $cUrl != '') {

					if ($ssl == 0) {
						curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, false);
					} else {
						curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, true);
					}
					curl_setopt($cUrl,CURLOPT_RETURNTRANSFER,1);
					$img		= curl_exec($cUrl);
					curl_close($cUrl);
				}



				$ytb['filename']	= $folder.$ytbCode.'.jpg';


				if ($img != '') {
					if (PhocaGalleryFile::exists(JPATH_ROOT . '/images/phocagallery' . '/'. $ytb['filename'], $img)) {
						//$errorMsg = JText::_('COM_PHOCAGALLERY_YTB_ERROR_VIDEO_EXISTS');
						//return false;
						//Overwrite the images

					}

					if (!File::write(JPATH_ROOT . '/images/phocagallery' . '/'. $ytb['filename'], $img)) {
						$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_ERROR_WRITE_IMAGE');
						return false;
					}
				}

			} else {
				$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_ERROR_IMPORTING_DATA');
				return false;
			}


			// API 2
			/*$xml 	= str_replace('<media:', '<phcmedia', $xml);
			$xml 	= str_replace('</media:', '</phcmedia', $xml);

			$data 	= simplexml_load_file($file);

			//Title
			if (isset($data->title)) {
				$ytb['title'] = (string)$data->title;
			}

			if ($ytb['title'] == '' && isset($data->phcmediagroup->phcmediatitle)) {
				$ytb['title'] = (string)$data->phcmediagroup->phcmediatitle;
			}

			if (isset($data->phcmediagroup->phcmediadescription)) {
				$ytb['desc'] = (string)$data->phcmediagroup->phcmediadescription;
			}

			// Thumbnail
			if (isset($data->phcmediagroup->phcmediathumbnail[0]['url'])) {
				$cUrl		= curl_init(strip_tags((string)$data->phcmediagroup->phcmediathumbnail[0]['url']));
				curl_setopt($cUrl,CURLOPT_RETURNTRANSFER,1);
				$img		= curl_exec($cUrl);
				curl_close($cUrl);
			}

			if ($img != '') {
				$cUrl		= curl_init("http://img.youtube.com/vi/".strip_tags($ytbCode)."/0.jpg");
				curl_setopt($cUrl,CURLOPT_RETURNTRANSFER,1);
				$img		= curl_exec($cUrl);
				curl_close($cUrl);
			}

			$ytb['filename']	= $folder.$ytbCode.'.jpg';

			if (PhocaGalleryFile::exists(JPATH_ROOT . '/' .'images' . '/' . 'phocagallery' . '/'. $ytb['filename'], $img)) {
				$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_ERROR_VIDEO_EXISTS');
				return false;
			}

            if (!File::write(JPATH_ROOT . '/' .'images' . '/' . 'phocagallery' . '/'. $ytb['filename'], $img)) {
				$errorMsg = Text::_('COM_PHOCAGALLERY_YTB_ERROR_WRITE_IMAGE');
				return false;
			}*/
		}

		return $ytb;

	}
}
