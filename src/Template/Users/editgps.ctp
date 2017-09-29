<?php

use Cake\Routing\Router; ?>
<div class="overflow dataGps">
    <div class="title-top">
        <?php
        echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
            'url' => [
                'controller' => 'surveys',
                'action' => 'index',
        ]]);
        ?>
        <span><?= __('Fix survey GPS') ?></span>
    </div>
    <div class="divDataGps mt20">
        <?= $this->Flash->render() ?>
        <div class="divTableTitle">
            <table>
                <tr class="trTitle">
                    <td width="15%" class="fContent">
                        <?= __('ID') ?>
                    </td>
                    <td width="25%" class="fContent">
                        <?= __('LATITUDE') ?>
                    </td>
                    <td width="25%" class="fContent">
                        <?= __('LONGTITUDE') ?>
                    </td>
                    <td width="15%" class="fContent">
                        <?= __('DATE') ?>
                    </td>
                    <td width="20%" class="fContent">
                        <?= __('GPS Point Show/Hide') ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="divTable editGps">
            <table>
                <?php
                $checkLocation = 0;
                foreach ($data as $val) { 
                    if ($val->image_id == 0 && $val->delete_flg==0){
                        $checkLocation = $checkLocation + 1;
                    }
                }
                $index = 0; 
                $dateFormat = "";
                foreach ($data as $key => $value) {
                    $index = $index +1;
                    if(!empty($value->catch_time)){
                        $dateFormat = $value->catch_time->format('Y/m/d H:i:s');
                    }
                    if ($value['delete_flg'] == 0) {
                        $showHideGPS = __('Show');
                    }
                    else {
                        $showHideGPS = __('Hidden');
                    }
                    ?>
                    <tr>
                        <td width="15%" class="fContent"><?= $index ?></td>
                        <td width="25%" class="fContent tdEditGps">
                            <a class="editLatLong" ><?= $value['latitude'] ?></a>
                            <div class="formEditGps">
                                <input type="text" value="<?= $value['latitude'] ?>">
                                <button class="btn-xs btn-info btSubmitGps" data-id="<?= $value['id'] ?>" data-save="latitude"><?= __('Submit') ?></button>
                                <button class="btn-xs btn-info btCancelGps"><?= __('Cancel') ?></button>
                            </div>
                        </td>
                        <td width="25%" class="fContent tdEditGps">
                            <a  class="editLatLong"><?= $value['longitude'] ?></a>
                            <div class="formEditGps">
                                <input type="text" value="<?= $value['longitude'] ?>">
                                <button class="btn-xs btn-info btSubmitGps" data-id="<?= $value['id'] ?>" data-save="longitude"><?= __('Submit') ?></button>
                                <button class="btn-xs btn-info btCancelGps"><?= __('Cancel') ?></button>
                            </div>
                        </td>
                        <td width="15%" class="fContent"><?=$dateFormat?></td>
                        <td width="20%" style="border-right: none" class="fContent">
                            <?php if ($value->image_id == 0 && ($checkLocation > 1 || ($checkLocation < 2 && $value->delete_flg==1))): ?>
                            <a class="editShowHide" data-id="<?= $value['id'] ?>" data-val="<?= $value['delete_flg'] ?>"><?=$showHideGPS?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <br>
        <div class="ov mt10 mb20 text-center">
            <button onclick="urlBack();" class="btn-primary btn" id="btn-download"><?= __('Back') ?></button>
        </div>
    </div>
</div>
<script>
    function urlBack() {
        var urlBack = "<?php echo $this->Url->build(['controller' => 'Surveys', 'action' => 'view_route',$id]);?>";
        window.location.href = urlBack;
    }
    $(document).ready(function() {
        $(".editGps .editLatLong").on("click", editLatLong);
        $(".editGps .btCancelGps").on("click", btCancelGps);
        $(".editGps .btSubmitGps").on("click", btSubmitGps);
        $(".editGps .editShowHide").on("click", btSubmitShowHideGps);
    });
    function editLatLong() {
        var tdEditGps = $(this).parent();
        $(tdEditGps).find('.formEditGps').show();
//        $(this).hide();
    }
    function btCancelGps() {
        var tdEditGps = $(this).parent().parent();
        $(this).parent().hide();
//        $(tdEditGps).find('.editLatLong').show();
    }
    function btSubmitGps() {
        var formEditGps = $(this).parent();
        var val = $(formEditGps).find('input').val();
        var url = "<?php echo $this->Url->build(['controller' => 'Users', 'action' => 'editLatLongGPS',$id]);?>";
        var data = {
            'dataSave' : $(this).attr('data-save'),
            'dataId' : $(this).attr('data-id'),
            'valSave' : val
        };
        var that = this;
        $.post(url,data,function(dataR){
            if(dataR.code==1){
                var tdEditGps = $(that).parent().parent();
                $(that).parent().hide();
                $(tdEditGps).find('.editLatLong').text(val).show();
            }else{
                alert(dataR.message);
            }
        },'json');
    }
    
    function btSubmitShowHideGps() {
        var value = parseInt($(this).attr('data-val'));
        var url = "<?php echo $this->Url->build(['controller' => 'Users', 'action' => 'editShowHideGPS',$id]);?>";
        var data = {
            'dataId' : $(this).attr('data-id'),
            'valSave' : value
        };
        var that = this;
        $.post(url,data,function(dataR){
            if(dataR.code==1){
//                if (value == 0) {
//                    $(that).attr('data-val', 1);
//                    $(that).html("<?= __('Hidden') ?>");
//                }
//                else {
//                    $(that).attr('data-val', 0);
//                    $(that).html("<?= __('Show') ?>");
//                }
                location.reload();
            }else{
                alert(dataR.message);
            }
        },'json');
    }
    
</script>
