
function displayMedia() {

   var type = jQuery("#wpsc-media-type").val();

   const json = jQuery("#wpsc-nft-media-json").val();

   if (!json) return;

   const selection = JSON.parse(json);

   var str_html = ""

   if (type=="video") str_html += '<video controls>';
   if (type=="audio") str_html += '<audio controls>';

   var valid_mime = false;

   selection.map( function( attachment ) {
      if (attachment.mime.indexOf(type) !== -1 || (type=="document" && attachment.mime.indexOf("application") !== -1)) {
         if (type=="image") str_html += "<img class=\"ui medium rounded centered bordered image\" src=\""+attachment.url+"\">";
         if (type=="document") {
            if (attachment.mime=="application/pdf") {
               str_html += "<div class=\"wpsc-pdf-iframe-container\"><iframe src=\""+attachment.url+"\"></iframe></div>";
            } else {
               str_html += "<strong>"+attachment.url.substring(attachment.url.lastIndexOf('/')+1)+"</strong><hr/><a class=\"ui medium button\" href=\""+attachment.url+"\" target=\"_blank\">Download</a>";
            }
         }
         else str_html += "<source src=\""+attachment.url+"\" type=\""+attachment.mime+"\"></video>";
         valid_mime = true;
      }
   });

   if (!valid_mime) {
      str_html = '';
   } else {
      if (type=="video") str_html += "</video>";
      if (type=="audio") str_html += "</audio>";   
   }

   str_html = '<div class="image">' + str_html + '</div>';

   jQuery("#wpsc-nft-media-preview-media-real").html(str_html);

}

jQuery(document).ready(function() {

   displayMedia();

   jQuery(".wpsc-open-qr-scanner").click(function(event) {
      event.preventDefault();
      var x = window.open(qr_scanner_page + "?input=" + jQuery(this).attr('data-input-name'), 
        "QR Scanner", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=460,top=100,left=200");
   });  

   jQuery(document).on('click', '#save-nft-item', function(event) {

      jQuery("#save-nft-item").prop('disabled', true);

      event.preventDefault();

      jQuery.ajax( {
         url: wpApiSettings.root + 'wpsc/v1/insert-nft/' + jQuery("#wpsc-collection-id").val(),
         method: 'POST',
         beforeSend: function ( xhr ) {
               xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
         },
         data:{
            'nft_id' : jQuery("#wpsc-nft-id").val(),
            'collection_id' : jQuery("#wpsc-collection-id").val(),
            'title' : jQuery("#wpsc-title").val(),
            'description' : jQuery("#wpsc-description").val(),
            'owner' : jQuery("#wpsc-nft-owner").val(),
            'media' : jQuery("#wpsc-nft-media-json").val(),
            'tags' : jQuery("#wpsc-tags").val(),
            'categories' : jQuery("#wpsc-categories").val(),
            'media_type' : jQuery("#wpsc-media-type").val()
         }
      } ).done( function ( response ) {
         if (!isNaN(response)) {
            jQuery("#wpsc-result-message-container").addClass('info').removeClass('warning').fadeIn(500);
            jQuery("#wpsc-result-message").html("Success!");
            jQuery("#wpsc-nft-id").val(response);   
         } else {
            jQuery("#wpsc-result-message-container").addClass('warning').removeClass('info').fadeIn(500);
            jQuery("#wpsc-result-message").html(response);
         }
         setTimeout(function(){ jQuery("#wpsc-result-message-container").fadeOut(500); }, 6000);
         jQuery("#save-nft-item").prop('disabled', false);
      } );

   });

   var custom_file_frame;
   jQuery(document).on('click', '#nft-add-media', function(event) {

      event.preventDefault();

      var type = jQuery("#wpsc-media-type").val();

      if (type=="image" || type=="video" || type=="audio" || type=="document") {

         var multiple = true;
         var type_browse = type;
         if (type=="image" || type=="document") {
            multiple = false;
            if (type=="document") {
               type_browse = "application";
            }
         }

         $this = jQuery(this);
         if (typeof(custom_file_frame)!=="undefined") {
            custom_file_frame.close();
         }
         custom_file_frame = wp.media.frames.customHeader = wp.media({
            title: "Select Media",
            button: {
               text: "Use Media"
            },
            multiple: multiple,
            library: {
               type: type_browse
            },
         });

         custom_file_frame.on('select', function() {
            var selection = custom_file_frame.state().get('selection').toJSON();
            var the_json = [];
            for (let index = 0; index < selection.length; index++) {
               the_json.push({id: selection[index].id, url: selection[index].url, url: selection[index].url, mime: selection[index].mime});
            }
            jQuery("#wpsc-nft-media-json").val(JSON.stringify(the_json));
            displayMedia();
         });
         custom_file_frame.open();
   
      } else {
         alert("Choose media type");
      }

   });

});


 jQuery(document).ready(function($){
   displayMedia();
   var custom_uploader;
   $('#upload_image_button').click(function(e) {
     e.preventDefault();
     //If the uploader object has already been created, reopen the dialog
     if (custom_uploader) {
       custom_uploader.open();
       return;
     }
     //Extend the wp.media object
     custom_uploader = wp.media.frames.file_frame = wp.media({
       title: 'Choose Image',
       button: {
         text: 'Choose Image'
       },
       multiple: true
     });
     custom_uploader.on('select', function() {
     });
     custom_uploader.open();
   });
 });