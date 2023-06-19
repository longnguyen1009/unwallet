import domReady from '@wordpress/dom-ready';
import { render } from "@wordpress/element";
import { UnWallet } from "unwallet-client-sdk";

const nonce = "123456789";

const App = function () {
    async function loginClick() {
        const unWallet = await UnWallet.init({
            clientID: "133980164233664",
            env: "dev", // ※ testnet 版の場合のみ指定
        });

        await unWallet.authorize({
            redirectURL: "http://localhost:8000/sample-page/",
            nonce: nonce,
        });
    }

    return (
      <button onClick={loginClick}>
        Click me!
      </button>
    );
}

domReady( function () {
    //do something after DOM loads.
    const container = document.querySelector("#App");
    render(<App />, container);
} );