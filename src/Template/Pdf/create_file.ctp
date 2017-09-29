<?php 
    pr($dataPdf);
?>
<div class="overflow dataGps">
    <div class="divDataGps mt55">
        <div class="divTable">
            <table>
                <tr class="trTitle">
                    <th width="15%" class="fContent">
                        DATE
                    </th>
                    <th width="15%" class="fContent">
                        USER
                    </th>
                    <th width="35%" class="fContent">
                        LOCATION
                    </th>
                    <th width="15%" class="fContent">
                        DEVICE ID
                    </th>
                    <th width="5%" class="fContent"></th>
                </tr>
                <tr>
                    <td style="text-align: right" class="fContent">1</td>
                    <td class="fContent">2</td>
                    <td class="fContent">3</td>
                    <td class="fLeft">4</td>
                    <td class="fContent">5</td>
                    <td class="fContent">6</td>
                </tr>
               
            </table>
        </div>
        <br>
        <br>
        <div class="ov mt10 mb20 text-center">
            <button onclick="checkAll();" class="btn-primary btn" id="btn-checkall"><?=__('Check All')?></button>
            <button onclick="dowloadCsv();" class="btn-primary btn" id="btn-download"><?=__('Download GPS data')?></button>
        </div>
    </div>
</div>

