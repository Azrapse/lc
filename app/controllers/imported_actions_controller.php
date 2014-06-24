<?php
class ImportedActionsController extends AppController {
	var $name = 'ImportedActions';
	var $components = array('RequestHandler');
	var $uses = array('Action', 'Expedient', 'ImportedAction');

    function index()
    {
        $this->layout = false;
        $user_id = $this->getCurrentUserId();
        $newActions = $this->ImportedAction->find('all', array(
            'conditions' => array(
                'ImportedAction.user_id' => $user_id,
                'ImportedAction.read' => null
            ),
            'recursive'=>0,
            'fields'=>array(
                'ImportedAction.id',
                'ImportedAction.user_id',
                'ImportedAction.expedient_id',
                'ImportedAction.action_id',
                'ImportedAction.from',
                'ImportedAction.date',
                'Expedient.reference',
                'Action.concept'
            )
        ));

        $this->set(compact('newActions'));
    }

    function view($user_id)
    {
        //$user_id = $this->getCurrentUserId();
        $newActions = $this->ImportedAction->find('all', array(
            'conditions' => array(
                'ImportedAction.user_id' => $user_id,
                'ImportedAction.read' => null
            ),
            'recursive'=>0,
            'fields'=>array(
                'ImportedAction.id',
                'ImportedAction.user_id',
                'ImportedAction.expedient_id',
                'ImportedAction.action_id',
                'ImportedAction.from',
                'ImportedAction.date',
                'Expedient.reference',
                'Action.concept'
            )
        ));

        $this->set(compact('newActions'));
    }

    function delete($importedActionId)
    {
        $user_id = $this->getCurrentUserId();
        $ia = $this->ImportedAction->findById($importedActionId);
        if($ia['ImportedAction']['user_id'] != $user_id)
        {
            $this->set('result', false);
        }
        else
        {
            $this->ImportedAction->id = $importedActionId;
            $this->ImportedAction->delete();
            $this->set('result', true);
        }
    }
}