<?php
session_set_cookie_params(1);
session_start();

const COUNTER_FILE = '/etc/pull-a-number.ini';

$ini = parse_ini_file(COUNTER_FILE, true);
$countervalue = intval($ini['number']['counter']);

$action = $_POST['action'] ?? null;
if($action == 'getnumber') {
//if(!isset($_SESSION['counter_ip'])) {
	$countervalue ++;
	$ini['number']['counter'] = $countervalue;
	write_ini_file(COUNTER_FILE, $ini);
	echo $countervalue; die();
	//$_SESSION['counter_ip'] = true;
//}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Ticketmachine</title>
		<style>
		* {
			box-sizing: border-box;
			font-family: monospace;
		}
		h1, h2, h3, p {
			text-align: center;
		}
		#ticketsystem {
			position: relative;
			width: 300px;
			height: 250px;
			display: block;
			margin-left: auto;
			margin-right: auto;
		}
		#ticketmachine {
			position: absolute;
			width: 300px;
			height: 100px;
			background-color: gray;
			border: 2px solid black;
			border-radius: 2px;
		}
		#ticketauswurf {
			position: absolute;
			width: 200px;
			height: 40px;
			left: 50px;
			top: 80px;
			background-color: lightgray;
			border: 1px solid gray;
			border-radius: 1px;
		}
		#ticket {
			display: none;
		}
		.ticket {
			display: inline-block;
			position: absolute;
			top: 0px;
			left: 75px;
			margin: auto;
			background-color: rgb(230,230,230);
			border-radius: 5px;
			border: 1px solid gray;
			font-family: "Segoe UI", sans-serif;
			font-size: 30px;
			padding: 10px 15px;
			transition: transform 0.4s ease;
		}
		#ticketmachine > .round-button {
			position: absolute;
			right: 0px;
			top: 0px;
			width: 94px;
			height: 94px;
		}
		.round-button-circle {
			width: 100%;
			height: 100%;
			border-radius: 50%;
			border: 10px solid #cfdcec;
			overflow: hidden;
			background: rgb(200,100,100);
			box-shadow: 0 0 3px gray;
			transition: all 0.1s ease;
		}
		.round-button-circle:hover {
			background: rgb(255,100,100);
			box-shadow: inset -5px -5px 20px rgba(255,255,255,0.1);
		}
		.round-button-circle:active {
			box-shadow: inset 5px 5px 20px rgba(0,0,0,0.7);
		}
		.round-button a {
			display: block;
			width: 100%;
			height: 100%;
			padding-top: 50%;
			padding-bottom: 50%;
			text-align: center;
			color: #e2eaf3;
			text-decoration: none;
		}
		</style>
		<script>
		var variation = 1;
		function randomIntFromInterval(min, max) { // min and max included 
			return Math.floor(Math.random() * (max - min + 1) + min);
		}
		function printTicket() {
			var amount = 1;
			if(randomIntFromInterval(1,6) == 4) amount = randomIntFromInterval(3,5);
			for(var i=0; i<amount; i++) {
				setTimeout(() => {
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function() {
						if(this.readyState == 4 && this.status == 200) {
							var clone = ticket.cloneNode(true);
							clone.id = null;
							ticketsystem.insertBefore(clone, ticket);
							clone.querySelectorAll('.ticketNo')[0].innerHTML = '#'+this.responseText;
							finalTransform = 'translateX('+randomIntFromInterval(-45*variation,45*variation)+'px) translateY('+randomIntFromInterval(150*variation,250*variation)+'px) rotate('+randomIntFromInterval(-10*variation,10*variation)+'deg)';
							clone.style = 'transform:'+finalTransform;
							clone.animate([
								{transform: 'translateX(  0px) translateY( 0px) rotate(0deg)'},
								{transform: 'translateX(  0px) translateY(80px) rotate(0deg)'},
								{transform: finalTransform},
								], {duration: 1000, iterations: 1}
							);
							if(variation < 2.5)
								variation += 0.2;
						} else if(this.readyState == 4) {
							alert(this.status+' '+this.statusText +"\n"+ this.responseText);
						}
					};
					xhttp.open('POST', window.location.href, true);
					xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					xhttp.send('action=getnumber');
				}, amount*i*60);
			}
		}
		</script>
	</head>
	<body>
		<h1>Bitte ziehen Sie eine Nummer.</h1>
		<p>
			Wir freuen uns Ihnen dank fortschreitender Technologie den Business Service "Nummer ziehen" nun auch digital anbieten zu können.
			<br>
			Bei uns ist jeder Kunde eine große Nummer.
		</p>
		<div id="ticketsystem">
			<div id="ticketauswurf">
			</div>
			<div id="ticket" class="ticket">
				<span class="ticketNo">#</span>
				<br>
				<img height="30" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAA1CAQAAABCKAnEAAAAAmJLR0QA/4ePzL8AAAAJcEhZcwAAFxIAABcSAWef0lIAAAAHdElNRQfhAQ0IAB4q2v8xAAABVUlEQVR42u3QLUskUBxG8UfL+LaIoFhkwyRNq2KwiIiDWJbpCxomaJkPsGDQZDMJVquIYhCDUcMg6MYty27ZIsuywxgUXzmGHQe/gKZzbrr/y71cfiH/1zofOKFeGqHMTXPWyAwhLEPW6KXGv6XhlwuEj/ziO4NUISv0cs7f3eKr8yI/edl8JYRxGjmlwEZrfsEgoY097jLLGI3m/DFfCKHMXbbp4IDrTDPBbY7oZIv7zBPCHNfZIIQBvkHIAYXm45/4U6/RwzqEXDJKCBWeQm4yRQifecgm3RxTL022fr4DIb8ZYoHHkIcsts5WIBVCGOEq+7QTujiEkDP6WIVU6ecHryjSHnuXhBZaaBNaaKElEFpoE1pooU1ooU1ooYU2oYU2oYUW2oQW2oQWWmgTWmgTWmihTWihTWihhTahhTahhRbahBbahBZaaBNaaBNaaKHtTXsGZP7VKNRD/98AAAAASUVORK5CYII=" />
			</div>
			<div id="ticketmachine">
				<div class="round-button"><div class="round-button-circle"><a href="#" class="round-button" onClick="printTicket()"></a></div></div>
			</div>
		</div>
	</body>
</html>

<?php
    function write_ini_file(string $file, array $array=[]) {
        // process array
        $data = array();
        foreach($array as $key => $val) {
            if(is_array($val)) {
                $data[] = "[$key]";
                foreach($val as $skey => $sval) {
                    if(is_array($sval)) {
                        foreach($sval as $_skey => $_sval) {
                            if(is_numeric($_skey)) {
                                $data[] = $skey.'[] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            } else {
                                $data[] = $skey.'['.$_skey.'] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            }
                        }
                    } else {
                        $data[] = $skey.' = '.$sval;
                    }
                }
            } else {
                $data[] = $key.' = '.$val;
            }
            // empty line
            $data[] = null;
        }

        // open file pointer, init flock options
        $fp = fopen($file, 'w');
        $retries = 0;
        $max_retries = 100;

        if(!$fp) {
            return false;
        }

        // loop until get lock, or reach max retries
        do {
            if($retries > 0) {
                usleep(rand(1, 5000));
            }
            $retries += 1;
        } while(!flock($fp, LOCK_EX) && $retries <= $max_retries);

        // couldn't get the lock
        if($retries == $max_retries) {
            return false;
        }

        // got lock, write data
        fwrite($fp, implode(PHP_EOL, $data).PHP_EOL);

        // release lock
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }
