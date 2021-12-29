document.addEventListener("DOMContentLoaded", function() {

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
    
  }, 4000);

});