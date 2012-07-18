<?php
/**
 * Default MVC controller
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.jemonweb.control
 * 
 * --------------------------
 * - MySQL Stored Procedure -
 * --------------------------
 * CALL channel_get_range(NULL, NULL);
 * CALL channel_get_range('2012-02-20', NULL);
 * CALL channel_get_range('2012-02-20', '2012-02-21');
 * CALL channel_get_range('2012-02-20', '2012-02-21 12:59:59');
 * CALL channel_get_range('2012-02-20 00:00:00', '2012-02-22 12:59:59');
 */
class IndexController extends BaseController {

    public function __construct() {

        parent::__construct();
        $this->set('title', AgilePHP::getAppName());
    }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {
	      $this->render('dashboard');
	  }

	  /**
	   * Displays the dashboard page
	   * 
	   * @return void
	   */
      public function dashboard() {
	      $this->render('dashboard');
	  }

	  /**
	   * Receives HTTP POST from the Arduino device which
	   * contains the following data points.
	   * 
	   * 1) Real Power
	   * 2) Apparent Power
	   * 3) Power Factor
	   * 4) Volts (RMS)
	   * 5) Current/Amperage (RMS)
	   * 6) Watt Hours Inc (... Inc? have no idea right now...)
	   * 7) Watt Hours
	   * 8) 110 Volt Mains AC Phase (1 left leg, 2 right leg)
	   * 
	   * @return void
	   */
	  public function arduino() {

	      $request = Scope::getRequestScope();

	      $channel1 = new Channel();
	      $channel1->setRealPower($request->get('realPower1'));
	      $channel1->setApparentPower($request->get('apparentPower1'));
	      $channel1->setPowerFactor($request->get('powerFactor1'));
	      $channel1->setVrms($request->get('Vrms1'));
	      $channel1->setIrms($request->get('Irms1'));
	      $channel1->setWhInc($request->get('whInc1'));
	      $channel1->setWh($request->get('wh1'));
	      $channel1->setPhase(1);

	      $channel2 = new Channel();
	      $channel2->setRealPower($request->get('realPower2'));
	      $channel2->setApparentPower($request->get('apparentPower2'));
	      $channel2->setPowerFactor($request->get('powerFactor2'));
	      $channel2->setVrms($request->get('Vrms2'));
	      $channel2->setIrms($request->get('Irms2'));
	      $channel2->setWhInc($request->get('whInc2'));
	      $channel2->setWh($request->get('wh2'));
	      $channel2->setPhase(2);

	      $channel1->persist();
	      $channel2->persist();
	  }

	  /**
	   * Creates a user specified date range report at /generated/jemon-graph-report-big.png
	   * 
	   * @param $start Start date
	   * @param $end End date
	   * @return void
	   */
	  public function report($start = null, $end = null) {

	      $start = date('Y-m-d H:i', strtotime($start));
	      $end = date('Y-m-d H:i', strtotime($end));

	      $channel = new SPChannelReport($start, $end);
	      $datapoints = $channel->call();

	      $Irms1 = new XYDataSet();
	      $Irms2 = new XYDataSet();

	      $day = '';
	      for($i=0; $i<count($datapoints); $i++) {

	          if(!$datapoints[$i] instanceof SPChannelReport) return;  // This keeps things from blowing up if no records are returned... why?

	          $timestamp = $datapoints[$i]->getTimestamp();
	          $strtotimestamp = strtotime($timestamp);
	          $dayDate = date('d', $strtotimestamp);

	          $axisText = '';
	          if($dayDate != $day) {

	             $axisText = date('m-d-Y', $strtotimestamp);
	             $day = $dayDate;
	          }

	          $datapoints[$i]->getPhase() == '1' ?
	             $Irms1->addPoint(new Point($axisText, $datapoints[$i]->getIrms())) :
	             $Irms2->addPoint(new Point('', $datapoints[$i]->getIrms()));
	      }

          require_once 'components/libchart/libchart/classes/libchart.php';

          $displayDate = $start == $end ?
	          date('F j, Y (H:i)', strtotime($start)) :
	          date('F j, Y (H:i)', strtotime($start)) . ' - ' . date('F j, Y (H:i)', strtotime($end));

	      // Center piece chart
    	  $chart = new LineChart(950, 800); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle($displayDate);
    	  $chart->render("generated/jemon-graph-report-center.png");

    	  // Large chart
    	  $chart = new LineChart(2000, 1000); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle($displayDate);
    	  $chart->render("generated/jemon-graph-report-big.png");
	  }

