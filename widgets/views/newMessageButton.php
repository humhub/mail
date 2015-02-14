<?php

/**
 * @var string $buttonLabel
 * @var string $guid
 * @var string $id
 * @var string $class
 */
echo CHtml::link($buttonLabel, $this->createUrl('//mail/mail/create', array('ajax' => 1, 'guid' => $guid)), array('class' => $class, 'id' => $id, 'data-toggle' => 'modal', 'data-target' => '#globalModal'));
?>