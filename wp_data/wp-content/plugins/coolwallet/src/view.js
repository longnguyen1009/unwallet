import domReady from '@wordpress/dom-ready';
import { render } from "@wordpress/element";
import WebBleTransport from "@coolwallet/transport-web-ble";
import * as core from "@coolwallet/core";

const App = function () {
	async function loginClick() {
		WebBleTransport.listen(async (error, device) => {
			const cardName = device.name;
			const transport = await WebBleTransport.connect(device);
			const SEPublicKey = await core.config.getSEPublicKey(transport)
			this.setState({ transport, cardName, SEPublicKey });
			localStorage.setItem('cardName', cardName)
			localStorage.setItem('SEPublicKey', SEPublicKey)
		  });
	}

    return (
		<div id="cw-login">
		  <button type="button" className="button" onClick={loginClick}>
			  coolWalletでログイン
		  </button>
		</div>
    );
}

domReady( function () {
    //do something after DOM loads.
    const container = document.querySelector("#coolwallet");
    render(<App />, container);
} );
