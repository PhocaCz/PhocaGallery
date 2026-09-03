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

use Joomla\Filesystem\File;

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Client\ClientHelper;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
phocagalleryimport( 'phocagallery.image.image');
phocagalleryimport( 'phocagallery.file.fileuploadfront' );
class PhocaGalleryFileUpload
{
	public static function realMultipleUpload( $frontEnd = 0) {

		$paramsC 		= ComponentHelper::getParams('com_phocagallery');
		$chunkMethod 	= $paramsC->get( 'multiple_upload_chunk', 0 );
		$uploadMethod 	= $paramsC->get( 'multiple_upload_method', 4 );

		$app 	= Factory::getApplication();
		$app->allowCache(false);

		// Chunk Files
		header('Content-type: text/plain; charset=UTF-8');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Invalid Token
		Session::checkToken( 'request' ) or jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 100,
				'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
				'details' => Text::_('COM_PHOCAGALLERY_INVALID_TOKEN'))));

		// Set FTP credentials, if given
		$ftp = ClientHelper::setCredentialsFromRequest('ftp');

		$path			= PhocaGalleryPath::getPath();
		$file 			= Factory::getApplication()->getInput()->files->get( 'file', null );
		$chunk 			= Factory::getApplication()->getInput()->get( 'chunk', 0, '', 'int' );
		$chunks 		= Factory::getApplication()->getInput()->get( 'chunks', 0, '', 'int' );
		$folder			= Factory::getApplication()->getInput()->get( 'folder', '', '', 'path' );

		// Make the filename safe
		if (isset($file['name'])) {
			$file['name']	= File::makeSafe($file['name']);
		}
		if (isset($folder) && $folder != '') {
			$folder	= $folder . '/';
		}

		$chunkEnabled = 0;
		// Chunk only if is enabled and only if flash is enabled
		if (($chunkMethod == 1 && $uploadMethod == 1) || ($frontEnd == 0 && $chunkMethod == 0 && $uploadMethod == 1)) {
			$chunkEnabled = 1;
		}




		if (isset($file['name'])) {


			// - - - - - - - - - -
			// Chunk Method
			// - - - - - - - - - -
			// $chunkMethod = 1, for frontend and backend
			// $chunkMethod = 0, only for backend
			if ($chunkEnabled == 1) {

				// If chunk files are used, we need to upload parts to temp directory
				// and then we can run e.g. the condition to recognize if the file already exists
				// We must upload the parts to temp, in other case we get everytime the info
				// that the file exists (because the part has the same name as the file)
				// so after first part is uploaded, in fact the file already exists
				// Example: NOT USING CHUNK
				// If we upload abc.jpg file to server and there is the same file
				// we compare it and can recognize, there is one, don't upload it again.
				// Example: USING CHUNK
				// If we upload abc.jpg file to server and there is the same file
				// the part of current file will overwrite the same file
				// and then (after all parts will be uploaded) we can make the condition to compare the file
				// and we recognize there is one - ok don't upload it BUT the file will be damaged by
				// parts uploaded by the new file - so this is why we are using temp file in Chunk method
				$stream 				= Factory::getStream();// Chunk Files
				$tempFolder				= 'pgpluploadtmpfolder/';
				$filepathImgFinal 		= Path::clean($path->image_abs.$folder.strtolower($file['name']));
				$filepathImgTemp 		= Path::clean($path->image_abs.$folder.$tempFolder.strtolower($file['name']));
				$filepathFolderFinal 	= Path::clean($path->image_abs.$folder);
				$filepathFolderTemp 	= Path::clean($path->image_abs.$folder.$tempFolder);
				$maxFileAge 			= 60 * 60; // Temp file age in seconds
				$lastChunk				= $chunk + 1;
				$realSize				= 0;

				// Get the real size - if chunk is uploaded, it is only a part size, so we must compute all size
				// If there is last chunk we can computhe the whole size
				if ($lastChunk == $chunks) {
					if (PhocaGalleryFile::exists($filepathImgTemp) && PhocaGalleryFile::exists($file['tmp_name'])) {
						$realSize = filesize($filepathImgTemp) + filesize($file['tmp_name']);
					}
				}

				// 5 minutes execution time
				@set_time_limit(5 * 60);// usleep(5000);

				// If the file already exists on the server:
				// - don't copy the temp file to final
				// - remove all parts in temp file
				// Because some parts are uploaded before we can run the condition
				// to recognize if the file already exists.
				if (PhocaGalleryFile::exists($filepathImgFinal)) {
					if($lastChunk == $chunks){
					@Folder::delete($filepathFolderTemp);
				}

				jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 108,
							'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
							'details' => Text::_('COM_PHOCAGALLERY_FILE_ALREADY_EXISTS'))));
				}

				if (!PhocaGalleryFileUpload::canUpload( $file, $errUploadMsg, $frontEnd, $chunkEnabled, $realSize )) {

					// If there is some error, remove the temp folder with temp files
					if($lastChunk == $chunks){
						@Folder::delete($filepathFolderTemp);
					}
					jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 104,
								'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
								'details' => Text::_($errUploadMsg))));
				}

				// Ok create temp folder and add chunks
				if (!PhocaGalleryFileFolder::exists($filepathFolderTemp)) {
					@Folder::create($filepathFolderTemp);
				}

				// Remove old temp files
				if (PhocaGalleryFileFolder::exists($filepathFolderTemp)) {
					$dirFiles = Folder::files($filepathFolderTemp);
					if (!empty($dirFiles)) {
						foreach ($dirFiles as $fileS) {
							$filePathImgS = $filepathFolderTemp . $fileS;
							// Remove temp files if they are older than the max age
							if (preg_match('/\\.tmp$/', $fileS) && (filemtime($filepathImgTemp) < time() - $maxFileAge)) {
								@File::delete($filePathImgS);
							}
						}
					}
				} else {
					jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 100,
							'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
							'details' => Text::_('COM_PHOCAGALLERY_ERROR_FOLDER_UPLOAD_NOT_EXISTS'))));
				}

				// Look for the content type header
				if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
					$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

				if (isset($_SERVER["CONTENT_TYPE"]))
					$contentType = $_SERVER["CONTENT_TYPE"];

				if (strpos($contentType, "multipart") !== false) {
					if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {

						// Open temp file
						$out = $stream->open($filepathImgTemp, $chunk == 0 ? "wb" : "ab");
						//$out = fopen($filepathImgTemp, $chunk == 0 ? "wb" : "ab");
						if ($out) {
							// Read binary input stream and append it to temp file
							$in = fopen($file['tmp_name'], "rb");
							if ($in) {
								while ($buff = fread($in, 4096)) {
									$stream->write($buff);
									//fwrite($out, $buff);
								}
							} else {
								jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 101,
								'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
								'details' => Text::_('COM_PHOCAGALLERY_ERROR_OPEN_INPUT_STREAM'))));
							}
							$stream->close();
							//fclose($out);
							@File::delete($file['tmp_name']);
						} else {
							jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 102,
							'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
							'details' => Text::_('COM_PHOCAGALLERY_ERROR_OPEN_OUTPUT_STREAM'))));
						}
					} else {
						jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 103,
							'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
							'details' => Text::_('COM_PHOCAGALLERY_ERROR_MOVE_UPLOADED_FILE'))));
					}
				} else {
					// Open temp file
					$out = $stream->open($filepathImgTemp, $chunk == 0 ? "wb" : "ab");
					//$out = JFile::read($filepathImg);
					if ($out) {
						// Read binary input stream and append it to temp file
						$in = fopen("php://input", "rb");

						if ($in) {
							while ($buff = fread($in, 4096)) {
								$stream->write($buff);
							}
						} else {
							jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 101,
								'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
								'details' => Text::_('COM_PHOCAGALLERY_ERROR_OPEN_INPUT_STREAM'))));
						}
						$stream->close();
						//fclose($out);
					} else {
						jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 102,
						'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
						'details' => Text::_('COM_PHOCAGALLERY_ERROR_OPEN_OUTPUT_STREAM'))));
					}
				}


				// Rename the Temp File to Final File
				if($lastChunk == $chunks){

					if(($imginfo = getimagesize($filepathImgTemp)) === FALSE) {
						Folder::delete($filepathFolderTemp);
						jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 110,
						'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
						'details' => Text::_('COM_PHOCAGALLERY_WARNING_INVALIDIMG'))));
					}


					if(!File::move($filepathImgTemp, $filepathImgFinal)) {

						Folder::delete($filepathFolderTemp);

						jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 109,
						'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
						'details' => Text::_('COM_PHOCAGALLERY_ERROR_UNABLE_TO_MOVE_FILE') .'<br />'
						. Text::_('COM_PHOCAGALLERY_CHECK_PERMISSIONS_OWNERSHIP'))));
					}


					Folder::delete($filepathFolderTemp);
				}

				if ((int)$frontEnd > 0) {
					return $file['name'];
				}

				jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'OK', 'code' => 200,
				'message' => Text::_('COM_PHOCAGALLERY_SUCCESS').': ',
				'details' => Text::_('COM_PHOCAGALLERY_IMAGES_UPLOADED'))));


			} else {
				// No Chunk Method

				$filepathImgFinal 		= Path::clean($path->image_abs.$folder.strtolower($file['name']));
				$filepathFolderFinal 	= Path::clean($path->image_abs.$folder);



				if (!PhocaGalleryFileUpload::canUpload( $file, $errUploadMsg, $frontEnd, $chunkMethod, 0 )) {
					jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 104,
					'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
					'details' => Text::_($errUploadMsg))));
				}

				if (PhocaGalleryFile::exists($filepathImgFinal)) {
					jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 108,
					'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
					'details' => Text::_('COM_PHOCAGALLERY_FILE_ALREADY_EXISTS'))));
				}


				if(!File::upload($file['tmp_name'], $filepathImgFinal, false, true)) {
					jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 109,
					'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
					'details' => Text::_('COM_PHOCAGALLERY_ERROR_UNABLE_TO_UPLOAD_FILE') .'<br />'
					. Text::_('COM_PHOCAGALLERY_CHECK_PERMISSIONS_OWNERSHIP'))));
				}

				if ((int)$frontEnd > 0) {
					return $file['name'];
				}

				jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'OK', 'code' => 200,
				'message' => Text::_('COM_PHOCAGALLERY_SUCCESS').': ',
				'details' => Text::_('COM_PHOCAGALLERY_IMAGES_UPLOADED'))));


			}
		} else {
			// No isset $file['name']

			jexit(json_encode(array( 'jsonrpc' => '2.0', 'result' => 'error', 'code' => 104,
			'message' => Text::_('COM_PHOCAGALLERY_ERROR').': ',
			'details' => Text::_('COM_PHOCAGALLERY_ERROR_UNABLE_TO_UPLOAD_FILE'))));
		}

	}


	public static function realSingleUpload( $frontEnd = 0 ) {

	//	$paramsC 		= JComponentHelper::getParams('com_phocagallery');
	//	$chunkMethod 	= $paramsC->get( 'multiple_upload_chunk', 0 );
	//	$uploadMethod 	= $paramsC->get( 'multiple_upload_method', 4 );

		$app			= Factory::getApplication();
		Session::checkToken( 'request' ) or jexit( 'ERROR: '. Text::_('COM_PHOCAGALLERY_INVALID_TOKEN'));

		$app->allowCache(false);

		$path			= PhocaGalleryPath::getPath();
		$file 			= Factory::getApplication()->getInput()->files->get( 'Filedata', null );
		$folder			= Factory::getApplication()->getInput()->get( 'folder', '', '', 'path' );
		$format			= Factory::getApplication()->getInput()->get( 'format', 'html', '', 'cmd');
		$return			= Factory::getApplication()->getInput()->get( 'return-url', null, 'post', 'base64' );//includes field
		$viewBack		= Factory::getApplication()->getInput()->get( 'viewback', '', '', '' );
		$tab			= Factory::getApplication()->getInput()->get( 'tab', '', '', 'string' );
		$field			= Factory::getApplication()->getInput()->get( 'field' );
		$errUploadMsg	= '';
		$folderUrl 		= $folder;
		$tabUrl			= '';
		$component		= Factory::getApplication()->getInput()->get( 'option', '', '', 'string' );

		// In case no return value will be sent (should not happen)
		if ($component != '' && $frontEnd == 0) {
			$componentUrl 	= 'index.php?option='.$component;
		} else {
			$componentUrl	= 'index.php';
		}
		if ($tab != '') {
			$tabUrl = '&tab='.(string)$tab;
		}

		$ftp = ClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		if (isset($file['name'])) {
			$file['name']	= File::makeSafe($file['name']);
		}


		if (isset($folder) && $folder != '') {
			$folder	= $folder . '/';
		}


		// All HTTP header will be overwritten with js message
		if (isset($file['name'])) {
			$filepath = Path::clean($path->image_abs.$folder.strtolower($file['name']));

			if (!PhocaGalleryFileUpload::canUpload( $file, $errUploadMsg, $frontEnd )) {

				if ($errUploadMsg == 'COM_PHOCAGALLERY_WARNING_FILE_TOOLARGE') {
					$errUploadMsg 	= Text::_($errUploadMsg) . ' ('.PhocaGalleryFile::getFileSizeReadable($file['size']).')';
				} else if ($errUploadMsg == 'COM_PHOCAGALLERY_WARNING_FILE_TOOLARGE_RESOLUTION') {
					$imgSize		= PhocaGalleryImage::getImageSize($file['tmp_name']);
					$errUploadMsg 	= Text::_($errUploadMsg) . ' ('.(int)$imgSize[0].' x '.(int)$imgSize[1].' px)';
				} else {
					$errUploadMsg 	= Text::_($errUploadMsg);
				}


				/*if ($return) {
					$app->enqueueMessage( $errUploadMsg, 'error');
					$app->redirect(base64_decode($return).'&folder='.$folderUrl);
					exit;
				} else {
					$app->enqueueMessage( $errUploadMsg, 'error');
					$app->redirect($componentUrl, $errUploadMsg, 'error');
					exit;
				}*/


				if ($return) {
					$app->enqueueMessage( $errUploadMsg, 'error');
					if ($frontEnd > 0) {

						$app->redirect(base64_decode($return));
					} else {
						$app->redirect(base64_decode($return).'&folder='.$folderUrl);
					}
					exit;
				} else {
					$app->enqueueMessage( $errUploadMsg, 'error');
					$app->redirect($componentUrl);
					exit;
				}
			}

			if (PhocaGalleryFile::exists($filepath)) {
				if ($return) {
					$app->enqueueMessage( Text::_('COM_PHOCAGALLERY_FILE_ALREADY_EXISTS'), 'error');
					$app->redirect(base64_decode($return).'&folder='.$folderUrl);
					exit;
				} else {
					$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_FILE_ALREADY_EXISTS'), 'error');
					$app->redirect($componentUrl);
					exit;
				}
			}

			if (!File::upload($file['tmp_name'], $filepath, false, true)) {
				if ($return) {
					$app->enqueueMessage( Text::_('COM_PHOCAGALLERY_ERROR_UNABLE_TO_UPLOAD_FILE'), 'error');
					$app->redirect(base64_decode($return).'&folder='.$folderUrl);
					exit;
				} else {
					$app->enqueueMessage( Text::_('COM_PHOCAGALLERY_ERROR_UNABLE_TO_UPLOAD_FILE'), 'error');
					$app->redirect($componentUrl);
					exit;
				}
			} else {

				if ((int)$frontEnd > 0) {
					return $file['name'];
				}

				if ($return) {
					$app->enqueueMessage( Text::_('COM_PHOCAGALLERY_SUCCESS_FILE_UPLOAD'));
					$app->redirect(base64_decode($return).'&folder='.$folderUrl);
					exit;
				} else {
					$app->enqueueMessage( Text::_('COM_PHOCAGALLERY_SUCCESS_FILE_UPLOAD'));
					$app->redirect($componentUrl);
					exit;
				}
			}
		} else {
			$msg = Text::_('COM_PHOCAGALLERY_ERROR_UNABLE_TO_UPLOAD_FILE');
			if ($return) {
				$app->enqueueMessage( $msg);
				$app->redirect(base64_decode($return).'&folder='.$folderUrl);
				exit;
			} else {
				switch ($viewBack) {
					case 'phocagalleryi':
						$app->enqueueMessage( $msg, 'error');
						$app->redirect('index.php?option=com_phocagallery&view=phocagalleryi&tmpl=component'.$tabUrl.'&folder='.$folder.'&field='.$field);
						exit;
					break;

					case 'phocagallerym':
						$app->enqueueMessage( $msg, 'error');
						$app->redirect('index.php?option=com_phocagallery&view=phocagallerym&layout=form&hidemainmenu=1'.$tabUrl.'&folder='.$folder);
						exit;
					break;

					default:
						$app->enqueueMessage( $msg, 'error');
						$app->redirect('index.php?option=com_phocagallery');
						exit;
					break;

				}
			}
		}

	}

	// realJavaUpload() is obsolete (Java applet uploader removed) and kept only as a stub.
	// public static function realJavaUpload( $frontEnd = 0 ) { ... }


	/**
	 * can Upload
	 *
	 * @param array $file
	 * @param string $errorUploadMsg
	 * @param int $frontEnd - if it is called from frontend or backend (1  - category view, 2 user control panel)
	 * @param boolean $chunkMethod - if chunk method is used (multiple upload) then there are special rules
	 * @param string $realSize - if chunk method is used we get info about real size of file (not only the part)
	 * @return boolean True on success
	 * @since 1.5
	 */


	public static function canUpload( $file, &$errUploadMsg, $frontEnd = 0, $chunkEnabled = 0, $realSize = 0 ) {

		$params 	= ComponentHelper::getParams( 'com_phocagallery' );
		$paramsL 	= array();
		$paramsL['upload_extensions'] 	= 'gif,jpg,png,jpeg,webp,avif';
		$paramsL['image_extensions'] 	= 'gif,jpg,png,jpeg,webp,avif';
		$paramsL['upload_mime']			= 'image/jpeg,image/gif,image/png,image/webp,image/avif';
		$paramsL['upload_mime_illegal']	='application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip,text/html';

		// The file doesn't exist
		if(empty($file['name'])) {
			$errUploadMsg = 'COM_PHOCAGALLERY_ERROR_UNABLE_TO_UPLOAD_FILE';
			return false;
		}

		// Path check
		$pathObj = PhocaGalleryPath::getPath();
		$folder = Factory::getApplication()->getInput()->get('folder', '', '', 'path');

		if (!empty($folder)) {
			try {
				Path::check($pathObj->image_abs . $folder);
			} catch (\Exception $e) {
				$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_INVALID_PATH';
				return false;
			}
		}

		// Not safe file
		jimport('joomla.filesystem.file');
		if ($file['name'] !== File::makesafe($file['name'])) {
			$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_FILENAME';
			return false;
		}

		$format = strtolower(File::getExt($file['name']));

		// Allowable extension
		$allowable = explode( ',', $paramsL['upload_extensions']);
		if ($format == '' || $format == false || (!in_array($format, $allowable))) {
		//if (!in_array($format, $allowable)) {
			$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_FILETYPE';
			return false;
		}

		// 'COM_PHOCAGALLERY_MAX_RESOLUTION'
		$imgSize		= PhocaGalleryImage::getImageSize($file['tmp_name']);
		$maxResWidth 	= $params->get( 'upload_maxres_width', 3072 );
		$maxResHeight 	= $params->get( 'upload_maxres_height', 2304 );
		if (((int)$maxResWidth > 0 && (int)$maxResHeight > 0)
		&& ((int)$imgSize[0] > (int)$maxResWidth || (int)$imgSize[1] > (int)$maxResHeight)) {
			$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_FILE_TOOLARGE_RESOLUTION';
			return false;
		}

		// User (only in ucp) - Check the size of all images by users
		if ($frontEnd == 2) {
			$user 				= Factory::getUser();
			$maxUserImageSize 	= (int)$params->get( 'user_images_max_size', 20971520 );

			if ($chunkEnabled == 1) {
				$fileSize = $realSize;
			} else {
				$fileSize = $file['size'];
			}
			$allFileSize = PhocaGalleryFileUploadFront::getSizeAllOriginalImages($fileSize, $user->id);

			if ((int)$maxUserImageSize > 0 && (int) $allFileSize > $maxUserImageSize) {
				$errUploadMsg = Text::_('COM_PHOCAGALLERY_WARNING_USERIMAGES_TOOLARGE');
				return false;
			}
		}

		// Max size of image
		// If chunk method is used, we need to get computed size
		$maxSize = $params->get( 'upload_maxsize', 3145728 );
		if ($chunkEnabled == 1) {
			if ((int)$maxSize > 0 && (int)$realSize > (int)$maxSize) {
				$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_FILE_TOOLARGE';
				return false;
			}
		} else {
			if ((int)$maxSize > 0 && (int)$file['size'] > (int)$maxSize) {
				$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_FILE_TOOLARGE';
				return false;
			}
		}

		$user = Factory::getUser();
		$imginfo = null;


		// Image check
		$images = explode( ',', $paramsL['image_extensions']);
		if(in_array($format, $images)) { // if its an image run it through decode and re-encode
			if ($chunkEnabled != 1) {

				// Security setting controlling the re-encode step below:
				// 1 = re-encode the image with GD to strip polyglot payloads, EXIF data is lost
				// 2 = re-encode the image with GD to strip polyglot payloads, EXIF data is restored
				//     afterwards for JPG, PNG and WEBP (default)
				// 3 = do not re-encode the image at all
				$reencodeMode = (int) $params->get('upload_image_reencode', 2);

				if ($reencodeMode !== 3) {

					// Capture the original EXIF data and ICC colour profile (camera
					// info, GPS, orientation, colour space, ...) before they get
					// stripped by the re-encode step below. Only needed when
					// metadata preservation is enabled.
					$exifSegment = null;
					$iccProfile  = null;
					if ($reencodeMode === 2) {
						$exifSegment = self::extractExifSegment($file['tmp_name']);
						$iccProfile  = self::extractIccProfile($file['tmp_name']);
					}

					$decoded = self::decodeAndValidateImage($file['tmp_name']);
					if (!$decoded) {
						$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_INVALIDIMG';
						return false;
					}

					// Re-encode and save over the temp file to strip any polyglot payloads
					if (!self::encodeAndSaveImage($decoded['image'], $file['tmp_name'], $decoded['mime'])) {
						imagedestroy($decoded['image']);
						$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_INVALIDIMG';
						return false;
					}
					imagedestroy($decoded['image']);

					// Restore the original EXIF data and ICC profile into the freshly
					// re-encoded file. Only the raw, already-existing bytes are copied
					// back - they are never parsed here, so this is no more risky than
					// an EXIF/ICC-preserving FTP upload. Supported for JPG, PNG and
					// WEBP only. ICC is reinserted first so that, for formats where
					// insertion order matters (JPEG/PNG), the final layout matches the
					// EXIF-before-ICC ordering commonly produced by cameras/software.
					if ($iccProfile !== null) {
						self::reinsertIccProfile($file['tmp_name'], $decoded['mime'], $iccProfile);
					}
					if ($exifSegment !== null) {
						self::reinsertExifSegment($file['tmp_name'], $decoded['mime'], $exifSegment);
					}

					// Remove the "CREATOR: gd-jpeg ..." comment GD writes into every
					// JPEG it encodes, so the server's image library/version isn't
					// disclosed in every uploaded image. Applies whenever a JPEG was
					// actually re-encoded (mode 1 or 2), independent of the EXIF/ICC
					// preservation choice above.
					if ($decoded['mime'] === 'image/jpeg') {
						self::stripJpegComment($file['tmp_name']);
					}
				}
			}
		} else if(!in_array($format, $images)) {
			// if its not an image...and we're not ignoring it
			$allowed_mime = explode(',', $paramsL['upload_mime']);
			$illegal_mime = explode(',', $paramsL['upload_mime_illegal']);
			if(function_exists('finfo_open')) {
				// We have fileinfo
				$finfo = finfo_open(FILEINFO_MIME);
				$type = finfo_file($finfo, $file['tmp_name']);
				if(strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
					$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_INVALIDMIME';
					return false;
				}
				finfo_close($finfo);
			} else if(function_exists('mime_content_type')) {
				// we have mime magic
				$type = mime_content_type($file['tmp_name']);
				if(strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
					$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_INVALIDMIME';
					return false;
				}
			}/* else if(!$user->authorize( 'login', 'administrator' )) {
				$errUploadMsg =  = 'WARNNOTADMIN';
				return false;
			}*/
		}

		// XSS Check
		$xss_check = file_get_contents($file['tmp_name'], false, null, -1, 256);

		$html_tags = array(
			'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blackface', 'blink',
			'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del',
			'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
			'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext',
			'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object',
			'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow', 'sidebar',
			'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title',
			'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--',
		);

		foreach ($html_tags as $tag)
		{
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if (stripos($xss_check, '<' . $tag . ' ') !== false || stripos($xss_check, '<' . $tag . '>') !== false)
			{
				$errUploadMsg = 'COM_PHOCAGALLERY_WARNING_IEXSS';
				return false;
			}
		}

		return true;
	}
	/*
	function uploader($id='file-upload', $params = array()) {

		$path = 'media/com_phocagallery/js/upload/';
		JHtml::script('swf.js', $path, false ); // mootools are loaded yet
		JHtml::script('uploader.js', $path, false );// mootools are loaded yet

		static $uploaders;

		if (!isset($uploaders)) {
			$uploaders = array();
		}

		if (isset($uploaders[$id]) && ($uploaders[$id])) {
			return;
		}

		// Setup options object
		$opt['url']					= (isset($params['targetURL'])) ? $params['targetURL'] : null ;
		$opt['swf']					= (isset($params['swf'])) ? $params['swf'] : Uri::root(true).'/media/system/swf/uploader.swf';
		$opt['multiple']			= (isset($params['multiple']) && !($params['multiple'])) ? '\\false' : '\\true';
		$opt['queued']				= (isset($params['queued']) && !($params['queued'])) ? '\\false' : '\\true';
		$opt['queueList']			= (isset($params['queueList'])) ? $params['queueList'] : 'upload-queue';
		$opt['instantStart']		= (isset($params['instantStart']) && ($params['instantStart'])) ? '\\true' : '\\false';
		$opt['allowDuplicates']		= (isset($params['allowDuplicates']) && !($params['allowDuplicates'])) ? '\\false' : '\\true';
		$opt['limitSize']			= (isset($params['limitSize']) && ($params['limitSize'])) ? (int)$params['limitSize'] : null;
		$opt['limitFiles']			= (isset($params['limitFiles']) && ($params['limitFiles'])) ? (int)$params['limitFiles'] : null;
		$opt['optionFxDuration']	= (isset($params['optionFxDuration'])) ? (int)$params['optionFxDuration'] : null;
		$opt['container']			= (isset($params['container'])) ? '\\$('.$params['container'].')' : '\\$(\''.$id.'\').getParent()';
		$opt['types']				= (isset($params['types'])) ?'\\'.$params['types'] : '\\{\'All Files (*.*)\': \'*.*\'}';

		// Optional functions
		$opt['createReplacement']	= (isset($params['createReplacement'])) ? '\\'.$params['createReplacement'] : null;
		$opt['onComplete']			= (isset($params['onComplete'])) ? '\\'.$params['onComplete'] : null;
		$opt['onAllComplete']		= (isset($params['onAllComplete'])) ? '\\'.$params['onAllComplete'] : null;

/*  types: Object with (description: extension) pairs, Default: Images (*.jpg; *.jpeg; *.gif; *.png)
 */
