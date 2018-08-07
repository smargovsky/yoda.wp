/**
 * Repeaters and iFramed in Dialogue
 */
(function( $ ) {
	'use strict';

	/**
	 * Clones the hidden field to add another repeater.
	 */
	$('#add-repeater').on( 'click', function( e ) {

		e.preventDefault();

		var clone = $('.repeater.hidden').clone(true);

		clone.removeClass('hidden');
		clone.insertBefore('.repeater.hidden');

		return false;

	});

	/**
	 * Removes the selected repeater.
	 */
	$('.link-remove').on('click', function() {

		var parents = $(this).parents('.repeater');

		if ( ! parents.hasClass( 'first' ) ) {

			parents.remove();

		}

		return false;

	});

	/**
	 * Shows/hides the selected repeater.
	 */
	$( '.btn-edit' ).on( 'click', function() {

		var repeater = $(this).parents( '.repeater' );

		repeater.children( '.repeater-content' ).slideToggle( '150' );
		$(this).children( '.toggle-arrow' ).toggleClass( 'closed' );
		$(this).parents( '.handle' ).toggleClass( 'closed' );

	});

	/**
	 * Changes the title of the repeater header as you type
	 */
	$(function(){

		$( '.repeater-title' ).on( 'keyup', function(){

			var repeater = $(this).parents( '.repeater' );
			var fieldval = $(this).val();

			if ( fieldval.length > 0 ) {

				repeater.find( '.title-repeater' ).text( fieldval );

			} else {

				repeater.find( '.title-repeater' ).text( nhdata.repeatertitle );

			}

		});

	});

	/**
	 * Makes the repeaters sortable.
	 */
	$(function() {

		$( '.repeaters' ).sortable({
			cursor: 'move',
			handle: '.handle',
			items: '.repeater',
			opacity: 0.6,
		});
	});


  var selectorInput = $('#announcement-selector');
  var urlInput = $('#announcement-url');
  /**
   * Dialogue for Iframe!
   */

  $('#dialog-for-iframe').dialog({
    title: 'Select Page / Element',
    dialogClass: 'wp-dialog',
    autoOpen: false,
    draggable: false,
    width: 'auto',
    modal: true,
    resizable: false,
    closeOnEscape: true,
    position: {
      my: "center",
      at: "center",
      of: window
    },
    buttons: [
      {
        id: 'url-selection-mode',
        text: 'Use Current Page',
        click: function() {
          // send PM to iFrame - yodaMessage: 'select-mode'
          var message = { 'yodaMessage': 'url-mode' };
          $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');
        }
      },
      {
        id: 'selection-mode',
        text: 'Select Element',
        click: function() {
          // send PM to iFrame - yodaMessage: 'select-mode'
          var message = { 'yodaMessage': 'select-mode' };
          $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');
        }
      },
      {
        id: 'save-selection',
        text: 'Use Selected Element',
        click: function() {
          $('#dialog-for-iframe').dialog('close');
        }
      }
    ],
    open: function () {
      // Buttons based on type
      if ($('#announcement-type').val() === 'toast') {
        $('#selection-mode').hide();
        $('#save-selection').hide();        
      } else {
        $('#url-selection-mode').hide();
        $('#selection-mode').prop("disabled", true).addClass("ui-state-disabled");
      }
      // close dialog by clicking the overlay behind it
      $('.ui-widget-overlay').bind('click', function(){
        $('#dialog-for-iframe').dialog('close');
      });

      // get URL for iFrame and set it
      $('#iframe-for-element-selection').attr('src', $('#url-for-iframe').val());

      // tell Yoda to LIVE
      var message = { 'yodaMessage': 'init' };
      $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');

      // env change selection
      $('#url-for-iframe').change(function(e) {
        $('#iframe-for-element-selection').attr('src', $( this ).val());
        $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');
      });

      // wait for PostMessage from iFrame - yodaMessage: 'iframe-ready'
      let handle = function(event) {
        var { source, data } = event.originalEvent;
        if ( data.yodaMessage ) {
          if ( data.yodaMessage === 'iframe-ready' ) {
            // clear any pops
            var message = { 'yodaMessage': 'clear-pops' };
            $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');

            $('#selection-mode').prop("disabled", false).removeClass("ui-state-disabled");
            $('#save-selection').prop("disabled", true).addClass("ui-state-disabled");
          } else if ( data.yodaMessage === 'return-selector' ) {
            $('#save-selection').prop("disabled", false).removeClass("ui-state-disabled");

            // get correct element for repeater
            selectorInput.val( data.yodaMessageSelector );
            urlInput.val( data.yodaMessageUrl );
          } else if ( data.yodaMessage === 'return-url' ) {
            urlInput.val( data.yodaMessageUrl );
            $('#dialog-for-iframe').dialog('close');
          }
        }
      }
      $(window).on("message", handle);
    },
    create: function () {
      // style fix for WordPress admin
      $('.ui-dialog-titlebar-close').addClass('ui-button');
    },
    close: function( event, ui ) {
      // nothing, everything already set!
      $(window).off('message');
    }
  });
  // bind a button  to open the dialog
  $('#open-iframe').click(function(e) {
    e.preventDefault(); //KEY b/c wp thinks this is a save button
    
    $('#dialog-for-iframe').dialog('open');
  });


})( jQuery );
