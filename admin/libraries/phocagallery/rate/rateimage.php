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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

class PhocaGalleryRateImage
{
	public static function updateVoteStatistics( $imgid ) {

		$db =Factory::getDBO();

		// Get AVG and COUNT
		$query = 'SELECT COUNT(vs.id) AS count, AVG(vs.rating) AS average'
				.' FROM #__phocagallery_img_votes AS vs'
			    .' WHERE vs.imgid = '.(int) $imgid;
		//		.' AND vs.published = 1';
		$db->setQuery($query, 0, 1);
		$votesStatistics = $db->loadObject();
		// if no count, set the average to 0
		if($votesStatistics->count == 0) {
			$votesStatistics->count = (int)0;
			$votesStatistics->average = (float)0;
		}

		if (isset($votesStatistics->count) && isset($votesStatistics->average)) {
			// Insert or update
			$query = 'SELECT vs.id AS id'
					.' FROM #__phocagallery_img_votes_statistics AS vs'
				    .' WHERE vs.imgid = '.(int) $imgid
					.' ORDER BY vs.id';
			$db->setQuery($query, 0, 1);
			$votesStatisticsId = $db->loadObject();

			// Yes, there is id (UPDATE) x No, there isn't (INSERT)
			if (!empty($votesStatisticsId->id)) {

				$query = 'UPDATE #__phocagallery_img_votes_statistics'
					.' SET count = ' .(int)$votesStatistics->count
					.' , average = ' .(float)$votesStatistics->average
				    .' WHERE imgid = '.(int) $imgid;
				$db->setQuery($query);
				$db->execute();

			} else {

				$query = 'INSERT into #__phocagallery_img_votes_statistics'
					.' (id, imgid, count, average)'
				    .' VALUES (null, '.(int)$imgid
					.' , '.(int)$votesStatistics->count
					.' , '.(float)$votesStatistics->average
					.')';
				$db->setQuery($query);
				$db->execute();

			}
		} else {
			return false;
		}
		return true;
	}

	public static function getVotesStatistics($id) {

		$db =Factory::getDBO();
		$query = 'SELECT vs.count AS count, vs.average AS average'
				.' FROM #__phocagallery_img_votes_statistics AS vs'
			    .' WHERE vs.imgid = '.(int) $id;
		$db->setQuery($query, 0, 1);
		$votesStatistics = $db->loadObject();

		return $votesStatistics;
	}

	public static function checkUserVote($imgid, $userid) {

		$db =Factory::getDBO();
		$query = 'SELECT v.id AS id'
			    .' FROM #__phocagallery_img_votes AS v'
			    .' WHERE v.imgid = '. (int)$imgid
				.' AND v.userid = '. (int)$userid;
		$db->setQuery($query, 0, 1);
		$checkUserVote = $db->loadObject();
		if ($checkUserVote) {
			return true;
		}
		return false;
	}

	public static function renderRateImg($id, $displayRating, $small = 1, $refresh = false) {

		$user					= Factory::getUser();
		$neededAccessLevels		= PhocaGalleryAccess::getNeededAccessLevels();
		$access					= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);


		if ($small == 1) {
			$smallO = '-small';
			$ratio = 16;
		} else {
			$smallO = '';
			$ratio = 22;
		}

		$o = '';
		$or = '';

		$href	= 'javascript:void(0);';

