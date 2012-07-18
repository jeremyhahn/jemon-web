<?php

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/

require 'AgilePHP/AgilePHP.php';

try {
    AgilePHP::setFrameworkRoot(realpath(dirname(__FILE__) . '/AgilePHP' ));
	AgilePHP::init();
    AgilePHP::setDefaultTimezone('America/New_York');
    AgilePHP::setDebugMode(true);
	AgilePHP::setAppName('Jeremy\'s Energy Monitor (JEMon)');

	/*
	$fields = array('realPower1', 'apparentPower1', 'powerFactor1', 'Vrms1', 'Irms1', 'whInc1', 'wh1',
	                'realPower2', 'apparentPower2', 'powerFactor2', 'Vrms2', 'Irms2', 'whInc2', 'wh2'); 
	

	$fields = array('realPower', 'apparentPower', 'powerFactor', 'Vrms', 'Irms', 'whInc', 'wh');
	$gen = new ModelGenerator(null, 'Channel',
			 $fields,
			 true,
			 true);
			 echo $gen->createModel();
			 exit;
	*/

	MVC::dispatch();
}
catch( FrameworkException $e ) {

     require_once 'AgilePHP/mvc/PHTMLRenderer.php';

     Log::error($e->getMessage());

     $renderer = new PHTMLRenderer();
     $renderer->set('title', AgilePHP::getAppName() . ' :: Error Page');
  	 $renderer->set('error', $e->getCode() . '   ' . $e->getMessage() . (AgilePHP::isInDebugMode() ? '<br><pre>' . $e->getTraceAsString() . '</pre>' : ''));
  	 $renderer->render('error');
}
?>