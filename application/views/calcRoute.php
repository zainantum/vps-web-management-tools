<?php
use lib\OSMap\OSMapOpenRoute;
use lib\OSMap\OSMapPoint;
// use lib\OSMap\OSMapNominatim;

// require_once 'lib/OSMap/OSMapNominatim.php';
require_once 'lib/OSMap/OSMapPoint.php';
require_once 'lib/OSMap/OSMapOpenRoute.php';
require_once 'lib/OSMap/OSMapOpenRouteStep.php';

	/**
	 * to get own API key, you must register at
	 * 	https://openrouteservice.org/dev/#/home
	 * and request new token. All further description ist found at the openrouteservice.org - page.
	 * (registration is free!) 
	 */
	$oOR = new OSMapOpenRoute('insert your own API-Key here');
	
	$oOR->setLanguage('EN');
	$oOR->setVehicleType(OSMapOpenRoute::VT_HGV);	// we're driving heavy goods ;-)
	$oOR->setFormat(OSMapOpenRoute::FMT_JSON);
	$oOR->enableInstructions();
	$oOR->setInstructionFormat(OSMapOpenRoute::IF_HTML);
	
	$aRoute = array();
	
	/*
	// simple version with from - to points
	$ptFrom = new OSMapPoint(49.41461,8.681495);
	$ptTo = new OSMapPoint(49.420318,8.687872);
	if ($oOR->calcRoute($ptFrom, $ptTo)) {
	}
	*/

	/*
	// determine geolocations with OSMapNominatim...  
	$oOSMap = new OSMapNominatim();
	
	// Dulles International Airport
	$oOSMap->setStr('Dulles International Airport');
	$oOSMap->setPostcode('VA 20166');
	if ($oOSMap->searchAddress()) {
		$aRoute[] = $oOSMap->getLocation();
	}

	// Washington Monument
	$oOSMap->reset();
	$oOSMap->setStr('Washington Monument');
	$oOSMap->setPostcode('DC 20024');
	$oOSMap->setCity('Washington');
	if ($oOSMap->searchAddress()) {
		$aRoute[] = $oOSMap->getLocation();
	}
	
	// George Washington Masonic National Memorial
	$oOSMap->reset();
	$oOSMap->setStr('101 Callahan Dr');
	$oOSMap->setPostcode('VA 22301');
	$oOSMap->setCity('Alexandria');
	if ($oOSMap->searchAddress()) {
		$aRoute[] = $oOSMap->getLocation();
	}
	*/
	
	// variable version: array may contain more than two points
	// coordinates may be as comma separated string lat, lon or object from class OSMapPoint, if available as single values
	$aRoute[] = '38.95226625, -77.45342297783296';					// Dulles International Airport
	$aRoute[] = '38.889483150000004, -77.03524967010638';			// Washington Monument
	$aRoute[] = new OSMapPoint(38.80746845, -77.06596192040345);	// George Washington Masonic National Memorial
	
	if ($oOR->calcRoute($aRoute)) {
		echo 'Distance: ' . $oOR->getDistance() . $oOR->getUnits() . '<br/>';
		echo 'Duration: ' . $oOR->getDuration() . 's<br/>';
		$iCnt = $oOR->getSegmentCount();
		echo 'Segment Count: ' . $iCnt . '<br/>';
		for ($iSeg = 0; $iSeg < $iCnt; $iSeg++) {
			echo '&nbsp;&nbsp;&nbsp;Segment ' . ($iSeg + 1) . '<br/>';
			echo '&nbsp;&nbsp;&nbsp;Distance: ' . $oOR->getDistance($iSeg) . $oOR->getUnits() . '<br/>';
			echo '&nbsp;&nbsp;&nbsp;Duration: ' . $oOR->getDuration($iSeg) . 's<br/>';
			$iSteps = $oOR->getStepCount($iSeg);
			echo '&nbsp;&nbsp;&nbsp;Step Count: ' . $iSteps . '<br/>';
			for ($iStep = 0; $iStep < $iSteps; $iStep++) {
				$oStep = $oOR->getStep($iSeg, $iStep);
				if ($oStep) {
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $oStep->getInstruction() . '<br/>';
				}
			}				
			echo '<br/>';
		} 
		// save on file
		// $oOR->saveRoute();	
	} else {
		echo $oOR->getError();
	}