		if ((int)$displayRating != 2) {
			return '';
		} else {

			$rating['alreadyratedfile']	= self::checkUserVote( (int)$id, (int)$user->id );

			$rating['notregisteredfile'] 	= true;
			//$rating['usernamefile']		= '';
			if ($access > 0) {
				$rating['notregisteredfile'] 	= false;
				$rating['usernamefile']			= $user->name;
			}

			$rating['votescountfile'] 	= 0;
			$rating['votesaveragefile'] = 0;
			$rating['voteswidthfile'] 	= 0;
			$votesStatistics	= self::getVotesStatistics((int)$id);
			if (!empty($votesStatistics->count)) {
				$rating['votescountfile'] = $votesStatistics->count;
			}
			if (!empty($votesStatistics->average)) {
				$rating['votesaveragefile'] = $votesStatistics->average;
				if ($rating['votesaveragefile'] > 0) {
					$rating['votesaveragefile'] 	= round(((float)$rating['votesaveragefile'] / 0.5)) * 0.5;
					$rating['voteswidthfile']		= $ratio * $rating['votesaveragefile'];
				} else {
					$rating['votesaveragefile'] 	= (int)0;// not float displaying
				}
			}

			// Leave message for already voted images
			//$vote = Factory::getApplication()->input->get('vote', 0, '', 'int');
			$voteMsg = Text::_('COM_PHOCAGALLERY_ALREADY_RATE_IMG');
			//if ($vote == 1) {
			//	$voteMsg = JText::_('COM_PHOCADOWNLOAD_ALREADY_RATED_FILE_THANKS');
			//}

			$rating['votestextimg'] = 'VOTE';
			if ((int)$rating['votescountfile'] > 1) {
				$rating['votestextimg'] = 'VOTES';
			}
/*
			$o .= '<div style="float:left;"><strong>'
					. Text::_('COM_PHOCAGALLERY_RATING'). '</strong>: ' . $rating['votesaveragefile'] .' / '
					.$rating['votescountfile'] . ' ' . Text::_('COM_PHOCAGALLERY_'.$rating['votestextimg']). '&nbsp;&nbsp;</div>';
	*/
			if ($rating['alreadyratedfile']) {
				$o .= '<div class="pg-rate-box" title="'.$voteMsg.'" ><ul class="star-rating'.$smallO.'">'
						.'<li class="current-rating" style="width:'.$rating['voteswidthfile'].'px"></li>'
						.'<li><span class="star1"></span></li>';

				for ($i = 2;$i < 6;$i++) {
					$o .= '<li><span class="stars'.$i.'"></span></li>';
				}
				$o .= '</ul></div>';

				//$or ='<div class="pg-cv-vote-img-result" id="pg-cv-vote-img-result'.(int)$id.'" style="float:left;margin-left:5px">'.JText::_('COM_PHOCAGALLERY_ALREADY_RATE_IMG').'</div>';

			} else if ($rating['notregisteredfile']) {

				$o .= '<div class="pg-rate-box" title="'.Text::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_RATE_IMAGE').'"><ul class="star-rating'.$smallO.'">'
						.'<li class="current-rating" style="width:'.$rating['voteswidthfile'].'px"></li>'
						.'<li><span class="star1"></span></li>';

				for ($i = 2;$i < 6;$i++) {
					$o .= '<li><span class="stars'.$i.'"></span></li>';
				}
				$o .= '</ul></div>';

				//$or ='<div class="pg-cv-vote-img-result" id="pg-cv-vote-img-result'.(int)$id.'" style="float:left;margin-left:5px">'.JText::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_RATE_IMAGE').'</div>';

			} else {

				$o .= '<div class="pg-rate-box"><ul class="star-rating'.$smallO.'">'
						.'<li class="current-rating" style="width:'.$rating['voteswidthfile'].'px"></li>'
						.'<li><a href="'.$href.'" onclick="pgRating('.(int)$id.', 1, 1, \'pg-msnr-container\')" title="'. Text::sprintf('COM_PHOCAGALLERY_STAR_OUT_OF', 1, 5). '" class="star1">1</a></li>';

				for ($i = 2;$i < 6;$i++) {
					$o .= '<li><a href="'.$href.'" onclick="pgRating('.(int)$id.', '.$i.', 1, \'pg-msnr-container\')"  title="'. Text::sprintf('COM_PHOCAGALLERY_STARS_OUT_OF', $i, 5) .'" class="stars'.$i.'">'.$i.'</a></li>';
				}
				$o .= '</ul></div>';

				$or ='<div class="pg-rate-img-result" id="pg-rate-img-result'.(int)$id.'"></div>';
			}



		}

