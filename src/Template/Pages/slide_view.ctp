<div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:1366px;height:768px;overflow:hidden;visibility:hidden;">
    <!-- Loading Screen -->
    <div data-u="loading" class="jssorl-oval" style="position:absolute;top:0px;left:0px;text-align:center;background-color:rgba(0,0,0,0.7);">
        <?php
        echo $this->Html->image("slide-app/oval.svg", [
            "data-u" => "image",
            'style' => "margin-top:-19.0px;position:relative;top:50%;width:38px;height:38px;"
        ]);
        ?>
    </div>
    <div class="dataUSlide" data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1366px;height:768px;overflow:hidden;">
        <?php $a = 0;foreach ($slide as $value) { $a = $a +1; ?>
            <div>
                <div class="titleSlide"> <?=$value['guide_text']?></div>
                <div class="clear"></div>
                <?php
                echo $this->Html->image("/files/slide-app/" . $value['image'], ['class' => 'imageSlide']);
                ?>
            </div>
            
        <?php } ?>
    </div>
    <!-- Bullet Navigator -->
    <div data-u="navigator" class="jssorb05" style="bottom:20px;right:16px;" data-autocenter="1">
        <!-- bullet navigator item prototype -->
        <div data-u="prototype" style="width:16px;height:16px;"></div>
    </div>
</div>

