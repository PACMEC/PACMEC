jQuery(document).ready(function ($) {
// var tipButton = document.querySelector('.tip-button');
var tipButton = document.querySelectorAll('.tip-button');
// var messageEl = document.querySelectorAll('.message')
if(tipButton){
for (var i = 0; i < tipButton.length; i++) {
tipButton[i].addEventListener('click', function() {
  var user_account = $(this).data('metamask-address');
// console.log(user_account);



// Let's imagine you want to receive an ether tip
const yourAddress = user_account;
const value = '0xde0b6ba000005' // an ether has 18 decimals, here in hex.
const desiredNetwork = '1' // '1' is the Ethereum main network ID.

// Detect whether the current browser is ethereum-compatible,
// and handle the case where it isn't:
if (typeof window.ethereum === 'undefined' || typeof web3  === 'undefined') {
  alert('You need to install MetaMask extention to use this feature')

}

/*var user_address = web3.eth.accounts[0];
if (typeof user_address === 'undefined') {
  alert('You need to log in MetaMask to use this feature')
}*/


else {

  // In the case the user has MetaMask installed, you can easily
  // ask them to sign in and reveal their accounts:
  //  ethereum.autoRefreshOnNetworkChange = false;
  ethereum.request({ method: 'eth_requestAccounts' })

  // Remember to handle the case they reject the request:
  .catch(function (reason) {
    if (reason === 'User rejected provider access') {
      // The user didn't want to sign in!
    } else {
      // This shouldn't happen, so you might want to log this...
      alert('There was an issue signing you in.')
    }
  })

  // In the case they approve the log-in request, you'll receive their accounts:
  .then(function (accounts) {
    // You also should verify the user is on the correct network:
    if (ethereum.networkVersion !== desiredNetwork) {
      alert('This application requires the main network, please switch it in your MetaMask UI.')

      // We plan to provide an API to make this request in the near future.
      // https://github.com/MetaMask/metamask-extension/issues/3663
    }

    // Once you have a reference to user accounts,
    // you can suggest transactions and signatures:
 
    const account = accounts[0]
    sendEtherFrom(account, function (err, transaction) {
      if (err) {
        return alert(`Sorry you weren't able to contribute!`)
      }

      alert('Thanks for your successful contribution!')
    })

  })
}

function sendEtherFrom (account, callback) {

  // We're going to use the lowest-level API here, with simpler example links below
  const method = 'eth_sendTransaction'
  const parameters = [{
    from: account,
    to: yourAddress,
    value: value,
  }]
  const from = account

  // Now putting it all together into an RPC request:
  const payload = {
    method: method,
    params: parameters,
    from: from,
  }

  // Methods that require user authorization like this one will prompt a user interaction.
  // Other methods (like reading from the blockchain) may not.
  ethereum.sendAsync(payload, function (err, response) {
    const rejected = 'User denied transaction signature.'
    if (response.error && response.error.message.includes(rejected)) {
      return alert(`We can't take your money without your permission.`)
    }

    if (err) {
      return alert('There was an issue, please check your  meta address.')
    }

    if (response.result) {
      // If there is a response.result, the call was successful.
      // In the case of this method, it is a transaction hash.
      const txHash = response.result
      alert('Thank you for your generosity!')

      // You can poll the blockchain to see when this transaction has been mined:
      pollForCompletion(txHash, callback)
    }
  })
}

function pollForCompletion (txHash, callback) {
  let calledBack = false

  // Normal ethereum blocks are approximately every 15 seconds.
  // Here we'll poll every 2 seconds.
  const checkInterval = setInterval(function () {

    const notYet = 'response has no error or result'
    ethereum.sendAsync({
      method: 'eth_getTransactionByHash',
      params: [ txHash ],
    }, function (err, response) {
      if (calledBack) return
      if (err || response.error) {
        if (err.message.includes(notYet)) {
          return 'transaction is not yet mined'
        }

        callback(err || response.error)
      }

      // We have successfully verified the mined transaction.
      // Mind you, we should do this server side, with our own blockchain connection.
      // Client side we are trusting the user's connection to the blockchain.
      const transaction = response.result
      clearInterval(checkInterval)
      calledBack = true
      callback(null, transaction)
    })
  }, 2000)
}




});
}}
});


jQuery(document).ready(function($){
      new ClipboardJS('.CDBBC_btn');

      $('ul.cbd-tabs li').click(function(){
      var tab_id = $(this).attr('data-tab');
      $('ul.cbd-tabs li').removeClass('current');
      $('.dbd-tabs-content').removeClass('current');
      $(this).addClass('current');
      $('#'+tab_id).addClass('current');
      })
}) 