	  /**
	   * Creates today's graph at generated/jemon-graph-today-small.png
	   * and generated/jemon-graph-today-big.png
	   * 
	   * @return void
	   */
	  public function graphToday() {

	      $channel = new SPChannelReport();
	      $datapoints = $channel->call();

	      $Irms1 = new XYDataSet();
	      $Irms2 = new XYDataSet();

	      $day = '';
	      for($i=0; $i<count($datapoints); $i++) {

	          $timestamp = $datapoints[$i]->getTimestamp();
	          $strtotimestamp = strtotime($timestamp);
	          $dayDate = date('d', $strtotimestamp);

	          $axisText = '';
	          if($dayDate != $day) {

	             $axisText = date('m-d-Y', $strtotimestamp);
	             $day = $dayDate;
	          }

	          $datapoints[$i]->getPhase() == '1' ?
	             $Irms1->addPoint(new Point($axisText, $datapoints[$i]->getIrms())) :
	             $Irms2->addPoint(new Point('', $datapoints[$i]->getIrms()));
	      }

          require_once 'components/libchart/libchart/classes/libchart.php';

          // Small chart
          $chart = new LineChart(300, 300); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Today");
    	  $chart->render("generated/jemon-graph-today-small.png");

    	  // Center piece chart
    	  $chart = new LineChart(950, 800); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Today");
    	  $chart->render("generated/jemon-graph-today-center.png");

    	  // Large chart
    	  $chart = new LineChart(2000, 1000); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Today");
    	  $chart->render("generated/jemon-graph-today-big.png");
	  }

	  /**
	   * Generates yesterday's graph at generated/jemon-graph-yesterday-small.png
	   * and generated/jemon-graph-yesterday-big.png
	   * 
	   * @return void
	   */
      public function graphYesterday() {

          $yesterday = date("Y-m-d", strtotime("yesterday"));

	      $channel = new SPChannelReport($yesterday, $yesterday);
	      $datapoints = $channel->call();

	      $Irms1 = new XYDataSet();
	      $Irms2 = new XYDataSet();

          $day = '';
	      for($i=0; $i<count($datapoints); $i++) {

	          $timestamp = $datapoints[$i]->getTimestamp();
	          $strtotimestamp = strtotime($timestamp);
	          $dayDate = date('d', $strtotimestamp);

	          $axisText = '';
	          if($dayDate != $day) {

	             $axisText = date('m-d-Y', $strtotimestamp);
	             $day = $dayDate;
	          }

	          $datapoints[$i]->getPhase() == '1' ?
	             $Irms1->addPoint(new Point($axisText, $datapoints[$i]->getIrms())) :
	             $Irms2->addPoint(new Point('', $datapoints[$i]->getIrms()));
	      }

          require_once 'components/libchart/libchart/classes/libchart.php';

          // Small chart
          $chart = new LineChart(300, 300); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Yesterday");
    	  $chart->render("generated/jemon-graph-yesterday-small.png");

    	  // Large chart
    	  $chart = new LineChart(2000, 1000); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Yesterday");
    	  $chart->render("generated/jemon-graph-yesterday-big.png");
	  }
	  
	  /**
	   * Generates Month To Date graph at generated/jemon-graph-mtd-small.png
	   * and generated/jemon-graph-mtd-big.png
	   * 
	   * @return void
	   */
      public function graphMTD() {

          $start = date("Y-m-") . "01";
          $end = date("Y-m-") . date('t',strtotime('today'));
          
	      $channel = new SPChannelReport($start, $end);
	      $datapoints = $channel->call();

	      $Irms1 = new XYDataSet();
	      $Irms2 = new XYDataSet();

          $day = '';
	      for($i=0; $i<count($datapoints); $i++) {

	          $timestamp = $datapoints[$i]->getTimestamp();
	          $strtotimestamp = strtotime($timestamp);
	          $dayDate = date('d', $strtotimestamp);

	          $axisText = '';
	          if($dayDate != $day) {

	             $axisText = date('m-d-Y', $strtotimestamp);
	             $day = $dayDate;
	          }

	          $datapoints[$i]->getPhase() == '1' ?
	             $Irms1->addPoint(new Point($axisText, $datapoints[$i]->getIrms())) :
	             $Irms2->addPoint(new Point('', $datapoints[$i]->getIrms()));
	      }

          require_once 'components/libchart/libchart/classes/libchart.php';

          // Small chart
          $chart = new LineChart(300, 300); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Month To Date");
    	  $chart->render("generated/jemon-graph-mtd-small.png");

    	  // Large chart
    	  $chart = new LineChart(2000, 1000); // width, height
    	  $dataSet = new XYSeriesDataSet();
    	  $dataSet->addSerie("Phase 1", $Irms1);
    	  $dataSet->addSerie("Phase 2", $Irms2);
    	  $chart->setDataSet($dataSet);
    	  $chart->setTitle("Month To Date");
    	  $chart->render("generated/jemon-graph-mtd-big.png");
	  }
}
?>