( function( $ ){

  $('#osom-form').on( 'submit', function(){
    event.preventDefault();
    var osom_data = jQuery( this ).serializeArray();
     osom_data.push( { "name" : "_ajax_nonce", "value" : osom_globals.nonce } );
    $.ajax({
      type : 'post',
      url : osom_globals.ajax_url,
      data: osom_data,
      success: function( response ) {
        response = JSON.parse(response);
         if( response.status == true ) {
            jQuery('#osom-form')[0].reset();
         }
         else {
            alert( 'Something went wrong!' );
         }
      }
    });
  } );

} )( jQuery );
