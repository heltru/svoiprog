<?php
/**
 * Created by PhpStorm.
 * User: heltru
 * Date: 31.01.2020
 * Time: 14:32
 */
?>

<iframe src="https://vk.com/ads?act=office" width="468" height="60"  ></iframe>


<script>
    $(document).ready(function (){
        $.ajax({
            type:"GET",
            url:"https://vk.com/dev/ads",

            success:function (data) {
             console.log(data);

            }
        });
    });
</script>