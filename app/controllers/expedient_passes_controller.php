<?php

class ExpedientPassesController extends AppController {
    var $name = 'ExpedientPasses';
    var $components = array('RequestHandler', 'Email');
    var $uses = array('ExpedientPass', 'Expedient' );

    function post_bind_pass_to_expedient()
    {
        $id = $this->params['form']['id'];
        $this->layout = false;
        $newPass = array(
            'ExpedientPass' => array(
                'pass'=>md5(uniqid('', true)),
                'expedient_id' => $id
            )
        );
        $this->ExpedientPass->deleteAll(array(
            'ExpedientPass.expedient_id' => $id
        ), false);
        $this->ExpedientPass->create();
        if($this->ExpedientPass->save($newPass))
        {
            $this->set(array(
                    'result'=>$newPass,
                    '_serialize'=>array('result')
                )
            );
        }
        else
        {
            $this->set(array(
                    'result'=>false,
                    '_serialize'=>array('result')
                )
            );
        }
    }

}