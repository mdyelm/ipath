<p style="font-size: 16px"><?=__('Dear')?>&nbsp;<?= $data['username'] ?></p>
<p style="font-size: 16px"><?=__('This survey was completed.') ?></p>
<p style="font-size: 16px"><?=__('The Overview of the survey is as below') ?></p>
<div class="table-responsive">
    <table style="font-size: 18px;border-collapse:collapse;background-color:#f9f9f9">
        <tbody>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?php if ($route['Users']['del_flg']) : ?>
                        <?=__('DELETED USER')?>
                    <?php else : ?>
                        <?=__('USER')?>
                    <?php endif; ?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?php if ($route['Users']['del_flg']) : ?>
                        <?php echo $route['Users']['username']; ?>
                    <?php else : ?>
                        <?php echo $route['Users']['username']; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('Device ID')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;">
                    <?= $route['Devices']['name'] ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('SURVEY')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;">
                    <?= $route['id'] ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('DATE')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;">
                    <?= $route['time_start']->i18nFormat('yyyy/MM/dd HH:mm:ss') . ' - ' . $route['time_end']->i18nFormat('yyyy/MM/dd HH:mm:ss'); ?>
                </td>
            </tr>

            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('START POSITION')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;">
                    <?= $this->App->convertDecimalToSexagesimal($route['LocationsMin']['longitude']) . ' / ' . $this->App->convertDecimalToSexagesimal($route['LocationsMin']['latitude']); ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                   <?=__('START LOCATION')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?= $route['country'] ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('END POSITION')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;">                                
                        <?= $this->App->convertDecimalToSexagesimal($route['Locations']['longitude']) . ' / ' . $this->App->convertDecimalToSexagesimal($route['Locations']['latitude']); ?>                                   
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('END LOCATION')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?= $route['last_address'] ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?=__('NUMBER OF PHOTOS')?>
                </td>
                <td style="border: 1px solid #ddd;padding: 5px;"> 
                    <?= $data['image_number'] . ' Pic' ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<br/>
<p style="font-size: 16px"><?=__('Best Regards')?></p>
<p style="font-size: 16px"><?= $data['author'] ?></p>