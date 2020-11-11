    <script>
        var d = new Date;
        d.setHours(0);
        d.setMinutes(0);
        d.setMilliseconds(0);



        var c = 0;
var str = '';
        for (var i = 0;i <= 47 ; i++){
c++;


            var dateFormat1 = ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);

            d.setTime(d.getTime() + (30*60*1000));

            var dateFormat2 = ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);

            //1 => ['03:30','04:00'],
            str += c + " => ["+ "'" + dateFormat1+ "'"  + ","+ "'"  +  dateFormat2+ "'"  + "], ";

        }
        console.log( str /*dateFormat1 + " - " + dateFormat2*/);
    </script>