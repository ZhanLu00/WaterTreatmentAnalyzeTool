

<?php 
// ini_set('display_errors',1);
// error_reporting(E_ALL); 
// //Turn off all error reports error_reporting(0); 
// ini_set('display_errors', true);
// error_reporting(E_ALL);

?>

<?php
	function convertTime($data){
	// To convert the format of time from xx:xx to x.x hour
		
		$counter = 0;
		$index = 0;
		$afterProcess = array();

		foreach ($data as $time){
			foreach ($time as $cha){
				$counter += 1;
				if ($cha == ":"){
					$theIndex = $counter;
				}
			}
			$before = substr($time, 0, $counter);
			$after = substr($time, $counter-1);
			$afterProcess[$index] = (int)$before + (int)$after/60;

			$counter = 0;
			$index += 1;
		}
		return $afterProcess;
	}




	function inputProcess($data){
	// To convert the input data from string to array
		
		$afterProcess = array();
		$eachData = "";
		$totalData = 0;
		$checkTime = false;
		$data = trim($data);
		for ($i = 0; $i < strlen($data); $i+=1){
			$cha = substr($data, $i, 1);
			$eachData = $eachData.$cha;

			if (ctype_space($cha)){ 
				if (!$checkTime){
					$afterProcess[] = (float)$eachData;
				}else{
					$afterProcess[] = $eachData;
				}
				$eachData = "";
				$totalData += 1;
			}elseif ($cha == ":"){
				$checkTime = true;
			}
		}
		// add the last one
		if ($eachData != " "){
			if (!$checkTime){
				$afterProcess[$totalData] = (float)$eachData;
			}else{
				$afterProcess[$totalData] = $eachData;
			}
		}

		// Convert data if it is time
		if ($checkTime){
			$afterProcess = convertTime($afterProcess);
		}

		return $afterProcess;
	}

	function unitConversionP1($tUnit, $time, $qUnit, $flowrate){
	// to convert and uniform the time unit 
	// time and floarate will be array list
	// tUnit will be day,hour, min
	// qUnit will be m^3/hr, min, 
		// $convertion = array("day"=>24, "hour"=>60, "minute"=>60, "second"=>60);
		// $convertion2 = array("day", "hour", "minute", "second");
		$convertion = array(
			array("day", 24),
			array("hour", 60),
			array("minute", 60),
			array("second", 60)
			);
		$target = $tUnit;
		$current = substr($qUnit, stripos($qUnit, "/")+1);
		$convertData = $flowrate;

		
		if ($target == $current){
			return $convertData;
		}else{
			//find each of the position in the convertion chart
			foreach ($convertion as $key => $value) {
				if ($value[0] == $target) {
					$targetInd = $key;
				}elseif ($value[0] == $current) {
					$currentInd = $key;
				}
			}

			while ($target != $current) {
				if ($targetInd < $currentInd) {
					$currentInd -= 1;
					$factor = $convertion[$currentInd][1];

				}else{
					$currentInd += 1;
					$factor = 1/$convertion[$currentInd][1];
				}
				foreach ($convertData as $key => $value) {
					$convertData[$key] = $value*$factor;	
				}
				$current = $convertion[$currentInd][0];
				
			}
			
			return $convertData;
		}		

	}

	function computeVolumeP1($time, $data, $ave){
	// To compute the average of a given data in the given period of time
	//	time and data will be list array which contains integar only
	// updated of the func, add $ave => flowrate average

		$length = count($time);
		$volume = array();
		$volume[0] = 0;

		for ($i = 0; $i<$length-1; $i+=1){
			$x = (1/2*($data[$i]+$data[$i+1]) - $ave) * ($time[$i+1]-$time[$i]) + $volume[$i];
			// $x= 1/2*($data[$i+1]+$data[$i]-$ave)*($time[$i+1]-$time[$i]);
			$volume[$i+1] = $x;
		}

		$result = array();
		
		foreach ($volume as $key => $value) {
			$result[$key] = round(($volume[$key] - min($volume)), 3);
		}
		return $result;
	}

	

	function runPart1($tUnit, $time, $qUnit, $flowrate){
	// To compute the result of part 1
	// both input are strings
		$time1 = inputProcess($time);
		$flowrate1 = inputProcess($flowrate);
		$flowrate1 = unitConversionP1($tUnit, $time1, $qUnit, $flowrate1);
		$flowrateOut = computeAverageP23($time1, $flowrate1);
		$result = computeVolumeP1($time1, $flowrate1, $flowrateOut);
		if ($_POST["formName"] == "part1") {
			echo implode("?", $time1);
			echo "^";
			echo implode("?", $result);
			echo "^";
			echo implode("?", $flowrate1);
			echo "^";
			echo $flowrateOut;
			echo "#";
			return max($result).substr($qUnit, 0,1)."<sup>3</sup>".$flowrateOut.substr($qUnit,0,1)."<sup>3</sup>/".substr($tUnit,0);
		}
	}
