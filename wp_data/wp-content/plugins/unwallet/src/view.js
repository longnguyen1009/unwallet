import domReady from '@wordpress/dom-ready';
import { render } from "@wordpress/element";

const App = () => <div>REACT!</div>

domReady( function () {
    //do something after DOM loads.
    const container = document.querySelector("#App");
    render(<App />, container);
} );