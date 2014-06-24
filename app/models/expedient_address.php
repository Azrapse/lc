<?php
class ExpedientAddress extends AppModel
{
    var $name = 'ExpedientAddress';
    var $belongsTo = 'Expedient';
    var $displayField = 'address';
}