?>



<?php
	function computeAverageP23($time, $data){
	// To compute the average of the given data in the given period of time
	// Time and data will be list array which contains integar only
		$totalTime = 0;  //number
		$totalData = 0;  //number
		$repeatTimes = count($time)-1; // this is when the end time is same as the begineering tims

		
		for ($i = 0; $i < $repeatTimes; $i+=1){
			// notice the time
			$t = $time[$i+1]-$time[$i];
			$integalOfData = 1/2*($data[$i+1]+$data[$i])*($t);

			$totalTime += $t;
			$totalData += $integalOfData;
		}
		return round($totalData/$totalTime, 4);

	}

	function unitConverstionP2($time, $concentration, $flowrate, $tU, $cU, $qU){
	// to uniform the unit of all three parameters
		// 1. convert time and flowrate
		$timeCon = array(
			array("day", 24),
			array("hour", 60),
			array("minute", 60),
			array("second", 60)
			);
		$target = $tU;
		$current = substr($qU, stripos($qU, "/")+1);
		$convertQ = $flowrate;

		
		if ($target == $current){
			return $convertData;
		}else{
			//find each of the position in the convertion chart
			foreach ($timeCon as $key => $value) {
				if ($value[0] == $target) {
					$targetInd = $key;
				}elseif ($value[0] == $current) {
					$currentInd = $key;
				}
			}

			while ($target != $current) {
				if ($targetInd < $currentInd) {
					$currentInd -= 1;
					$factor = $timeCon[$currentInd][1];

				}else{
					$currentInd += 1;
					$factor = 1/$timeCon[$currentInd][1];
				}
				$convertQ = $convertQ*$factor;	
				$current = $timeCon[$currentInd][0];
			}
			return $convertQ;
		}		
		// 2. convert flowrate and concentration
		// need to discuss with peng
		// $concenCon = array(
		// 	array(""))
	}

	function cycleOnce($time, $c0, $q, $v, $c11){
	// To simulate the cycle through one period
		$theta = $v/$q;
		$c1 = array();
		$l = count($time);	
		$c12 = 0;

		// this works when the initial and the end points are the same
		// make sure to make change on the problem above or note on the website
		for ($i = 0; $i < $l-1; $i+=1){
			if ($i == 0){
				$c1[$i] = $c11;
			}

			$c12 = 1/$theta * ($c0[$i] - $c1[$i]) * ($time[$i+1] - $time[$i])+ $c1[$i];

			//echo "at this time: ".$time[$i]."  with this concentration0: ".$c0[$i]." with this c11: ".$c1[$i]." get this c12: ".$c12."<br>";

			$c1[$i+1] = round($c12, 4);
			
		}

		return $c1;
	}



	function cycleThrough($time, $c0, $q, $v){
	// To simulate the cycle until the become similar
	// Question here is that the begining sometimes is not predictable 	
		$cycle = 0;
		$similar = false;
		$similarity = 0.05;
		// $cycle1 = array(); 
		// $cycle2 = array();
		// $l = 0;


		while (!$similar and $cycle != 30) {
			if ($cycle == 0){
				$cycle += 2;
				$cycle1 = cycleOnce($time, $c0, $q, $v, computeAverageP23($time,$c0));
			}else{
				$cycle += 1;
				$cycle1 = $cycle2;
			}

			// echo "cycle1: ";
			// echo "<br>";
			// echo implode(" ", $cycle1);
			$cycle2 = cycleOnce($time, $c0, $q, $v, $cycle1[count($cycle1)-1]);
			// echo "<br>cycle2: ";
			// echo implode(" ", $cycle2);
			// echo "<br>";

			$similar = true;
			for ($i = 0; $i < count($cycle1); $i+=1){
				if (abs($cycle1[$i] - $cycle2[$i])/$cycle1[$i] > $similarity){ 
				//this number is might cause error 
					$similar = false;
				}
			}
			// if ($similar){
			// 	echo "true";
			// }else{
			// 	echo "false";
			// }
			// echo "<br>";
		}
		return array($cycle2, $cycle);
	}

	function computeE1($time, $c1){
	// compute e
		$cAverage = computeAverageP23($time, $c1);
		//echo "cAverage: ".$cAverage." outflow c1 is: ".implode(" ", $c1)."<br>";
		$percentDiff = array();

		foreach ($c1 as $key => $value) {
			$percentDiff[$key] = abs($cAverage-$value)/$cAverage;
		}
		return $percentDiff;
		
	}

	function compareE($e, $c1, $time){
	// compare E to the tolerance
	// returns true if e is larger => volume is too small
	// returns false if e is smaller => volume is too large
		$data = computeE1($time, $c1);
		foreach ($data as $key => $percentage) {
			if ($percentage > $e) {
				return true;
			}
		}
		return false;
	}

	function findRange($time, $c0, $e, $q){
	// to find the range of the volume
		// initial volume is detention time 5 * q
		$v = 5 * $q;
		$rangeFound = false;

		while (!$rangeFound) {
			$cycleResult = cycleThrough($time, $c0, $q, $v);
			$cycleResultC1 = $cycleResult[0];
			$cycleTimes = $cycleResult[1];
			
			if ($v == 5*$q) {
				$v1 = $v;
				$v2 = $v;
				$result1 = compareE($e, $cycleResultC1, $time);
				$result2 = $result1;
			}else{
				$v1 = $v2;
				$v2 = $v;
				$result1 = $result2;
				$result2 = compareE($e, $cycleResultC1, $time);
			}
			
			// compute the next v
			if ($result2) {
				//echo "smaller";
				$v *= 2;
			}else{
				//echo "larger";
				$v /= 2;
			}

			//echo "<br>";


			//to check found or not
			if ($result1 != $result2) {
				if ($result1 == false) {
					return array($v2, $v1);
				}else{
					return array($v1, $v2);
				}
			}
		}
	}


	function binarySearch($c0, $time, $q, $e){
	// find the range first, then use binary search to find the value for volume
		$range = findRange($time, $c0, $e, $q);
		$lowerLimit = $range[0];
		$higherLimit = $range[1];
		$found = false;

		// be careful about this value below
		// be careful about the steps here, might be too samll or decimals
		$similarity = 0.5 * $e;
		$range  = range(floor($lowerLimit), ceil($higherLimit), 1000);
		
		// if ($e >= 1) {
		// 	return "please try a smaller $e";
		// }

		while (!$found) {
			$testValue = $range[floor(count($range)/2)];
			$cycleResult = cycleThrough($time, $c0, $q, $testValue);
			$cycleResultC1 = $cycleResult[0];
			$cycleTimes = $cycleResult[1];
			$dataE = computeE1($cycleResultC1, $time);

			// echo $testValue;
			// echo "<br>";			
			if (compareE($e, $cycleResultC1, $time)) {
				// echo "yes";
				$range = array_slice($range, floor(count($range)/2));
			}else{
				// echo "okay";
				$range = array_slice($range, 0, floor(count($range)/2));
			}
			// echo count($range)."<br>";

			if (abs($e-max($dataE))/$e <= $similarity) {
				$found = true;
				return array($testValue, $cycleResultC1);
			}elseif (count($range) <= 10) {
				return array(array_sum($range)/count($range), $cycleResultC1);
			}
		}


	}

	function runPart2($tU, $time, $cU, $concen, $qU, $flowrate, $tolerance){
		// convert the format of data
		$t = inputProcess($time);
		$con = inputProcess($concen);
		$timeU = trim($tU);
		$conU = trim($cU);
		$qU = trim($qU);
		$q = (float)trim($flowrate);
		$q = unitConverstionP2($t, $con, $q, $timeU, $conU, $qU);
		$toler = (float)trim($tolerance)/100;


		$result = binarySearch($con, $t, $q, $toler);
		// print_r($result);
		// get data back for plotting
		echo implode("?", $t);
		echo "^";
		echo implode("?", $con);
		echo "^";
		echo implode("?", $result[1]);
		echo "^";
		echo "#";
		echo $result[0];
		
	}
