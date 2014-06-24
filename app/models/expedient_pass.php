<?php
class ExpedientPass extends AppModel
{
    var $name = 'ExpedientPass';
    var $belongsTo = 'Expedient';
    var $displayField = 'pass';
}