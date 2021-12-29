document.addEventListener("DOMContentLoaded", function() {
  // Countdown
  setTimeout(function(){ 

    var countDown = document.getElementById('wpsc-nft-time-countdown');
    if (countDown) {
      var countDownValue = document.getElementById('wpsc-nft-time-countdown').value;
    }

    //New countdown
    if (countDownValue) {
      var finaleDate = countDownValue;

      var timer = () =>{
          var now = new Date().getTime();
          var diff = finaleDate - now;
          var endedAuction = document.querySelectorAll('.ended-auction');
          var activeAuction = document.querySelectorAll('.active-auction');

          if(diff < 0){
              for (var index = 0; index < endedAuction.length; index++) {
                endedAuction[index].style.display = 'flex';                
              }

              for (var i = 0; i < activeAuction.length; i++) {
                activeAuction[i].style.display = 'none';                
              }
          }else {
              for (var index = 0; index < endedAuction.length; index++) {
                endedAuction[index].style.display = 'none';                
              }

              for (var i = 0; i < activeAuction.length; i++) {
                activeAuction[i].style.display = 'flex';                
              }
          }
          
          var days = Math.floor(diff / (1000*60*60*24));
          var hours = Math.floor(diff % (1000*60*60*24) / (1000*60*60));
          var minutes = Math.floor(diff % (1000*60*60)/ (1000*60));
          var seconds = Math.floor(diff % (1000*60) / 1000);
        // Adding the zeros.
          days <= 99 ? days = `${days}` : days;
          days <= 9 ? days = `00${days}` : days;
          hours <= 9 ? hours = `0${hours}` : hours;
          minutes <= 9 ? minutes = `0${minutes}` : minutes;
          seconds <= 9 ? seconds = `0${seconds}` : seconds;   

          if (document.querySelector('#days')) {
            document.querySelector('#days').textContent = days;
          }
          if (document.querySelector('#hours')) {
            document.querySelector('#hours').textContent = hours;
          }
          if (document.querySelector('#minutes')) {
            document.querySelector('#minutes').textContent = minutes;
          }
          if (document.querySelector('#seconds')) {
            document.querySelector('#seconds').textContent = seconds;
          }

      }
      timer();
      // Calling the function every 1000 milliseconds.
      setInterval(timer,1000);
    }


    // *** getClosestParent ***
    // Use this function to get the closest parent element with a matching selector
    var getClosestParent = function (elem, selector) {

      // Element.matches() polyfill
      if (!Element.prototype.matches) {
        Element.prototype.matches =
          Element.prototype.matchesSelector ||
          Element.prototype.mozMatchesSelector ||
          Element.prototype.msMatchesSelector ||
          Element.prototype.oMatchesSelector ||
          Element.prototype.webkitMatchesSelector ||
          function (s) {
            var matches = (this.document || this.ownerDocument).querySelectorAll(s),
              i = matches.length;
            while (--i >= 0 && matches.item(i) !== this) { }
            return i > -1;
          };
      }

      // Get the closest matching element
      for (; elem && elem !== document; elem = elem.parentNode) {
        if (elem.matches(selector)) return elem;
      }
      return null;

    };

    //Modal

    jQuery(document).on('click', '.tile', function($) {
      var modal = this.nextElementSibling;
      modal.style.display = "flex";
    })

    jQuery(document).on('click', '.close-list-modal', function($) {
      var modalParent = getClosestParent(this,'.modal-list');
      modalParent.style.display = "none";
    })
  

  }, 4000);


});