?>


<?php

	function cycle($time, $c0, $q, $v, $c11){
	// To simulate the cycle through one period
		
		$c1 = array();
		$l = count($time);
		

		for ($i = 0; $i < $l-1; $i+=1){
			if ($i == 0) {
				$c1[] = $c11;
			}

			$value =($q[$i] * ($c0[$i] - $c1[$i]) * ($time[$i+1] - $time[$i]) + $v[$i] * $c1[$i]) / $v[$i];
			if ($value < 0) {
				echo "<br>inflow:".$q[$i]."<br>inConcen".$c0[$i]."<br>outCon".$c1[$i]."<br>volume".$v[$i];
			}
			$c1[] = ($q[$i] * ($c0[$i] - $c1[$i]) * ($time[$i+1] - $time[$i]) + $v[$i] * $c1[$i]) / $v[$i];


		}
		// return an array with all the c1
		return $c1;
	}

	function cycles($time, $c0, $q, $v){
	// To simulate the cycle until the become similar
	// Question here is that the begining sometimes is not predictable 	
		$cycle = 0;
		$similar = false;
		// make sure to change this number later
		$similarity = 0.01;


		while (!$similar) {
			switch ($cycle) {
			 	case 0:
			 		$cycle += 2;
					$cycle1 = cycle($time, $c0, $q, $v, computeAverageP23($time,$c0));
					$cycle2 = cycle($time, $c0, $q, $v, $cycle1[count($cycle1)-1]);
			 		break;

			 	default:
				 	$cycle += 1;
					$cycle1 = $cycle2;
					$cycle2 = cycle($time, $c0, $q, $v, $cycle1[count($cycle1)-1]);


					for ($i = 0; $i < count($cycle1); $i+=1){
						if (abs($cycle1[$i] - $cycle2[$i])/$cycle1[$i] < $similarity){ 
						//this number is might cause error 
							$similar = true;
						}
					}
					break;
			}
		}
		return array($cycle2, $cycle);
	}



	function findRange3($time, $c0, $e, $q, $v){
	// to find the range of the volume
		$rangeFound = false;
		$increase = 0;

		while (!$rangeFound) {
			// simulate cycles and get result
			$cycleResult = cycles($time, $c0, $q, $v);
			$cycleResultC1 = $cycleResult[0];
			$cycleTimes = $cycleResult[1];
			
			// compute increase
			if ($result2) {
				$increase += max($v);
				foreach ($v as $key => $value) {
					$v[$key] = $value + $increase;
				}
			}

			// store value 
			if ($increase == 0) {
				$v1 = $increase;
				$v2 = $increase;
				$result1 = compareE($e, $cycleResultC1, $time);
				$result2 = $result1;
			}else{
				$v1 = $v2;
				$v2 = $increase;
				$result1 = $result2;
				$result2 = compareE($e, $cycleResultC1, $time);
			}
			

			//to check found or not
			if ($result1 != $result2) {
				if ($result1 == false) {
					return array($v2, $v1);
				}else{
					return array($v1, $v2);
				}
			}
		}
	}


	function binarySearch3($time, $c0, $q, $e){
	// find the range first, then use binary search to find the value for volume
	// 1. check if minimum volume would works
	// 2. if not, find range
	// 3. apply binary search to the range

		// 1
		$v = computeVolumeP1($time, $q, computeAverageP23($time, $q));
		if (compareE($e, cycle($time, $c0, $q, $v, computeAverageP23($time, $c0)), $time)){
			return $v;
		}
		
		// 2
		$range = findRange3($time, $c0, $e, $q, $v);
		$lowerLimit = $range[0];
		$higherLimit = $range[1];

		// 3
		//echo $lowerLimit.$higherLimit;
		$found = false;
		// be careful about this value below
		$similarity = 0.1 * $e;
		$range  = range(floor($lowerLimit), ceil($higherLimit));
		

		while (!$found) {
			$testValue = $range[floor(count($range)/2)];
			$cycleResult =  cycles($time, $c0, $q, $v);
			$cycleResultC1 = $cycleResult[0];
			$cycleTimes = $cycleResult[1];
			$dataE = computeE1($cycleResultC1, $time);

			if (compareE($e, $cycleResultC1, $time)) {
				$range = substr($range, floor(count($range)/2));
			}else{
				$range = substr($range, 0, floor(count($range)/2));
			}

			if (($e-max($dataE))/$e <= $similarity) {
				$found = true;
				return array($testValue,$cycleResultC1);
			}elseif (count($range) <= 10) {
				return array(array_sum($range)/count($range), $cycleResultC1);
			}
		}


	}


	function runPart3($tU, $time, $cU, $concen, $qU, $flowrate, $tolerance){
		//input process, convert to useable data for later use
		$timeU = trim($tU);
		$conU = trim($cU);
		$flowU = trim($qU);
		$toler = ((float)trim($tolerance))/100;
		$t = inputProcess($time);
		$con = inputProcess($concen);
		$flow = inputProcess($flowrate);
		$flow = unitConversionP1($timeU, $t, $flowU, $flow);
		// make sure to construct a function for this later
		//$con = unitConversionP1($tU, $time, $cU, $concen);
		
		$flowrateOut = computeAverageP23($t, $flow);
		$initialV = computeVolumeP1($t, $flow, $flowrateOut);
		//$initialV = max($initialV)- min($initialV);
		$initialC = computeAverageP23($t, $con);
		$test = cycle($t, $con, $flow, $initialV, $initialC);
		print_r($initialV);

		$result = binarySearch3($t, $con, $flow, $toler);
		//return $result[0];
		return $test;
	}
