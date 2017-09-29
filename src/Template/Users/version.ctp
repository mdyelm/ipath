<?php 
    use Cake\I18n\Time;
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
        <span><?= __('Version Information') ?></span></div>
    <div class="col-md-1"></div>
    <div class="col-md-10 table-version-index">
        <div class="table-responsive">
            <div class="addVersion">
                <?= $this->Html->link(__('Register "NEW VERSION"'), ['controller' => 'users', 'action' => 'registerVersion'], ['class' => 'btn btn-success']); ?>
            </div>
            <div class="clear"></div>
            <?= $this->Flash->render() ?>
            <span class="typeTitle"><?= __('Web Operation Assistant') ?></span>
            <div class="divTableTitleVersion">
                <table>
                    <tr class="trTitle">
                        <td class="col-xs-3">
                            <?= __('Version') ?>
                        </td>
                        <td class="col-xs-3">
                            <?= __('Date') ?>
                        </td>
                        <td class="col-xs-6">
                            <?= __('Content of change') ?>
                        </td>

                    </tr>
                </table>
            </div>
            <div class="divTableVersion">
                <table>
                    <?php foreach ($web as $datum): ?>
                    <?php 
                        $releasedSet = Time::parse($datum->released);
                        $releasedTime = $releasedSet->i18nFormat('yyyy-MM-dd');
                    ?>
                        <tr>
                            <td class="col-xs-3">
                                    <?php echo $this->Html->link($datum->version,
                                            ['controller' => 'users', 'action' => 'editVersion',$datum->id]
                                        );  
                                    ?>
                            </td>
                            <td class="col-xs-3"><?= $releasedTime ?></td>
                            <td class="col-xs-6"><?= $datum->content ?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                </table>
            </div>
            <span class="typeTitle"><?= __('android') ?></span>
            <div class="divTableTitleVersion">
                <table>
                    <tr class="trTitle">
                        <td class="col-xs-3">
                            <?= __('Version') ?>
                        </td>
                        <td class="col-xs-3">
                            <?= __('Date') ?>
                        </td>
                        <td class="col-xs-6">
                            <?= __('Content of change') ?>
                        </td>

                    </tr>
                </table>
            </div>
            <div class="divTableVersion">
                <table>
                    <?php foreach ($android as $datum): ?>
                    <?php 
                        $releasedSet = Time::parse($datum->released);
                        $releasedTime = $releasedSet->i18nFormat('yyyy-MM-dd');
                    ?>
                        <tr>
                            <td class="col-xs-3"> <?php echo $this->Html->link($datum->version,
                                            ['controller' => 'users', 'action' => 'editVersion',$datum->id]
                                        );  
                                    ?></td>
                            <td class="col-xs-3"><?= $releasedTime ?></td>
                            <td class="col-xs-6"><?= $datum->content ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <span class="typeTitle"><?= __('iOS') ?></span>
            <div class="divTableTitleVersion">
                <table>
                    <tr class="trTitle">
                        <td class="col-xs-3">
                            <?= __('Version') ?>
                        </td>
                        <td class="col-xs-3">
                            <?= __('Date') ?>
                        </td>
                        <td class="col-xs-6">
                            <?= __('Content of change') ?>
                        </td>

                    </tr>
                </table>
            </div>
            <div class="divTableVersion">
                <table>
                    <?php foreach ($iOS as $datum): ?>
                    <?php 
                        $releasedSet = Time::parse($datum->released);
                        $releasedTime = $releasedSet->i18nFormat('yyyy-MM-dd');
                    ?>
                        <tr>
                            <td class="col-xs-3"> <?php echo $this->Html->link($datum->version,
                                            ['controller' => 'users', 'action' => 'editVersion',$datum->id]
                                        );  
                                    ?></td>
                            <td class="col-xs-3"><?= $releasedTime ?></td>
                            <td class="col-xs-6"><?= $datum->content ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            

        </div>
    </div>
</div>
<div class="col-md-6">
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    });
</script>