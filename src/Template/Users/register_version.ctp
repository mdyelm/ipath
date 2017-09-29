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
        <span><?=__('Register "NEW VERSION"')?></span></div>
    <div class="col-md-2"></div>
    <div class="col-md-8 mt55">
        <?= $this->Form->create($result, ['autocomplete' => 'off', 'novalidate' => 'novalidate']); ?>
        <?= $this->Flash->render() ?>
        <div class = "table-responsive">
            <table class = "top15 table table-bordered">
                <!--<tr><th class = "active text-center" colspan = "2">&nbsp;</th></tr>-->
                <tr>
                    <th class="active text-center" style="width:150px;"><?=__('Version')?></th>
                    <td><?= $this->Form->input('version', ['class' => 'form-control', 'id' => 'version']); ?></td>
                </tr>
                <tr class="date-version">
                    <th class="active text-center" style="width:150px;"><?=__('Date')?></th>
                    <td><?= $this->Form->input('released', ['class' => 'form-control nospace', 'id' => 'released']); ?></td>
                </tr>
               
                <tr>
                    <th class="active text-center"><?=__('Types')?></th>
                    <td class="classType">
                        <?=
                        //$aaa = Configure::read('Version2');
                        $this->Form->input('device', ['type' => 'radio', 'options' => [
                                ['value' => '0', 'text' => __('Web')],
                                ['value' => '1', 'text' => __('iOS')],
                                ['value' => '2', 'text' => __('Android')],
                            ], 'default' => 0, 'class' => 'form-control', 'id' => 'publish_flg']);
                        ?>
                    </td>
                </tr>
                 <tr>
                    <th class="active text-center" style="width:150px;"><?=__('Content of change')?></th>
                    <td><?= $this->Form->input('content', ['class' => 'form-control nospace', 'type' => 'textarea']); ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <?=
                        $this->Html->link(__('Cancel'), [
                            'controller' => 'Users',
                            'action' => 'version',
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