?>


<?php

	if (isset($_POST["submit"])) {
	// check if $_post has the key submit or not ( check if the form has been submit or not)

		// assign value from the submition
		if ($_POST["formName"] == "part1"){
			$tUnit = $_POST['tUnit'];
			$time = $_POST['time'];
			$qUnit = $_POST['qUnit'];
			$flowrate = $_POST['flowrate'];

			$result = runPart1($tUnit, $time, $qUnit, $flowrate);
			echo "<p>".$result."</p>";

		}elseif ($_POST["formName"] == "part2") {
			$tUnit = $_POST['tUnit'];
			$time = $_POST['time'];
			$cUnit = $_POST['cUnit'];
			$concentration = $_POST['concentration'];
			$qUnit = $_POST['qUnit'];
			$flowrate = $_POST['flowrate'];
			$tolerance = $_POST['tolerance'];
			
			$result = runPart2($tUnit, $time, $cUnit, $concentration, $qUnit, $flowrate, $tolerance);

		}elseif ($_POST["formName"] == "part3"){
			$tUnit = $_POST['tUnit'];
			$time = $_POST['time'];
			$cUnit = $_POST['cUnit'];
			$concentration = $_POST['concentration'];
			$qUnit = $_POST['qUnit'];
			$flowrate = $_POST['flowrate'];
			$tolerance = $_POST['tolerance'];

			$result = runPart3($tUnit, $time, $cUnit, $concentration, $qUnit, $flowrate, $tolerance);
			print_r($result);
		}
		
	}
?>

