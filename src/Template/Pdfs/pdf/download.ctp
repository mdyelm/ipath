<div id="contentPdf">
    <h1 class="fContent"><?=$titlePdf?></h1>
    <div class="divImage">
        <?php 
            $check = 0;
            foreach ($images as $value) {
                $check = $check + 1;
                $classClear = "";
                if($check%2 == 0){
                    $classClear = "clear";
                }
                
                if(!empty($value['Locations']['catch_time'])){
                    $dateTime = $value['Locations']['catch_time'];
                }else{
                    $dateTime = $value['created'];
                }
                $dateSet = new \DateTime($dateTime);
                $dateFormat = $dateSet->format('Y/m/d H:i:s');
        ?>
            <div class="row divImageLeft">
                <div class="viewImg">
                    <img src="<?=WWW_ROOT?>/files/image/<?=$route['id']?>/<?=$value['name']?>" >
                </div>
                <div class="viewDes">
                    <table style="width:80%">
                        <tr>
                          <th class="TALeft"><?= __('Date / Time') ?></th>
                          <th class="TACenter"><?=$dateFormat?></th> 
                        </tr>
                        <tr>
                          <td class="TALeft"><?= __('LAT / LON') ?></td>
                          <td class="TACenter">
                            <span class="SpanLat TACenter"><?=$this->App->convertDecimalToSexagesimal($value['Locations']['latitude']);?></span>/
                            <span class="TACenter"><?=$this->App->convertDecimalToSexagesimal($value['Locations']['longitude']);?></span> 
                          </td> 
                        </tr>
                        <tr>
                          <td class="TALeft"><?= __('DIRECTION') ?></td>
                          <td class="TACenter"><?=$this->App->changeDirectionByRotation($value['rotation']);?></td> 
                        </tr>
                        <tr>
                          <td class="TALeft"><?= __('COMMENT') ?></td>
                          <td class="tdCM TALeft"><?=$value['comment']?></td> 
                        </tr>
                      </table>
                </div>
            </div>
            <div class="<?=$classClear ?>"></div>
        <?php 
            }
        ?>
    </div>
</div>


