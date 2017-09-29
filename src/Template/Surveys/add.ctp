<!-- src/Template/Users/add.ctp -->

<div class="users form">
<?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Add User') ?></legend>
        <?= $this->Form->input('username',['label' => false,'required'=>false]) ?>
        <?= $this->Form->input('password') ?>
   </fieldset>
<?= $this->Form->button(__('Submit')); ?>
<?= $this->Form->end() ?>
</div>
<?php 
//    if ($this->Form->isFieldError('gender')) {
//        echo $this->Form->error('gender');
//    }
?>