<?php defined('APPLICATION') or die; ?>

<?= $this->Form->open(['action' => url('plugin/apitoken')]) ?>
<?= $this->Form->errors() ?>
<ul>
    <li>
        <?= $this->Form->label('Email', 'email') ?>
        <?= $this->Form->textBox('email') ?>
    </li>
    <li>
        <?= $this->Form->label('Password', 'password') ?>
        <?= $this->Form->textBox('password') ?>
    </li>
</ul>
<?= $this->Form->close('Get Token') ?>
