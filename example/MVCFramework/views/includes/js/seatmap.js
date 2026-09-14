$(document).ready(function(){
    var $result = $('#resultCart');
    var $activityid = $('#activityid').val();
    getReservedSeats();

    function getReservedSeats() {
        $.ajax({
            type: "get",
            async: true,
            dataType: 'json',
            url: "getReservedSeats",
            data: {'id': $activityid},
            success: setupSeatChart, 
            error: function(xhr){
                alert("An error occured in getReservedSeats " + xhr.status + " " + xhr.statusText);
            }
        });
    }

    function setupSeatChart(SeatChart) {
        var element = document.getElementById('container');
        var options = {
          cart: {
            submitLabel: 'Reserve your seat(s)',
            visible: true
          },
          legendVisible: {
              legendVisible: true
          },
          map: {
            rows: 12,
            columns: 20,
            seatTypes: {
              default: {
                label: 'Economy',
                cssClass: 'economy',
                price: SeatChart[0]["map"][1]["priceDefault"]
              },
              first: {
                label: 'First Class',
                cssClass: 'first-class',
                price: SeatChart[0]["map"][2]["priceFirst"],
                seatRows: [0, 1, 2, 7],
                seats: [
                    {row: 3, col: 5 },
                    {row: 3, col: 6 },
                    {row: 3, col: 7 },
                    {row: 3, col: 8 },
                    {row: 3, col: 9 },
                    {row: 3, col: 10 },
                    {row: 3, col: 11 },
                    {row: 3, col: 12 },
                    {row: 3, col: 13 },
                    {row: 3, col: 14 }
                ]
              },
              reduced: {
                label: 'Reduced',
                cssClass: 'reduced',
                price: SeatChart[0]["map"][3]["priceReduced"],
                seatRows: [9, 10, 11]
               }
            },
            disabledSeats: [
              { row: 0, col: 0 },
              { row: 0, col: 19 },
              { row: 0, col: 1 },
              { row: 0, col: 18 },
              { row: 1, col: 0 },
              { row: 1, col: 19 }
            ],
            reservedSeats: SeatChart[0]["map"][0]["reservedSeats"],
//            selectedSeats: [{ row: 0, col: 5 }, { row: 0, col: 6 }],
            rowSpacers: [3, 7],
            columnSpacers: [5,15]
          }
        };

        var sc = new Seatchart(element, options);
        sc.addEventListener('submit', function handleSubmit(e) {
          $result.text(e.total);
          $('#total').val(e.total);
          var html = '<b>Your reserved seats: </b><ul>';
          var x = 0;
          var firstSeats = '';
          var y = 0;
          var defaultSeats = '';
          var z = 0;
          var reducedSeats = '';
          for(var i in e.cart) {
              var seat = e.cart[i];
              html += '<li>Seat: '+seat.label+' - '+seat.type+' <input type="hidden" name="seat'+i+'" value='+JSON.stringify(seat.index)+'></li>';
              if (seat.type === 'first') {
                x++;
                firstSeats += JSON.stringify(seat.index) +';';
              }
              if (seat.type === 'default') {
                y++;
                defaultSeats += JSON.stringify(seat.index) +';';
              }
              if (seat.type === 'reduced') {
                z++;
                reducedSeats += JSON.stringify(seat.index) +';';
              }
          }
          html += '</ul><p><b>Complete your order and as soon as we have received your payment, we will confirm your tickets.</b></p>';
          $('#first').val(x);
          $('#default').val(y);
          $('#reduced').val(z);
          $('#firstSeats').val(firstSeats);
          $('#defaultSeats').val(defaultSeats);
          $('#reducedSeats').val(reducedSeats);
          $('#yourReservedseats').html(html);
          sendCart(e.cart);
        });
    }

    function sendCart(cart) {
        window.location.href = "#name";
//        $.ajax({
//            type: 'POST',
//            async: true,
//            dataType: 'JSON',
//            url: 'setReservedSeats',
//            data: {reservedSeats: cart},
//            success: cartConfirmed, 
//            error: function(xhr){
//                alert("An error occured in sendCart " + xhr.status + " " + xhr.statusText);
//            }
//        });                
    }

//    function cartConfirmed() {
//        window.location.href = "#name";
////        alert('Your seat are currently reserved. As soon as we have received your payment, we will make a final confirmation of the seats');
//    }
});
