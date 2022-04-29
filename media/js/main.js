/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @extension Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

document.addEventListener("DOMContentLoaded", () => {



	/*var anchors = document.querySelectorAll('.pg-modal-button');
	for (var i = 0, length = anchors.length; i < length; i++) {
	var anchor = anchors[i];
	anchor.addEventListener('click', event => {
		// `this` refers to the anchor tag that's been clicked
		event.preventDefault();
		console.log(this.getAttribute('href'));
	}, true);
	};*/

	// Events
	document.querySelectorAll('.pg-bs-modal-button').forEach(item => {


		item.addEventListener('click', function(event) {

			event.preventDefault();
			let href = this.getAttribute('href');
			let title = this.getAttribute('data-img-title');


			let modalItem = document.getElementById('pgCategoryModal')
			let modalIframe = document.getElementById('pgCategoryModalIframe');
			let modalTitle	= document.getElementById('pgCategoryModalLabel');

			modalIframe.src = href;
			modalTitle.innerHTML = title;

			//let modal = document.getElementById('phCategoryModal')

			/*modal.addEventListener('shown.bs.modal', function () {
			myInput.focus()
			})*/
			//console.log(href);
			let modal = new bootstrap.Modal(modalItem);
			modal.show();

		})
	})

	/* Events */
	/*document.getElementById("filterOptionsClear").addEventListener("click", (event) => {
		document.getElementById("filterOptionsInput").value = "";
		filterOptions("");
	})

	document.getElementById("filterOptionsInput").addEventListener("input", (event) => {
		let eV = event.currentTarget.value;
		filterOptions(eV);
	});*/
});

/*
* Change dynamically title in Bootstrap modal header
 */

function pgFrameOnLoad(){

	let iframe = document.getElementById("pgCategoryModalIframe");
	let titleEl = iframe.contentWindow.document.getElementById('pgDetailTitle');
	if (titleEl) {
		let title = titleEl.getAttribute('data-title');
		let modalTitle	= document.getElementById('pgCategoryModalLabel');
		modalTitle.innerHTML = title;
	}

}


function pgCountChars(maxCount) {

	var phLang		= Joomla.getOptions('phLangPG');
	var pfc 		= document.getElementById('phocagallery-comments-form');
	var charIn		= pfc.phocagallerycommentseditor.value.length;
	var charLeft	= maxCount - charIn;

	if (charLeft < 0) {
		alert(phLang['COM_PHOCAGALLERY_MAX_LIMIT_CHARS_REACHED']);
		pfc.phocagallerycommentseditor.value = pfc.phocagallerycommentseditor.value.substring(0, maxCount);
		charIn	 = maxCount;
		charLeft = 0;
	}
	pfc.phocagallerycommentscountin.value	= charIn;
	pfc.phocagallerycommentscountleft.value	= charLeft;
}

function pgCheckCommentsForm() {

	var phLang	= Joomla.getOptions('phLangPG');
	var pfc = document.getElementById('phocagallery-comments-form');
	if ( pfc.phocagallerycommentstitle.value == '' ) {
		alert(phLang['COM_PHOCAGALLERY_ENTER_TITLE']);
		return false;
	} else if ( pfc.phocagallerycommentseditor.value == '' ) {
		alert(phLang['COM_PHOCAGALLERY_ENTER_COMMENT']);
		return false;
	} else {
		return true;
	}
}

function pgPasteTag(tag, closingTag, prependText, appendText) {
	var pe 			= document.getElementById( 'phocagallery-comments-editor' );
	var startTag 	= '[' + tag + ']';
	var endTag 		= '[/' + tag + ']';

	if (typeof pe.selectionStart != 'undefined') {
		var tagText = pe.value.substring(pe.selectionStart, pe.selectionEnd);
	} else if (typeof document.selection != 'undefined') {
		var tagText = document.selection.createRange().text;
	} else {
	}

	if (typeof closingTag == 'undefined') {
		var closingTag	= true;
	}
	if (typeof prependText == 'undefined') {
		var prependText	= '';
	}
	if (typeof appendText == 'undefined') {
		var appendText	= '';
	}
	if (!closingTag) {
		endTag 			= '';
	}
	var totalText 		= prependText + startTag + tagText + endTag + appendText;
	pe.focus();

	if (typeof pe.selectionStart != 'undefined') {
		var start	= pe.selectionStart;
		var end 	= pe.selectionEnd;
		pe.value 	= pe.value.substr(0, start) + totalText + pe.value.substr(end);

		if (typeof selectionStart != 'undefined' && typeof selectionEnd != 'undefined') {
			pe.selectionStart 	= start + selectionStart;
			pe.selectionEnd 	= start + selectionEnd;
		} else {
			if (tagText == '') {
				pe.selectionStart 	= start + prependText.length + startTag.length;
				pe.selectionEnd 	= start + prependText.length + startTag.length;
			} else {
				pe.selectionStart 	= start + totalText.length;
				pe.selectionEnd 	= start + totalText.length;
			}
		}
	} else if (typeof document.selection != 'undefined') {
		var range 	= document.selection.createRange();
		range.text 	= totalText;

		if (typeof selectionStart != 'undefined' && typeof selectionEnd != 'undefined') {
			range.moveStart('character', -totalText.length + selectionStart);
			range.moveEnd('character', -totalText.length + selectionEnd);
		} else {
			if (tagText == '') {
				range.move('character', -(endTag.length + appendText.length));
			} else {
			}
		}
		range.select();
	}
	pgCountChars();
	delete selectionStart;
	delete selectionEnd;
}

function pgPasteSmiley( smiley ) {
	var pe = document.getElementById( 'phocagallery-comments-editor' );
	if ( typeof pe.selectionStart != 'undefined' ) {
		var start	= pe.selectionStart;
		var end 	= pe.selectionEnd;
		pe.value 	= pe.value.substring( 0, start ) + smiley + pe.value.substring( end );

		newPosition	= start + smiley.length;

		pe.selectionStart	= newPosition;
		pe.selectionEnd		= newPosition;

	} else if (typeof document.selection != 'undefined') {
		pe.focus();
		range = document.selection.createRange();
		range.text = smiley;
	} else {
		pe.value += smiley;
	}
	pgCountChars();
	pe.focus();
}
