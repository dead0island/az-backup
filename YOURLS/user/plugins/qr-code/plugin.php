<?php
/*
Plugin Name: QR Code Short URLS
Plugin URI: https://yourls.org/
Description: Add .qr to shorturls to display QR Code
Version: 1.1
Author: Ozh
Author URI: https://ozh.org/
*/

// Kick in if the loader does not recognize a valid pattern
yourls_add_action('redirect_keyword_not_found', 'ozh_yourls_qrcode');

function ozh_yourls_qrcode( $request ) {
        // Get authorized charset in keywords and make a regexp pattern
        $pattern = yourls_make_regexp_pattern( yourls_get_shorturl_charset() );

        // Shorturl is like bleh.qr?
        if( preg_match( "@^([$pattern]+)\.qr?/?$@", $request[0], $matches ) ) {
                // this shorturl exists?
                $keyword = yourls_sanitize_keyword( $matches[1] );
                if( yourls_is_shorturl( $keyword ) ) {
                        // Show the QR code then!
                        header('Location: https://cli.im/api/qrcode/code?text='.YOURLS_SITE.'/'.$keyword.'&mhid=5ECVDlu/ksIhMHYoLNNQPqI');
                        exit;
                }
        }
}
