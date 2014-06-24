<?php
class ImportedAction extends AppModel
{
    var $name = 'ImportedAction';
    var $belongsTo = array('Expedient', 'User', 'Action');
    var $displayField = 'from';
}