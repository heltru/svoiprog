<div class="container">
    <div class="row">
        <div class="col-xs-12 center">
            <h1><?=Yii::$app->name?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 center">
            <?php if (Yii::$app->user->isGuest){ ?>
                <a
                        href="/login"
                        style="background-color: #ffd400;color: black;font-weight: bold"
                        class="waves-effect waves-light btn-large send-order  ">Войти</a>
            <?php } else { ?>
                <a
                        href="/admin"
                        style="background-color: #ffd400;color: black;font-weight: bold"
                        class="waves-effect waves-light btn-large send-order  ">Личный кабинет</a>
            <?php } ?>
        </div>
    </div>

</div>