		if ($refresh == true) {
			return $o . '<div style="clear:both;"></div>';//we are in Ajax, return only content of pdvoting div
		} else {
			return '<div class="pg-rate-img" id="pg-rate-img'.(int)$id.'">'.$o.'</div>'.$or . '<div style="clear:both;"></div>';//not in ajax, return the content in div
		}


	}

	public static function renderRateImgJS($small = 1) {

		$document	 = Factory::getDocument();
		$url		  = Route::_('index.php?option=com_phocagallery&view=ratingimga&task=rate&format=json&'.Session::getFormToken().'=1', false);
		$urlRefresh		= Route::_('index.php?option=com_phocagallery&view=ratingimga&task=refreshrate&small='.$small.'&format=json&'.Session::getFormToken().'=1', false);
		$imgLoadingUrl = Uri::base(). 'media/com_phocagallery/images/loading.svg';
		$imgLoadingHTML = '<img src="'.$imgLoadingUrl.'" alt="" />';


		$js = '
		function pgRating(id, vote, m, container) {
		
			var result 			= "#pg-rate-img-result" + id;
			var resultvoting 	= "#pg-rate-img" + id;
			data = {"ratingId": id, "ratingVote": vote, "format":"json"};
			
			pgRequest = jQuery.ajax({
                type: "POST",
                url: "'.$url.'",
                async: "false",
                cache: "false",
                data: data,
                dataType:"JSON",
                
                beforeSend: function(){
                    jQuery(result).html("'.addslashes($imgLoadingHTML).'");
                    if (m == 2) {
                        //var wall = new Masonry(document.getElementById(container));
                    }
                },
                
                success: function(data){
                    if (data.status == 1){
                        jQuery(result).html(data.message);
                      
                        
                        
                        // Refresh vote
                        dataR = {"ratingId": id, "ratingVote": vote, "format":"json"};
                        
                        pgRequestRefresh = jQuery.ajax({
                            type: "POST",
                            url: "'.$urlRefresh.'",
                            async: "false",
                            cache: "false",
                            data: dataR,
                            dataType:"JSON",
                            
                            beforeSend: function(){
                                jQuery(resultvoting).html("'.addslashes($imgLoadingHTML).'");
                                if (m == 2) {
                                    //var wall = new Masonry(document.getElementById(container));
                                }
                            },
                            
                            success: function(dataR){
                                if (dataR.status == 1){
                                    jQuery(resultvoting).html(dataR.message);
                                } else if(dataR.status == 0){
                                    jQuery(resultvoting).html(dataR.error);
                                } else {
                                    jQuery(resultvoting).text("'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
                                }
                                
                                if (m == 2) {
                                    //var wall = new Masonry(document.getElementById(container));
                                }
                            },
                            
                            error: function(){
                                jQuery(resultvoting).text( "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
                            
                                if (m == 2) {
                                    //var wall = new Masonry(document.getElementById(container));
                                }
                            }
            
                        })
                        
   
                        
                    } else if(data.status == 0){
                        jQuery(result).html(data.error);
                    } else {
                        jQuery(result).text("'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
                    }
                    
                    if (m == 2) {
					    //var wall = new Masonry(document.getElementById(container));
				    }
                },
                
                error: function(){
                    jQuery(result).text( "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
				
				    if (m == 2) {
					    //var wall = new Masonry(document.getElementById(container));
				    }
                }

            })
        }';
		$document->addScriptDeclaration($js);



       /*

        $js .= '
		function pgRating(id, vote, m, container) {

			var result 			= "pg-cv-vote-img-result" + id;
			var resultvoting 	= "pg-cv-vote-img" + id;
			var pgRequest = new Request.JSON({
			url: "'.$url.'",
			method: "post",

			onRequest: function(){
				$(result).set("html", "'.addslashes($imgLoadingHTML).'");
				if (m == 2) {
					//var wall = new Masonry(document.getElementById(container));
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
						$(result).set("text", jsonObj.message);

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
									$(resultvoting).set("html", json2Obj.message);
								} else {
									$(resultvoting).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
								}

								if (m == 2) {
									//var wall = new Masonry(document.getElementById(container));
								}
							},

							onFailure: function() {
								$(resultvoting).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
								if (m == 2) {
									//var wall = new Masonry(document.getElementById(container));
								}
							}
						})

						pgRequestRefresh.send({
							data: {"ratingId": id, "ratingVote": vote, "format":"json"}
						});
						//End refreshing voting

					} else {
						$(result).set("html", r.error);
					}
				} else {
					$(result).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");
				}

				if (m == 2) {
					//var wall = new Masonry(document.getElementById(container));
				}
			},

			onFailure: function() {
				$(result).set("text", "'.Text::_('COM_PHOCAGALLERY_ERROR_REQUESTING_ITEM').'");

				if (m == 2) {
					//var wall = new Masonry(document.getElementById(container));
				}
			}

			})

			pgRequest.send({
				data: {"ratingId": id, "ratingVote": vote, "format":"json"},
			});

		};';

		//$js .= '});';

		$js .= "\n" . '//-->' . "\n" .'</script>';
		$document->addCustomTag($js);*/


	}
}
?>
