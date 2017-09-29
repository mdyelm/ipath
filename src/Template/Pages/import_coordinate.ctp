<?php 
    echo $this->Form->create(false, array('type' => 'file'));
    echo $this->Form->input('route');
    echo $this->Form->file('file');
    echo $this->Form->submit(__('Submit'));
?>
<?= $this->Form->end(); ?>