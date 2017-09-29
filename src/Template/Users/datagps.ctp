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
        <span><?= __('Survey list and data download') ?></span></div>

    <div class="divDataGps mt20">
        <?= $this->Flash->render() ?>
        <div class="divTableTitle">
            <table>
                <tr class="trTitle">
                    <td width="10%" style="border-left: 1px solid #dddddd" class="fContent">
                        <?= __('SURVEY ID') ?>
                        <?php
                        $imageIDGps = 'filter-up-down.png';
                        $typeIdGps = "asc";
                        $imageDateGps = 'filter-up-down.png';
                        $typeDateGps = "asc";
                        $imageUserGps = 'filter-up-down.png';
                        $typeUserGps = "asc";
                        if (!empty($filtergps) && !empty($typegps)) {
                            if ($typegps == "asc") {
                                if ($filtergps == "id") {
                                    $imageIDGps = "filter-up.png";
                                    $typeIdGps = "desc";
                                } elseif ($filtergps == "date") {
                                    $imageDateGps = "filter-up.png";
                                    $typeDateGps = "desc";
                                } elseif ($filtergps == "user") {
                                    $imageUserGps = "filter-up.png";
                                    $typeUserGps = "desc";
                                }
                            } else {
                                if ($filtergps == "id") {
                                    $imageIDGps = "filter-down.png";
                                } elseif ($filtergps == "date") {
                                    $imageDateGps = "filter-down.png";
                                } elseif ($filtergps == "user") {
                                    $imageUserGps = "filter-down.png";
                                }
                            }
                        }
                        ?>
                        <?php
                        echo $this->Html->image($imageIDGps, [
                            'class' => 'fix-img-dataGps',
                            'url' => ['controller' => 'Users', 'action' => 'datagps?filtergps=id&typegps=' . $typeIdGps]
                        ]);
                        ?>
                    </td>
                    <td width="10%" class="fContent">
                        <?= __('DATE') ?>
                        <?php
                        echo $this->Html->image($imageDateGps, [
                            'class' => 'fix-img-dataGps',
                            'url' => ['controller' => 'Users', 'action' => 'datagps?filtergps=date&typegps=' . $typeDateGps]
                        ]);
                        ?>
                    </td>
                    <td width="15%" class="fContent">
                        <?= __('USER') ?>
                        <?php
                        echo $this->Html->image($imageUserGps, [
                            'class' => 'fix-img-dataGps',
                            'url' => ['controller' => 'Users', 'action' => 'datagps?filtergps=user&typegps=' . $typeUserGps]
                        ]);
                        ?>
                    </td>
                    <td width="35%" class="fContent">
                        <?= __('LOCATION') ?>
                    </td>
                    <?php if(isset($authUser['admin_flg']) && $authUser['admin_flg']==1) { ?>
                        <td width="10%" class="fContent">
                            <?= __('Show') ?>
                        </td>
                    <?php } ?>
                    <td width="15%" class="fContent">
                        <?= __('DEVICE ID') ?>
                    </td>
                    <td width="5%" style="border-right: none" class="fContent"></td>
                </tr>
            </table>
        </div>
        <div class="divTable">
            <table>
                <?php
                echo $this->Form->create(null, [
                    'id' => 'formSubmitDow',
                    'url' => ['controller' => 'Users', 'action' => 'dowloadCsv']
                ]);
                ?>
                <?php
                foreach ($result as $key => $value) {
                    $dateSet = new \DateTime($value['time_start']);
                    $dateFormat = $dateSet->format('Y/m/d');
                    ?>
                    <tr>
                        <td width="10%" style="border-left: 1px solid #dddddd" class="fContent"><?php if(isset($arrayIndex[$value['id']])) echo $arrayIndex[$value['id']]; else echo $value['id']; ?></td>
                        <td width="10%" class="fContent"><?= $dateFormat ?></td>
                        <td width="15%" class="fContent"><?= $value['Users']['username'] ?></td>
                        <td width="35%" class="fLeft"><?= $value['country'] ?></td>
                        <?php if(isset($authUser['admin_flg']) && $authUser['admin_flg']==1) { ?>
                            <td width="10%" class="fContent">
                                <a data-del="<?=$value['delete_flg']?>" data-id="<?= $value['id'] ?>" class="showRouteInput">
                                    <span>
                                        <?php 
                                            if($value['delete_flg']==0){
                                                echo __('YES') ;
                                            }else{
                                                echo __('NO') ;
                                            }
                                        ?>
                                    </span>
                                </a>

                            </td>
                        <?php } ?>
                        <td width="15%" class="fContent"><?= $value['Devices']['name'] ?></td>
                        <td width="5%" style="border-right: none" class="fContent tdLast"><input name="idRoute[]" value="<?= $value['id'] ?>" type="checkbox"></td>
                    </tr>
                    <?php
                }
                ?>
                <?php echo $this->Form->end(); ?>
            </table>
        </div>
        <br>
        <div class="ov mt10 mb20 text-center">
            <button style="display: none;" onclick="removeAll();" class="btn-primary btn" id="btn-removeall"><?= __('Remove All') ?></button>
            <button onclick="checkAll();" class="btn-primary btn" id="btn-checkall"><?= __('Check All') ?></button>
            <button onclick="dowloadCsv();" class="btn-primary btn" id="btn-download"><?= __('Download GPS data') ?></button>
        </div>
    </div>
</div>
<script>
    function checkAll() {
        $('.divTable .tdLast input').prop('checked', true);
        $('#btn-removeall').css('display', 'inline-block');
        $('#btn-checkall').css('display', 'none');
    }
    function removeAll() {
        $('.divTable .tdLast input').prop('checked', false);
        $('#btn-removeall').css('display', 'none');
        $('#btn-checkall').css('display', 'inline-block');
    }
    function dowloadCsv() {
        $('#formSubmitDow').submit();
    }
    $(".showRouteInput").on("click", showRouteInput);
    function showRouteInput() {
        var check = 1;
        var spCheck = "<?=__('NO') ;?>";
        if($(this).attr('data-del')==1){
            check = 0;
            spCheck = "<?=__('YES') ;?>";
        }
        var data = {
            'check' : check,
            'dataId' : $(this).attr('data-id'),
        };
        var url = "<?php echo $this->Url->build(['controller' => 'Users', 'action' => 'deleteRoute']);?>";
        var that = this;
        $.post(url,data,function(dataR){
            if(dataR.code==1){
                $(that).attr('data-del',check);
                $(that).find('span').text(spCheck);
            }else{
                alert(dataR.message);
            }
        },'json');
    }
</script>