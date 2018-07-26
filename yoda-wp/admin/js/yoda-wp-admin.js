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

  /**
   * Dialogue for Iframe!
   */
  var selectorInput = null;
  $('#dialog-for-iframe').dialog({
    title: 'Select Page Element',
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
      // close dialog by clicking the overlay behind it
      $('.ui-widget-overlay').bind('click', function(){
        $('#dialog-for-iframe').dialog('close');
      });
      // disable selection button
      $('#selection-mode').prop("disabled", true).addClass("ui-state-disabled");

      // get URL for iFrame and set it
      $('#iframe-for-element-selection').attr('src', $('#url-for-iframe').val());

      // tell Yoda to LIVE
      var message = { 'yodaMessage': 'init' };
      $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');

      // wait for PostMessage from iFrame - yodaMessage: 'iframe-ready'
      let handle = function(event) {
        console.log(' ********* POST MESSAGE EVENT', event);
        var { source, data } = event.originalEvent;
        if ( data.yodaMessage ) {
          if ( data.yodaMessage === 'iframe-ready' ) {
            console.log(' ********* POST MESSAGE!!!!!', data);

            // clear any pops
            var message = { 'yodaMessage': 'clear-pops' };
            $('#iframe-for-element-selection')[0].contentWindow.postMessage(message, '*');

            $('#selection-mode').prop("disabled", false).removeClass("ui-state-disabled");
            $('#save-selection').prop("disabled", true).addClass("ui-state-disabled");
          } else if ( data.yodaMessage === 'return-selector' ) {
            console.log(' ********* POST MESSAGE!!!!!', data);
            $('#save-selection').prop("disabled", false).removeClass("ui-state-disabled");

            // get correct element for repeater
            $(selectorInput).val( data.yodaMessageSelector );
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
  // bind a button or a link to open the dialog
  $('.element-selection-mode').click(function(e) {
    e.preventDefault();
    selectorInput = $(e.target).siblings('input')[0];
    console.log(' ********* TARGET >>>>>>>>>> ', selectorInput);
    $('#dialog-for-iframe').dialog('open');
  });


})( jQuery );
