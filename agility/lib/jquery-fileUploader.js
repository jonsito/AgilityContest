/**
 * Original code from
 * https://deliciousbrains.com/using-javascript-file-api-to-avoid-file-upload-limits/
 *
 * Heavily modified by jonsito@gmail.com to become a jquery function instead of wordpress plugin
 */

(function( $ ) {

    var reader = {};
    var file = {};
    var slice_size = 1000 * 1024; // 1Mbyte chunk

    var config = {
        selector: "", // ID of "file" input field
        button: "",   // ID of submit button
        progress: "", // ID or function to send upload progress info to
        onSuccess: function() { return true; },   // callback function to invoke on success
        onError: function(msg) { return true; },     // callback function to invoke on error
        onCancel: function() { return true; }     // callback function to invoke on cancel
    };

    var methods = {
        init: function(options) {
            $.extend(config,options);
        },
        options: function() {
            return config;
        },
        upload: function() {
            reader = new FileReader();
            file = document.querySelector( config.selector ).files[0];
            upload_file( 0 ); // call to uploader method on first chunk
        },
        cancel: function() {
            cancel_upload();
        }
    };

    function log_progress(msg) {
        console.log(msg);
        if ( config.progress===null) return false;
        if ( config.progress==="") return false;

        // if progress is function, fire it
        if ( typeof (config.progress) === 'function') {
            setTimeout(function() {config.progress(msg)},0);
        }
        // else use as elementID and set msg to be inner data
        else $(config.progress).html(msg);
        return true;
    }

    function cancel_upload() {
        log_progress( 'Upload Cancelled!' );
        if (typeof (config.onCancel) === "function" ) {
            setTimeout( function() {config.onCancel(); },0);
        }
    }

    function upload_file( start ) {
        var next_slice = start + slice_size + 1;
        var blob = file.slice( start, next_slice );

        reader.onloadend = function( event ) {
            if ( event.target.readyState !== FileReader.DONE ) {
                return;
            }

            $.ajax( {
                url: '../ajax/fileFunctions.php',
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    Operation: 'upload',
                    Data: event.target.result,
                    File: file.name,
                    Type: file.type,
                    Chunk: start
                },
                error: function( XMLHttpRequest, textStatus, errorThrown ) {
                    // internal ajax call: do not call onError
                    alert("fireUpdater Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown);
                },
                success: function( data ) {
                    if (data.errorMsg) {
                        // server side error received. notify user
                        log_progress( 'Upload Error: ' + data.errorMsg );
                        if (typeof (config.onError) === "function" ) {
                            setTimeout( function() {config.onError(data.errorMsg); },0);
                        }
                        return false;
                    }
                    if ( next_slice < file.size ) {
                        var size_done = start + slice_size;
                        var percent_done = Math.floor( ( size_done / file.size ) * 100 );
                        // log result
                        log_progress( 'Uploading File - ' + percent_done + '%' );
                        // More to upload, call function recursively
                        upload_file( next_slice );
                    } else {
                        // Update upload progress
                        log_progress( 'Upload Complete!' );
                        if (typeof (config.onSuccess) === "function" ) {
                            setTimeout( config.onSuccess ,0);
                        }
                    }
                }
            } );
        };

        reader.readAsDataURL( blob );
    }


    $.fn.fileUploader = function( args ){
        if ( methods[args]){
            // vemos si lo hemos invocado con un metodo como parametro
            return methods[ args ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( (typeof args === 'object' ) || (!args) ) {
            // init con argumentos de configuracion
            methods.init.apply( this, arguments );
            // on each found element declared as fileUploader trigger upload on fire button when declared
            return this.each(function(){
                if ( (config.button!==null) && (config.button!=="") ) {
                    $(config.button).on('click',methods.upload);
                }
            });
        } else {
            // error
            $.error( 'Method ' +  args + ' does not exist on jQuery.fileUpload' );
        }
    };


})( jQuery );