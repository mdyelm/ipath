<?php
use Cake\Routing\Router; 
use Cake\Core\Configure;
?>
<div class="overflow">
    <div class="title-top">
        <?php
        echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
            'url' => [
                'controller' => 'surveys',
                'action' => 'index',
        ]]);
        ?>
        <span><?=__('Edit app language')?></span></div>
    <div class="col-md-2"></div>
    <div class="col-md-8 mt55">
        <?= $this->Form->create($result, ['autocomplete' => 'off', 'novalidate' => 'novalidate', 'id' => 'formEdit']); ?>
        <?= $this->Form->input('id', ['type' => 'hidden']); ?>
        <div class = "table-responsive">
            <table class = "top15 table table-bordered">
                <!--<tr><th class = "active text-center" colspan = "2">&nbsp;</th></tr>-->
                <tr>
                    <th class="active text-center" style="width:150px;"><?=__('Language')?></th>
                    <td>
                        <?php 
                            if(in_array($result['id'], Configure::read('id_language_fix')))
                            {
                                echo $result['language'];
                            }else{
                                echo  $this->Form->input('language', ['class' => 'form-control', 'id' => 'language']);
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="active text-center" style="width:150px;"><?=__('Shortname')?></th>
                    <td><?= $this->Form->input('shortname', ['class' => 'form-control nospace', 'id' => 'shortname']); ?></td>
                </tr>
                <tr>
                    <th class="active text-center"><?=__('Show')?></th>
                    <td>
                        <?=
                        $this->Form->input('publish_flg', ['type' => 'radio', 'options' => [
                                ['value' => '0', 'text' => __('Show')],
                                ['value' => '1', 'text' => __('Hidden')],
                            ], 'default' => 0, 'class' => 'form-control', 'id' => 'publish_flg']);
                        ?>
                    </td>
                </tr>                
                <tr>
                    <td colspan="2" class="text-center">
                        <?= $this->Form->input('del_flg', ['type' => 'hidden', 'id' => 'del_flg']); ?>
                        <?php 
                            if(!in_array($result['id'], Configure::read('id_language_fix')))
                            {
                                      
                        ?>
                             <button class="btn btn-danger" id="btn-delete"><?=__('Delete')?></button> 
                        <?php 
                            }
                        ?>
                        <?=
                        $this->Html->link(__('Cancel'), [
                            'controller' => 'Users',
                            'action' => 'appLanguageVersion',
                                ], ['class' => 'btn btn-default']);
                        ?>
                        <button class="btn btn-primary" id="btn-manga-edit"><?=__('OK')?></button>
                    </td>
                </tr>
            </table>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="col-md-6">
    <!--        <table class="top15 table table-bordered">
            <tr>
                <td class="text-center">
    
                </td>
            </tr>
        </table>-->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('input.nospace').keydown(function (e) {
            if (e.keyCode == 32) {
                return false;
            }
        });
        $('#btn-delete').click(function (e) {
            e.preventDefault();
            var r = confirm("<?= __("Are you sure want to delete this language?")?>");
            if (r == true) {
                $('#del_flg').val(1);
                $('form#formEdit').submit();
            } else {

            }
        });        
    });
</script>