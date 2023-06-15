// import { UnWallet } from "@wordpress/unwallet-client-sdk";
// import crypto from "crypto-js";
// var CryptoJS = require("crypto-js");
// var crypto = window.crypto-js;
// console.log(crypto.randomBytes(16).toString("hex"));
// console.log(CryptoJS.HmacSHA1("Message", "Key"));
// const UnWallet = require('unwallet-client-sdk');
// import UnWallet from "../../../../../wp_source/node_modules/unwallet-client-sdk/dist/";
import UnWallet from "./node_modules/unwallet-client-sdk/dist/unwallet.js";

// import UnWallet from "https://unpkg.com/unwallet-client-sdk@0.6.1/dist/unwallet.js"; //from external source
const unWalletLoginButton = document.querySelector( '#uw-login a' );

// click on button
unWalletLoginButton.addEventListener( 'click', async function ( event ) {
    event.preventDefault();
    console.log('12321');
    unWalletSignIn();
});

async function unWalletSignIn() {
    // let's remove login errors if any
    const loginError = document.getElementById( 'login_error' )
    if( loginError ) {
        loginError.remove()
        document.loginform.classList.remove( 'shake' )
    }

    // const nonce = crypto.randomBytes(16).toString("hex");
    const nonce = '34f650896b1416de0ccdd53ce375a8e3';

    const unWallet = await UnWallet.init({
        clientID: "130304747316987",
        env: "dev", // ※ testnet 版の場合のみ指定
    });

    // If you want to receive the authentication token as a URL fragment
    // unWallet.authorize({
    //     redirectURL: "http://wordpress-test.com/callback",
    //     nonce: nonce,
    // });

    // If you want to receive the authentication token as a POST parameter
    // unWallet.authorize({
    //     responseMode: "form_post",
    //     redirectURL: "<REDIRECT_URL>",
    //     nonce: "<NONCE>",
    // });

    // const idToken      = "<ID_TOKEN>";
    // const idTokenParts = idToken.split(".");
    // const idTokenClaim = JSON.parse((Buffer.from(idTokenParts[1], "base64")).toString());
    // const address      = idTokenClaim.sub;
    const address      = '0x401d0C6D1e49D7D84695a66302A3930A974A5c24';

    // sending AJAX request to WordPress
    /*
    await jQuery.ajax({
        method: 'POST',
        url: metamask_login.ajaxurl,
        data: {
            action: 'unwalletlogin',
            address: address,
        },
        success: function( response ) {
            if( true === response.success ) {
                // show success message
                document.loginform.insertAdjacentHTML( 'beforebegin', '<div class="success"><strong>Success:</strong> Redirecting...</div>' );
                // redirect to subscriber page
                if (response.data.user_meta.redirect_url[0]) {
                    window.location.href = location.protocol + '//' + location.host + '/' + response.data.user_meta.redirect_url[0];
                } else {
                    window.location.href = location.protocol + '//' + location.host;
                }
                // window.location.href = location.protocol + '//' + location.host + '/sample-page/';
            } else {
                // shake the form and show  error message
                document.loginform.classList.add( 'shake' );
                document.loginform.insertAdjacentHTML( 'beforebegin', '<div id="login_error"><strong>Error:</strong> ' + response.data[0].message + '</div>' );
            }
        }
    });
    */
}

