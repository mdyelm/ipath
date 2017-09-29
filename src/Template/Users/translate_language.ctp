<?php
use Cake\Routing\Router; ?>
<div class="overflow">
    <div class="title-top">
        <?php
        echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
            'url' => [
                'controller' => 'surveys',
                'action' => 'index',
        ]]);
        ?>
        <span><?=__('Translate Language')?></span></div>
    
    <div class="col-md-2"></div>
            
    <div class="col-md-8 mt55">
         <?= $this->Flash->render() ?>
        <h3 style="text-align: center;"><?=__('Translate Language')?> : <?=$language['language']?></h3>
        <?= $this->Form->create(NULL, ['autocomplete' => 'off', 'novalidate' => 'novalidate']); ?>
        <div class = "table-responsive">
            <table class = "top15 table table-bordered">
                <?php
                    foreach ($languageAll as $valLan) {
                        $stringLan = "";
                        foreach ($translate as $valTran) { 
                            if($valLan['id']==$valTran['need_language_id']){
                                $stringLan = $valTran['translate'];
                                break;
                            }
                        }
                ?>
                    <tr>
                        <th class="active text-center" style="width:150px;"><?=$valLan['language']?></th>
                        <td><?= $this->Form->input('language.'.$valLan['id'], ['class' => 'form-control', 'value' =>$stringLan]); ?></td>
                    </tr>
                <?php
                    }
                ?>
                <tr>
                    <td colspan="2" class="text-center">
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
    });
</script>