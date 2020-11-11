<?php
/**
 * Created by PhpStorm.
 * User: heltru
 * Date: 23.01.2020
 * Time: 14:58
 */

?>

<script>
    $(document).ready(function () {

        $.ajax({
            url:  "/test/default/test-time",
            success:function (data){
                console.log(data);
            }
        });



        setTimeout(function (){
            location.reload();
        },50000);





    });
    </script>