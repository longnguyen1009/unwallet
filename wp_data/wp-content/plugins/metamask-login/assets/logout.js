// Account changed
let currentAccount = null;

window.ethereum.request({ method: 'eth_accounts' })
    .then(function (addresses) {
        currentAccount = addresses[0];
    })
    .catch((err) => {
        console.error(err);
    });

// Note that this event is emitted on page load.
// If the array of accounts is non-empty, you're already
// connected.
window.ethereum.on('accountsChanged', handleAccountsChanged);

// eth_accounts always returns an array.
function handleAccountsChanged(accounts) {
    if (accounts.length === 0) {
        logout();
        alert('Metamask is lock');
    } else if (accounts[0] !== currentAccount) {
        currentAccount = accounts[0];
        logout();
    }

    // logout();
}

async function logout() {
    await jQuery.ajax({
        method: 'POST',
        url: metamask_logout.ajaxurl,
        data: {
            action: 'logout_metamask',
        },
        success: function( response ) {
            window.location.href = location.protocol + '//' + location.host;
        }
    });
}
