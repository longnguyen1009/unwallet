const loginButton = document.querySelector( '#mm-login a' )

// click on button
loginButton.addEventListener( 'click', async function ( event ) {
    event.preventDefault()
    if ('undefined' !== typeof window.ethereum) {
        metaMaskSignIn() // we are going to talk about it in a while
    } else {
        alert('Metamaskがインストールされていません。Metamaskのブラウザ拡張機能をインストールして、Metamaskウォレットに接続してください。');
    }
} )
async function metaMaskSignIn() {

    // let's remove login errors if any
    const loginError = document.getElementById( 'login_error' )
    if( loginError ) {
        loginError.remove()
        document.loginform.classList.remove( 'shake' )
    }

    // connect to wallet and request etherium address
    let address = ( await ethereum.request({ method: 'eth_requestAccounts' }) )[0];

    const message = 'Wallet Address:' + address;

    // signing the signature
    const sign = await ethereum.request({
        method: 'personal_sign',
        params: [
            // `0x${buffer.Buffer.from(metamask_login.message, 'utf8').toString('hex')}`,
            // metamask_login.message,
            message,
            address
        ],
    });

    // sending AJAX request to WordPress
    await jQuery.ajax({
        method: 'POST',
        url: metamask_login.ajaxurl,
        data: {
            action: 'metamasklogin',
            address: address,
            message: message,
            signature: sign
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
}
