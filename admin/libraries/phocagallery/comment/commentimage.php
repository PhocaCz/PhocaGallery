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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

class PhocaGalleryCommentImage
{
	public static function checkUserComment($imgid, $userid) {
		$db =Factory::getDBO();
		$query = 'SELECT co.id AS id'
			    .' FROM #__phocagallery_img_comments AS co'
			    .' WHERE co.imgid = '. (int)$imgid
				.' AND co.userid = '. (int)$userid
				.' ORDER BY co.id';
		$db->setQuery($query, 0, 1);
		$checkUserComment = $db->loadObject();

		if ($checkUserComment) {
			return true;
		}
		return false;
	}

	public static function displayComment($imgid) {

		$db =Factory::getDBO();
		$query = 'SELECT co.id AS id, co.title AS title, co.comment AS comment, co.date AS date, u.name AS name, u.username AS username, uc.avatar AS avatar'
			    .' FROM #__phocagallery_img_comments AS co'
				.' LEFT JOIN #__users AS u ON u.id = co.userid'
				.' LEFT JOIN #__phocagallery_user AS uc ON uc.userid = u.id'
			    /*.' WHERE co.imgid = '. (int)$imgid
				.' AND co.published = 1'
				.' AND uc.published = 1'
				.' AND uc.approved = 1'*/

				 .' WHERE '
				. ' CASE WHEN avatar IS NOT NULL THEN'
				.' co.imgid = '. (int)$imgid
				.' AND co.published = 1'
				.' AND uc.published = 1'
				.' AND uc.approved = 1'
				.' ELSE'
				.' co.imgid = '. (int)$imgid
				.' AND co.published = 1'
				.' END'

				.' ORDER by co.ordering';
		$db->setQuery($query);
		$commentItem = $db->loadObjectList();

		return $commentItem;
	}

	public static function getUserAvatar($userId) {
		$db = Factory::getDBO();
		$query = 'SELECT a.*'
		. ' FROM #__phocagallery_user AS a'
		. ' WHERE a.userid = '.(int)$userId;
		$db->setQuery( $query );
		$avatar = $db->loadObject();
		if(isset($avatar->id)) {
			return $avatar;
		}
		return false;
	}

	public static function renderCommentImageJS() {


		// We only use refresh task (it means to get answer)
		// pgRequest uses pgRequestRefresh site
		$document	 = Factory::getDocument();
		$url		  = 'index.php?option=com_phocagallery&view=commentimga&task=commentimg&format=json&'.Session::getFormToken().'=1';
		$urlRefresh		= 'index.php?option=com_phocagallery&view=commentimga&task=refreshcomment&format=json&'.Session::getFormToken().'=1';
		$imgLoadingUrl = Uri::base(). 'media/com_phocagallery/images/icon-loading3.gif';
		$imgLoadingHTML = '<img src="'.$imgLoadingUrl.'" alt="" />';
		//$js  = '<script type="text/javascript">' . "\n";
		//$js .= 'window.addEvent("domready",function() {
		$js = '
		function pgCommentImage(id, m, container) {
		
			var result 			= "#pg-cv-comment-img-box-result" + id;
			
			var commentTxtArea	= "#pg-cv-comments-editor-img" + id;
			var comment			= jQuery(commentTxtArea).val();
			data = {"commentId": id, "commentValue": comment, "format":"json"};
			
			pgRequest = jQuery.ajax({
                type: "POST",
                url: "'.$urlRefresh.'",
                async: "false",
                cache: "false",
                data: data,
                dataType:"JSON",
                
                beforeSend: function(){
                    jQuery(result).html("'.addslashes($imgLoadingHTML).'");
                    if (m == 2) {
                        var wall = new Masonry(document.getElementById(container));
                    }
                },
                
                success: function(data){
                    if (data.status == 1){
                        jQuery(result).html(data.message);
                    } else if(data.status == 0){
                        jQuery(result).html(data.error);
                    } else {
                        jQuery(result).text("'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
                    }
                    
                    if (m == 2) {
					    var wall = new Masonry(document.getElementById(container));
				    }
                },
                
                error: function(){
                    jQuery(result).text( "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
				
				    if (m == 2) {
					    var wall = new Masonry(document.getElementById(container));
				    }
                }

            })
        }';

		$document->addScriptDeclaration($js);

        //})';

		/*
			if (r) {
					if (r.error == false) {
						jQuery(result).set("html", jsonObj.message);
					} else {
						jQuery(result).set("html", r.error);
					}
				} else {
					jQuery(result).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
				}

				if (m == 2) {
					var wall = new Masonry(document.getElementById(container));
				}



			var pgRequest = new Request.JSON({
			url: "'.$urlRefresh.'",
			method: "post",

			onRequest: function(){
				jQuery(result).set("html", "'.addslashes($imgLoadingHTML).'");
				if (m == 2) {
					var wall = new Masonry(document.getElementById(container));
				}
			  },

			onComplete: function(jsonObj) {
				try {
					var r = jsonObj;
				} catch(e) {
					var r = false;
				}

				if (r) {
					if (r.error == false) {
						jQuery(result).set("html", jsonObj.message);
					} else {
						jQuery(result).set("html", r.error);
					}
				} else {
					jQuery(result).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
				}

				if (m == 2) {
					var wall = new Masonry(document.getElementById(container));
				}
			},

			onFailure: function() {
				jQuery(result).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");

				if (m == 2) {
					var wall = new Masonry(document.getElementById(container));
				}
			}

			})

			pgRequest.send({
				data: {"commentId": id, "commentValue": comment, "format":"json"},
			});

		};';

		//$js .= '});';


		/*
		var resultcomment 	= "pg-cv-comment-img-box-newcomment" + id;
		// Refreshing Voting
						var pgRequestRefresh = new Request.JSON({
							url: "'.$urlRefresh.'",
							method: "post",

							onComplete: function(json2Obj) {
								try {
									var rr = json2Obj;
								} catch(e) {
									var rr = false;
								}

								if (rr) {
									$(resultcomment).set("html", json2Obj.message);
								} else {
									$(resultcomment).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
								}
							},

							onFailure: function() {
								$(resultcomment).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
							}
						})

						pgRequestRefresh.send({
							data: {"commentId": id, "commentValue": comment, "format":"json"}
						});
						//End refreshing comments
						*/

		//$js .= "\n" .'</script>';


	}
}
?>
