import domReady from '@wordpress/dom-ready';
import { render } from "@wordpress/element";
import { UnWallet } from "unwallet-client-sdk";
const randomBytes = require('randombytes');
const nonce = randomBytes(16).toString("hex");

const App = function () {
    async function loginClick() {
		// let's remove login errors if any
		const loginError = document.getElementById( 'login_error' )
		if( loginError ) {
			loginError.remove()
			document.loginform.classList.remove( 'shake' )
		}

		const unWallet = await UnWallet.init({
            clientID: "130304747316987", // APP iD
            env: "dev", // ※ testnet 版の場合のみ指定
        });

		await unWallet.authorize({
			responseMode: 'form_post',
			redirectURL: 'https://wordpress-test.com/login',
			nonce: nonce,
		});
    }

    return (
		<div id="uw-login">
		  <button type="button" className="button" onClick={loginClick}>
			  unWalletでログイン
		  </button>
		</div>
    );
}

domReady( function () {
    //do something after DOM loads.
    const container = document.querySelector("#App");
    render(<App />, container);
} );