/*
		$options = PhocaGalleryFileUpload::getJSObject($opt);

		// Attach tooltips to document
		$document =Factory::getDocument();
		$uploaderInit = 'sBrowseCaption=\''.Text::_('Browse Files', true).'\';
				sRemoveToolTip=\''.Text::_('Remove from queue', true).'\';
				window.addEvent(\'load\', function(){
				var Uploader = new FancyUpload($(\''.$id.'\'), '.$options.');
				$(\'upload-clear\').adopt(new Element(\'input\', { type: \'button\', events: { click: Uploader.clearList.bind(Uploader, [false])}, value: \''.Text::_('Clear Completed').'\' }));				});';
		$document->addScriptDeclaration($uploaderInit);

		// Set static array
		$uploaders[$id] = true;
		return;
	}

	protected static function getJSObject($array=array())
	{
		// Initialise variables.
		$object = '{';

		// Iterate over array to build objects
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v)) {
				continue;
			}
			if (!is_array($v) && !is_object($v))
			{
				$object .= ' '.$k.': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'".$v."'";
				$object .= ',';
			}
			else {
				$object .= ' '.$k.': '.PhocaGalleryFileUpload::getJSObject($v).',';
			}
		}
		if (substr($object, -1) == ',') {
			$object = substr($object, 0, -1);
		}
		$object .= '}';

		return $object;
	}*/

	public static function renderFTPaccess() {

		$ftpOutput = '<fieldset title="'.Text::_('COM_PHOCAGALLERY_FTP_LOGIN_LABEL'). '">'
		.'<legend>'. Text::_('COM_PHOCAGALLERY_FTP_LOGIN_LABEL').'</legend>'
		.Text::_('COM_PHOCAGALLERY_FTP_LOGIN_DESC')
		.'<table class="adminform nospace">'
		.'<tr>'
		.'<td width="120"><label for="username">'. Text::_('JGLOBAL_USERNAME').':</label></td>'
		.'<td><input type="text" id="username" name="username" class="input_box" size="70" value="" /></td>'
		.'</tr>'
		.'<tr>'
		.'<td width="120"><label for="password">'. Text::_('JGLOBAL_PASSWORD').':</label></td>'
		.'<td><input type="password" id="password" name="password" class="input_box" size="70" value="" /></td>'
		.'</tr></table></fieldset>';
		return $ftpOutput;
	}

	public static function renderCreateFolder($sessName, $sessId, $currentFolder, $viewBack, $attribs = '') {

		if ($attribs != '') {
			$attribs = '&amp;'.$attribs;
		}

		$folderOutput = '<form action="'. Uri::base()
		.'index.php?option=com_phocagallery&task=phocagalleryu.createfolder&amp;'. $sessName.'='.$sessId.'&amp;'
		.Session::getFormToken().'=1&amp;viewback='.$viewBack.'&amp;'
		.'folder='.PhocaGalleryText::filterValue($currentFolder, 'folderpath').$attribs .'" name="folderForm" id="folderForm" method="post">'
		//.'<fieldset id="folderview">'
		//.'<legend>'.JText::_('COM_PHOCAGALLERY_FOLDER').'</legend>'
		.'<div class="ph-in"><div class="ph-head-form">'.Text::_('COM_PHOCAGALLERY_CREATE_FOLDER').'</div>'
		.'<dl class="dl-horizontal ph-input">'
		.'<dt><input class="form-control" type="text" id="foldername" name="foldername"  /></dt>'
		.'<input class="update-folder" type="hidden" name="folderbase" id="folderbase" value="'.PhocaGalleryText::filterValue($currentFolder, 'folderpath').'" />'
		.'<dd><button class="btn btn-success" type="submit">'. Text::_( 'COM_PHOCAGALLERY_CREATE_FOLDER' ).'</button></dd>'
		.'</dl></div>'
	    //.'</fieldset>'
		.HTMLHelper::_( 'form.token' )
		.'</form>';
		return $folderOutput;
	}

	/**
	 * Strictly validate and decode an image using GD to prevent polyglot payloads.
	 *
	 * @param   string  $path  Absolute path to the temporary file
	 *
	 * @return  array|false  Array with 'mime' and 'image' keys on success, false on failure
	 */
	public static function decodeAndValidateImage($path) {
		if (!is_file($path)) {
			return false;
		}

		$mimeType = false;

		if (function_exists('finfo_open')) {
		  $finfo    = finfo_open(FILEINFO_MIME_TYPE);
		  $mimeType = finfo_file($finfo, $path);
		  finfo_close($finfo);
		} elseif (function_exists('mime_content_type')) {
		  $mimeType = mime_content_type($path);
		}

		if (!$mimeType) {
		  return false;
		}

		$handlers = [
			'image/jpeg' => 'imagecreatefromjpeg',
			'image/png'  => 'imagecreatefrompng',
			'image/gif'  => 'imagecreatefromgif',
			'image/webp' => 'imagecreatefromwebp',
			'image/avif' => 'imagecreatefromavif',
		];

		if (!isset($handlers[$mimeType]) || !function_exists($handlers[$mimeType])) {
			return false;
		}

		$handler = $handlers[$mimeType];
		$image   = @$handler($path);

		if (!$image) {
			return false;
		}

		$width  = imagesx($image);
		$height = imagesy($image);

		if ($width < 1 || $height < 1 || $width > 20000 || $height > 20000) {
			imagedestroy($image);
			return false;
		}

		return ['mime' => $mimeType, 'image' => $image];
	}

	/**
	 * Re-encode and save a decoded GD image resource to disk in its own format.
	 *
	 * @param   resource  $image     GD image resource
	 * @param   string    $path      Destination file path
	 * @param   string    $mimeType  MIME type (image/jpeg, image/png, etc.)
	 *
	 * @return  bool
	 */
	public static function encodeAndSaveImage($image, $path, $mimeType) {
		$quality = 90;

		if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}

		switch ($mimeType) {
			case 'image/jpeg': return imagejpeg($image, $path, $quality);
			case 'image/png':  return imagepng($image, $path, 9);
			case 'image/gif':  return imagegif($image, $path);
			case 'image/webp': return imagewebp($image, $path, $quality);
			case 'image/avif': return function_exists('imageavif') ? imageavif($image, $path, $quality) : false;
		}
		return false;
	}

	/**
	 * Extract the raw EXIF data from an image file before it is re-encoded by
	 * encodeAndSaveImage(), so it can be restored afterwards with
	 * reinsertExifSegment(). Only the raw bytes of the existing segment/chunk
	 * are read and returned - nothing is parsed or interpreted, so this carries
	 * the same risk profile as leaving EXIF data untouched (e.g. an FTP upload).
	 *
	 * Currently supports JPEG (APP1 "Exif" marker), PNG (eXIf chunk, PNG spec
	 * 2017+) and WEBP (RIFF "EXIF" chunk). GIF has no EXIF concept. AVIF is
	 * not covered yet.
	 *
	 * @param   string  $path  Absolute path to the original (not yet re-encoded) file
	 *
	 * @return  array|null  ['type' => 'jpeg'|'png'|'webp', 'data' => string raw segment/chunk] or null
	 */
	public static function extractExifSegment($path) {
		if (!is_file($path)) {
			return null;
		}

		$handle = @fopen($path, 'rb');
		if (!$handle) {
			return null;
		}

		$header = fread($handle, 12);

		// JPEG: walk the markers looking for APP1 "Exif\0\0"
		if (substr($header, 0, 2) === "\xFF\xD8") {
			fseek($handle, 2);

			while (!feof($handle)) {
				$b1 = fread($handle, 1);
				if ($b1 !== "\xFF") {
					break;
				}
				$b2 = fread($handle, 1);
				// Skip any 0xFF fill bytes before the real marker code
				while ($b2 === "\xFF" && !feof($handle)) {
					$b2 = fread($handle, 1);
				}
				if ($b2 === false || $b2 === '') {
					break;
				}
				$markerType = $b2;

				// SOS (start of scan): compressed image data follows, no more metadata markers
				if ($markerType === "\xDA") {
					break;
				}
				// Markers without a length/payload
				if ($markerType === "\x01" || (ord($markerType) >= 0xD0 && ord($markerType) <= 0xD9)) {
					continue;
				}

				$lenBytes = fread($handle, 2);
				if (strlen($lenBytes) < 2) {
					break;
				}
				$segLen = (ord($lenBytes[0]) << 8) + ord($lenBytes[1]);
				if ($segLen < 2) {
					break;
				}

				$payload = fread($handle, $segLen - 2);

				if ($markerType === "\xE1" && substr($payload, 0, 6) === "Exif\x00\x00") {
					fclose($handle);
					return ['type' => 'jpeg', 'data' => "\xFF\xE1" . $lenBytes . $payload];
				}
			}

			fclose($handle);
			return null;
		}

		// PNG: walk the chunks looking for "eXIf"
		if ($header === "\x89PNG\x0D\x0A\x1A\x0A") {
			fseek($handle, 8);

			while (!feof($handle)) {
				$lenBytes = fread($handle, 4);
				$type     = fread($handle, 4);
				if (strlen($lenBytes) < 4 || strlen($type) < 4) {
					break;
				}
				$unpacked = unpack('N', $lenBytes);
				$chunkLen = $unpacked[1];

				// Sanity cap - a legitimate EXIF chunk is never anywhere near this large
				if ($chunkLen > 1048576) {
					break;
				}

				if ($type === 'eXIf') {
					$data = fread($handle, $chunkLen);
					$crc  = fread($handle, 4);
					fclose($handle);
					if (strlen($data) === $chunkLen && strlen($crc) === 4) {
						return ['type' => 'png', 'data' => $lenBytes . $type . $data . $crc];
					}
					return null;
				}

				if ($type === 'IDAT' || $type === 'IEND') {
					break; // eXIf, when present, always comes before image data
				}

				fseek($handle, $chunkLen + 4, SEEK_CUR); // skip chunk data + CRC
			}

			fclose($handle);
			return null;
		}

		// WEBP: RIFF container, walk the chunks looking for the "EXIF" chunk
		if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP') {
			fseek($handle, 12);

			while (!feof($handle)) {
				$fourcc    = fread($handle, 4);
				$sizeBytes = fread($handle, 4);
				if (strlen($fourcc) < 4 || strlen($sizeBytes) < 4) {
					break;
				}
				$unpacked  = unpack('V', $sizeBytes);
				$chunkSize = $unpacked[1];

				// Sanity cap - a legitimate EXIF chunk is never anywhere near this large
				if ($chunkSize > 10485760) {
					break;
				}

				$data = fread($handle, $chunkSize);
				if (strlen($data) !== $chunkSize) {
					break;
				}
				// RIFF chunks are padded to an even number of bytes
				if ($chunkSize % 2 === 1) {
					fread($handle, 1);
				}

				if ($fourcc === 'EXIF') {
					fclose($handle);
					return ['type' => 'webp', 'data' => $data];
				}
			}

			fclose($handle);
			return null;
		}

		fclose($handle);
		return null;
	}

	/**
	 * Re-insert a previously extracted EXIF segment/chunk (see extractExifSegment())
	 * into a freshly re-encoded file. No-op if the type doesn't match the
	 * re-encoded MIME type or the file structure isn't as expected.
	 *
	 * @param   string  $path         Absolute path to the re-encoded file
	 * @param   string  $mimeType     MIME type of the re-encoded file
	 * @param   array   $exifSegment  Value returned by extractExifSegment()
	 *
	 * @return  bool
	 */
	public static function reinsertExifSegment($path, $mimeType, $exifSegment) {
		if (!is_file($path) || empty($exifSegment['data']) || empty($exifSegment['type'])) {
			return false;
		}

		$contents = file_get_contents($path);
		if ($contents === false) {
			return false;
		}

		if ($mimeType === 'image/jpeg' && $exifSegment['type'] === 'jpeg') {
			if (substr($contents, 0, 2) !== "\xFF\xD8") {
				return false;
			}
			// Insert right after the SOI marker, before any segments GD wrote
			$newContents = "\xFF\xD8" . $exifSegment['data'] . substr($contents, 2);
			return file_put_contents($path, $newContents) !== false;
		}

		if ($mimeType === 'image/png' && $exifSegment['type'] === 'png') {
			if (substr($contents, 0, 8) !== "\x89PNG\x0D\x0A\x1A\x0A") {
				return false;
			}
			// IHDR is always the first chunk: 8-byte signature + 4 (len) + 4 (type) + 13 (data) + 4 (crc)
			$ihdrEnd = 8 + 4 + 4 + 13 + 4;
			if (strlen($contents) < $ihdrEnd) {
				return false;
			}
			$newContents = substr($contents, 0, $ihdrEnd) . $exifSegment['data'] . substr($contents, $ihdrEnd);
			return file_put_contents($path, $newContents) !== false;
		}

		if ($mimeType === 'image/webp' && $exifSegment['type'] === 'webp') {
			return self::injectWebpMetadata($path, $exifSegment['data'], null);
		}

		return false;
	}

	/**
	 * Extract the raw ICC colour profile from an image file before it is
	 * re-encoded, so it can be restored afterwards with reinsertIccProfile().
	 * Only the raw bytes of the existing segment/chunk are read and returned -
	 * nothing is parsed or interpreted.
	 *
	 * Currently supports JPEG (one or more APP2 "ICC_PROFILE" segments - large
	 * profiles are legitimately split across several), PNG (iCCP chunk) and
	 * WEBP (RIFF "ICCP" chunk).
	 *
	 * @param   string  $path  Absolute path to the original (not yet re-encoded) file
	 *
	 * @return  array|null  ['type' => 'jpeg'|'png'|'webp', 'data' => ...] or null.
	 *                      For 'jpeg', 'data' is an array of raw segment strings
	 *                      (in file order); for 'png'/'webp' it is a single string.
	 */
	public static function extractIccProfile($path) {
		if (!is_file($path)) {
			return null;
		}

		$handle = @fopen($path, 'rb');
		if (!$handle) {
			return null;
		}

		$header = fread($handle, 12);

		// JPEG: collect every APP2 "ICC_PROFILE\0" segment, in file order
		if (substr($header, 0, 2) === "\xFF\xD8") {
			fseek($handle, 2);
			$segments = [];

			while (!feof($handle)) {
				$b1 = fread($handle, 1);
				if ($b1 !== "\xFF") {
					break;
				}
				$b2 = fread($handle, 1);
				while ($b2 === "\xFF" && !feof($handle)) {
					$b2 = fread($handle, 1);
				}
				if ($b2 === false || $b2 === '') {
					break;
				}
				$markerType = $b2;

				if ($markerType === "\xDA") {
					break;
				}
				if ($markerType === "\x01" || (ord($markerType) >= 0xD0 && ord($markerType) <= 0xD9)) {
					continue;
				}

				$lenBytes = fread($handle, 2);
				if (strlen($lenBytes) < 2) {
					break;
				}
				$segLen = (ord($lenBytes[0]) << 8) + ord($lenBytes[1]);
				if ($segLen < 2) {
					break;
				}

				$payload = fread($handle, $segLen - 2);

				if ($markerType === "\xE2" && substr($payload, 0, 12) === "ICC_PROFILE\x00") {
					$segments[] = "\xFF\xE2" . $lenBytes . $payload;
				}
			}

			fclose($handle);
			return empty($segments) ? null : ['type' => 'jpeg', 'data' => $segments];
		}

		// PNG: iCCP chunk (must precede PLTE/IDAT, so stop looking once we hit those)
		if ($header === "\x89PNG\x0D\x0A\x1A\x0A") {
			fseek($handle, 8);

			while (!feof($handle)) {
				$lenBytes = fread($handle, 4);
				$type     = fread($handle, 4);
				if (strlen($lenBytes) < 4 || strlen($type) < 4) {
					break;
				}
				$unpacked = unpack('N', $lenBytes);
				$chunkLen = $unpacked[1];

				// Sanity cap - ICC profiles are occasionally large but not this large
				if ($chunkLen > 5242880) {
					break;
				}

				if ($type === 'iCCP') {
					$data = fread($handle, $chunkLen);
					$crc  = fread($handle, 4);
					fclose($handle);
					if (strlen($data) === $chunkLen && strlen($crc) === 4) {
						return ['type' => 'png', 'data' => $lenBytes . $type . $data . $crc];
					}
					return null;
				}

				if ($type === 'PLTE' || $type === 'IDAT' || $type === 'IEND') {
					break; // iCCP, if present, always precedes these
				}

				fseek($handle, $chunkLen + 4, SEEK_CUR);
			}

			fclose($handle);
			return null;
		}

		// WEBP: ICCP chunk
		if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP') {
			fseek($handle, 12);

			while (!feof($handle)) {
				$fourcc    = fread($handle, 4);
				$sizeBytes = fread($handle, 4);
				if (strlen($fourcc) < 4 || strlen($sizeBytes) < 4) {
					break;
				}
				$unpacked  = unpack('V', $sizeBytes);
				$chunkSize = $unpacked[1];

				if ($chunkSize > 5242880) {
					break;
				}

				$data = fread($handle, $chunkSize);
				if (strlen($data) !== $chunkSize) {
					break;
				}
				if ($chunkSize % 2 === 1) {
					fread($handle, 1);
				}

				if ($fourcc === 'ICCP') {
					fclose($handle);
					return ['type' => 'webp', 'data' => $data];
				}
			}

			fclose($handle);
			return null;
		}

		fclose($handle);
		return null;
	}

	/**
	 * Re-insert a previously extracted ICC profile (see extractIccProfile())
	 * into a freshly re-encoded file. No-op if the type doesn't match the
	 * re-encoded MIME type or the file structure isn't as expected.
	 *
	 * @param   string  $path        Absolute path to the re-encoded file
	 * @param   string  $mimeType    MIME type of the re-encoded file
	 * @param   array   $iccProfile  Value returned by extractIccProfile()
	 *
	 * @return  bool
	 */
	public static function reinsertIccProfile($path, $mimeType, $iccProfile) {
		if (!is_file($path) || empty($iccProfile['data']) || empty($iccProfile['type'])) {
			return false;
		}

		if ($mimeType === 'image/jpeg' && $iccProfile['type'] === 'jpeg') {
			$contents = file_get_contents($path);
			if ($contents === false || substr($contents, 0, 2) !== "\xFF\xD8") {
				return false;
			}
			$segments = is_array($iccProfile['data']) ? $iccProfile['data'] : [$iccProfile['data']];
			// Insert right after the SOI marker, before any segments GD wrote
			$newContents = "\xFF\xD8" . implode('', $segments) . substr($contents, 2);
			return file_put_contents($path, $newContents) !== false;
		}

		if ($mimeType === 'image/png' && $iccProfile['type'] === 'png') {
			$contents = file_get_contents($path);
			if ($contents === false || substr($contents, 0, 8) !== "\x89PNG\x0D\x0A\x1A\x0A") {
				return false;
			}
			$ihdrEnd = 8 + 4 + 4 + 13 + 4;
			if (strlen($contents) < $ihdrEnd) {
				return false;
			}
			$newContents = substr($contents, 0, $ihdrEnd) . $iccProfile['data'] . substr($contents, $ihdrEnd);
			return file_put_contents($path, $newContents) !== false;
		}

		if ($mimeType === 'image/webp' && $iccProfile['type'] === 'webp') {
			return self::injectWebpMetadata($path, null, $iccProfile['data']);
		}

		return false;
	}

	/**
	 * Remove JPEG COM (comment) marker segments from a freshly re-encoded JPEG
	 * file. GD's imagejpeg() always writes a "CREATOR: gd-jpeg ..." comment
	 * identifying the encoder/library version; stripping it avoids disclosing
	 * that in every uploaded image. Only ever called on the file this
	 * extension itself just wrote, never on arbitrary user-supplied JPEGs.
	 *
	 * @param   string  $path  Absolute path to the re-encoded JPEG file
	 *
	 * @return  bool  True on success or if there was nothing to strip, false on error
	 */
	public static function stripJpegComment($path) {
		if (!is_file($path)) {
			return false;
		}

		$contents = file_get_contents($path);
		if ($contents === false || substr($contents, 0, 2) !== "\xFF\xD8") {
			return false;
		}

		$len     = strlen($contents);
		$out     = "\xFF\xD8";
		$pos     = 2;
		$changed = false;

		while ($pos < $len) {
			if ($contents[$pos] !== "\xFF") {
				// Not a well-formed marker boundary - copy the remainder verbatim
				$out .= substr($contents, $pos);
				break;
			}

			$markerStart = $pos;
			$pos++;
			while ($pos < $len && $contents[$pos] === "\xFF") {
				$pos++; // skip fill bytes
			}
			if ($pos >= $len) {
				break;
			}
			$markerType = $contents[$pos];
			$pos++;

			if ($markerType === "\xDA") {
				// Start of scan: copy everything from here to EOF verbatim
				$out .= substr($contents, $markerStart);
				break;
			}
			if ($markerType === "\x01" || (ord($markerType) >= 0xD0 && ord($markerType) <= 0xD9)) {
				$out .= substr($contents, $markerStart, $pos - $markerStart);
				continue;
			}

			if ($pos + 2 > $len) {
				$out .= substr($contents, $markerStart);
				break;
			}
			$segLen = (ord($contents[$pos]) << 8) + ord($contents[$pos + 1]);
			if ($segLen < 2 || $pos + $segLen > $len) {
				$out .= substr($contents, $markerStart);
				break;
			}

			$fullSegment = substr($contents, $markerStart, ($pos - $markerStart) + $segLen);
			$pos += $segLen;

			if ($markerType === "\xFE") {
				$changed = true; // drop GD's comment segment
				continue;
			}

			$out .= $fullSegment;
		}

		if (!$changed) {
			return true; // nothing to strip
		}

		return file_put_contents($path, $out) !== false;
	}

	/**
	 * Rebuild a WEBP file's RIFF container to carry an EXIF chunk and/or an
	 * ICCP chunk. WEBP has no fixed slot to splice metadata into like
	 * JPEG/PNG: both are only recognised by readers if the file has a VP8X
	 * ("extended") header with the corresponding flag bit set, so if GD wrote
	 * a plain (non-extended) file this adds a VP8X chunk. Per the WEBP
	 * container spec, chunk order is VP8X, ICCP, (image data), EXIF - that
	 * order is enforced here regardless of the order the two pieces are
	 * supplied in, and any metadata piece not supplied is left as-is if it
	 * already exists in the file (so calling this once for EXIF and once for
	 * ICC does not clobber the other).
	 *
	 * @param   string       $path      Absolute path to the re-encoded WEBP file
	 * @param   string|null  $exifData  Raw EXIF payload to set, or null to leave as-is
	 * @param   string|null  $iccData   Raw ICC profile payload to set, or null to leave as-is
	 *
	 * @return  bool
	 */
	private static function injectWebpMetadata($path, $exifData = null, $iccData = null) {
		$contents = file_get_contents($path);
		if ($contents === false || strlen($contents) < 12) {
			return false;
		}
		if (substr($contents, 0, 4) !== 'RIFF' || substr($contents, 8, 4) !== 'WEBP') {
			return false;
		}

		// Parse the existing chunks
		$pos    = 12;
		$len    = strlen($contents);
		$chunks = [];

		while ($pos + 8 <= $len) {
			$fourcc    = substr($contents, $pos, 4);
			$unpacked  = unpack('V', substr($contents, $pos + 4, 4));
			$chunkSize = $unpacked[1];
			$dataStart = $pos + 8;

			if ($chunkSize < 0 || $dataStart + $chunkSize > $len) {
				break; // malformed/truncated - stop parsing rather than risk corruption
			}

			$chunks[] = ['fourcc' => $fourcc, 'data' => substr($contents, $dataStart, $chunkSize)];
			$pos = $dataStart + $chunkSize;
			if ($chunkSize % 2 === 1) {
				$pos++; // skip the padding byte
			}
		}

		if (empty($chunks)) {
			return false;
		}

		// Split out VP8X / ICCP / EXIF; everything else (ALPH, VP8, VP8L, ANIM,
		// ANMF, ...) is carried over unchanged, in its original relative order
		$vp8x        = null;
		$iccp        = null;
		$exif        = null;
		$imageChunks = [];

		foreach ($chunks as $c) {
			switch ($c['fourcc']) {
				case 'VP8X': $vp8x = $c; break;
				case 'ICCP': $iccp = $c; break;
				case 'EXIF': $exif = $c; break;
				default:     $imageChunks[] = $c;
			}
		}

		if ($iccData !== null) {
			$iccp = ['fourcc' => 'ICCP', 'data' => $iccData];
		}
		if ($exifData !== null) {
			$exif = ['fourcc' => 'EXIF', 'data' => $exifData];
		}

		if ($iccp === null && $exif === null && $vp8x === null) {
			return true; // nothing to add and no VP8X to keep - leave file as-is
		}

		$hasAlpha = false;
		foreach ($imageChunks as $c) {
			if ($c['fourcc'] === 'ALPH') {
				$hasAlpha = true;
			}
		}

		$flags = ($vp8x !== null && strlen($vp8x['data']) >= 1) ? ord($vp8x['data'][0]) : 0;
		if ($iccp !== null) {
			$flags |= 0x20; // ICC profile flag
		}
		if ($exif !== null) {
			$flags |= 0x08; // Exif flag
		}
		if ($hasAlpha) {
			$flags |= 0x10; // Alpha flag
		}

		if ($vp8x !== null) {
			$vp8x['data'] = chr($flags) . substr($vp8x['data'], 1);
		} else {
			$size = @getimagesize($path);
			if (!$size || (int) $size[0] < 1 || (int) $size[1] < 1) {
				return false;
			}
			$width  = (int) $size[0];
			$height = (int) $size[1];
			if ($width > 16777216 || $height > 16777216) {
				return false; // outside VP8X's 24-bit dimension fields
			}
			$w1 = $width - 1;
			$h1 = $height - 1;
			$vp8xData = chr($flags) . "\x00\x00\x00"
				. chr($w1 & 0xFF) . chr(($w1 >> 8) & 0xFF) . chr(($w1 >> 16) & 0xFF)
				. chr($h1 & 0xFF) . chr(($h1 >> 8) & 0xFF) . chr(($h1 >> 16) & 0xFF);
			$vp8x = ['fourcc' => 'VP8X', 'data' => $vp8xData];
		}

		// Enforce the spec's required ordering: VP8X, ICCP, image data, EXIF
		$ordered   = [$vp8x];
		if ($iccp !== null) {
			$ordered[] = $iccp;
		}
		foreach ($imageChunks as $c) {
			$ordered[] = $c;
		}
		if ($exif !== null) {
			$ordered[] = $exif;
		}

		$body = '';
		foreach ($ordered as $c) {
			$chunkSize = strlen($c['data']);
			$body     .= $c['fourcc'] . pack('V', $chunkSize) . $c['data'];
			if ($chunkSize % 2 === 1) {
				$body .= "\x00";
			}
		}

		$riffSize    = 4 + strlen($body); // 'WEBP' + all chunks
		$newContents = 'RIFF' . pack('V', $riffSize) . 'WEBP' . $body;

		return file_put_contents($path, $newContents) !== false;
	}
}
?>
