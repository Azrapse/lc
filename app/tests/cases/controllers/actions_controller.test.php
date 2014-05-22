<?php
/* Actions Test cases generated on: 2010-09-15 17:09:38 : 1284564038*/
App::import('Controller', 'Actions');

class TestActionsController extends ActionsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class ActionsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.action', 'app.action_status', 'app.expedient', 'app.document');

	function startTest() {
		$this->Actions =& new TestActionsController();
		$this->Actions->constructClasses();
	}

	function endTest() {
		unset($this->Actions);
		ClassRegistry::flush();
	}

	function testEdit() {

	}

	function testDelete() {

	}

	function testAdd() {

	}

	function testAjaxSetRelevance() {

	}

	function testAjaxViewRelevance() {

	}

}
?>