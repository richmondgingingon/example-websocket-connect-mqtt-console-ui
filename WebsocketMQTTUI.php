
<!DOCTYPE html>
<html>
	<style>
		body
		{
			padding : 10px;
			margin : 10px;
		}
		#messages
		{
			background-color : yellow;
			font-size : 3;
			font-weight : bold;
			line-height : 140%;
		}
		#status
		{	
            background-color : red;		
			font-size : 4;
			font-weight : bold;
			color : white;
			line-height : 140%;
		}
        label
        {
            display : inline-block;
            width : 110px;
        }
        #connect input[type = "text"], #subscribe input[type = "text"], #publish input[type = "text"]
        {
            margin-left : 20px;
            padding-left : 5px;
            padding-right : 5px;
            width : 120px;
            height : 25px;
            margin-bottom : 5px;           
        }
        #connect input[type = "checkbox"], #publish input[type = "checkbox"]
        {
            margin-left : 20px;
            float : center;
        }
        input[type="submit"]
        {
            float : left;
        }
		/* The switch - the box around the slider */
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 60px;
		  height: 34px;
		}

		/* Hide default HTML checkbox */
		.switch input 
		{
		  opacity: 0;
		  width: 0;
		  height: 0;
		}

		/* The slider */
		.slider 
		{
		  position: absolute;
		  cursor: pointer;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  background-color: #ccc;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		.slider:before 
		{
		  position: absolute;
		  content: "";
		  height: 26px;
		  width: 26px;
		  left: 4px;
		  bottom: 4px;
		  background-color: white;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		input:checked + .slider 
		{
		  background-color: #2196F3;
		}

		input:focus + .slider 
		{
		  box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before 
		{
		  -webkit-transform: translateX(26px);
		  -ms-transform: translateX(26px);
		  transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider.round 
		{
		  border-radius: 34px;
		}

		.slider.round:before 
		{
		  border-radius: 50%;
		}
	</style>
	<head>
		<meta name = "viewport" content = "width=device-width, initial-scale=1.0">
		<meta charset="UTF-8" >

	  	<title>Websockets Using JavaScript MQTT Client</title>

		<script type = "text/javascript" src = "https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js"></script>
		<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	  
		<script type = "text/javascript">

			var connected_flag = 0;	
			var mqtt;
	    	var reconnectTimeout = 2000;
			var row = 0;
			var host ;
			var port;
			var clean_sessions;
			var username;
			var password;
			var out_msg = "";
			var mcount = 0;

			function onConnectionLost()
			{
                console.log("Connection Lost");
                
                document.getElementById("status").innerHTML = "Connection Status : Connection Lost";
                document.getElementById("status").style.backgroundColor = "red";
                document.getElementById("status_messages").innerHTML = "Connection Lost";
                
				connected_flag = 0;
			}

			function onFailure(message) 
			{
                console.log("Failed");
                
                document.getElementById("status_messages").innerHTML = "Connection Failed - Retrying";
                
			    setTimeout(MQTTconnect, reconnectTimeout);
			}

			function onMessageArrived(r_message)
			{				
				out_msg = "Topic : "+r_message.destinationName +"<br>";
                out_msg = out_msg+"Message received : "+r_message.payloadString+"<br>";
				out_msg = "<b>"+out_msg+"</b><br>";

				try
				{
					document.getElementById("out_messages").innerHTML += out_msg;
				}
				catch(err)
				{
					document.getElementById("out_messages").innerHTML = err.message;
				}
			
				if (row==10)
				{
					row=1;
					document.getElementById("out_messages").innerHTML = out_msg;
				}
				else{
					row += 1;
                    mcount += 1;
                    
					console.log(mcount +"  "+ row);
				}

				if(r_message.payloadString == "on")
				{
					document.getElementById("switch_toggle").checked = true;
				}
				else if(r_message.payloadString == "off")
				{
					document.getElementById("switch_toggle").checked = false;
				}
			}

			function onConnected(recon, url)
			{
				console.log(" in onConnected " + reconn);
			}

			function onConnect() 
			{
				
			  	// Once a connection has been made, make a subscription and send a message.
                document.getElementById("status_messages").innerHTML = "Connected to "+ host +" on port "+ port;
                
                connected_flag=1;
                
                document.getElementById("status").innerHTML = "Connection Status : Connected";
                document.getElementById("status").style.backgroundColor = "green";
                
				console.log("on Connect "+ connected_flag);
			}

			function disconnect()
			{
				if (connected_flag == 1)
					mqtt.disconnect();
			}
			  
			function MQTTconnect() 
			{
				clean_sessions = document.forms["connect"]["clean_sessions"].value;
				username = document.forms["connect"]["username"].value;
				password = document.forms["connect"]["password"].value;
				host = document.forms["connect"]["server"].value;
				port = document.forms["connect"]["port"].value;

				console.log("clean = "+ clean_sessions);
				
				if (clean_sessions = document.forms["connect"]["clean_sessions"].checked)
					clean_sessions = true
				else
					clean_sessions = false

				document.getElementById("status_messages").innerHTML = "";
				
				if (port != "")
				{
					port = parseInt(port);
				}

				if (host != "")
				{
					//host=s;
					console.log("host");
				}

				console.log("connecting to "+ host +" "+ port +" clean session = "+ clean_sessions);
				console.log("username =  "+ username);

				document.getElementById("status_messages").innerHTML = 'connecting';

				mqtt = new Paho.MQTT.Client(host, port,"mqttclient");

				//document.write("connecting to "+ host);
				var options = 
				{
				    timeout: 3,
					useSSL: true,
					userName: username,
					password: password,
					cleanSession: clean_sessions,
					onSuccess: onConnect,
					onFailure: onFailure,
				};

				if (username != "")
					options.userName=document.forms["connect"]["username"].value;

				if (password != "")
					options.password=document.forms["connect"]["password"].value;
			   
				mqtt.onConnectionLost = onConnectionLost;
				mqtt.onMessageArrived = onMessageArrived;
				mqtt.onConnected = onConnected;
				mqtt.connect(options);
			
				return false;		   			   
			}

			function sub_topics()
			{
				document.getElementById("status_messages").innerHTML ="";

				if (connected_flag == 0)
				{
					out_msg="<b>Not Connected so can't subscribe</b>"

					console.log(out_msg);

					document.getElementById("status_messages").innerHTML = out_msg;

					return false;
				}

				var stopic = document.forms["subs"]["Stopic"].value;				
				var sqos = parseInt(document.forms["subs"]["sqos"].value);

				console.log("here");

				if (sqos > 2)
					sqos = 0;

				console.log("Subscribing to topic = "+ stopic +" QOS " + sqos);

				document.getElementById("status_messages").innerHTML = "Subscribing to topic = "+ stopic;

				var soptions=
				{
					qos:sqos,
				};

				mqtt.subscribe(stopic, soptions);

				return false;
			}

			function send_message()
			{
				document.getElementById("status_messages").innerHTML = "";				

				if (connected_flag == 0)
				{
                    out_msg = "<b>Not Connected so can't send</b>"
                    
                    console.log(out_msg);
                    
                    document.getElementById("status_messages").innerHTML = out_msg;
                    
					return false;
				}

				var pqos=parseInt(document.forms["smessage"]["pqos"].value);

				if (pqos > 2)
					pqos = 0;

				var msg = document.forms["smessage"]["message"].value;

				console.log("Message sent = "+ msg);

				document.getElementById("status_messages").innerHTML = "Sending message : "+ msg;

				var topic = document.forms["smessage"]["Ptopic"].value;
				//var retain_message = document.forms["smessage"]["retain"].value;

				if (document.forms["smessage"]["retain"].checked)
					retain_flag = true;
				else
					retain_flag = false;

				message = new Paho.MQTT.Message(msg);

				if (topic == "")
					message.destinationName = "test-topic";
				else
					message.destinationName = topic;

				message.qos = pqos;
				message.retained = retain_flag;
				mqtt.send(message);

				return false;
			}

			function send_switch_state()
			{
				var topic = "send";

				if (switch_val = document.getElementById("switch_toggle").checked)
				{
					switch_val = "on";

					//document.getElementById("status_messages").innerHTML = "Switch state : "+ switch_val;

					console.log("switch state = "+ switch_val);

					message = new Paho.MQTT.Message(switch_val);					
					
					message.destinationName = topic;

					document.getElementById("status_messages").innerHTML = "Sending message : "+ switch_val;

					mqtt.send(message);					

					return false;
				}
				else
				{
					switch_val = "off";

					//document.getElementById("status_messages").innerHTML = "Switch state : "+ switch_val;

					console.log("switch state = "+ switch_val);

					message = new Paho.MQTT.Message(switch_val);
					
					message.destinationName = topic;

					mqtt.send(message);

					document.getElementById("status_messages").innerHTML = "Sending message : "+ switch_val;

					return false;
				}	
			}			
	  </script>
	</head>
	<body>
		<h1>Websockets MQTT UI</h1>
		<script type = "text/javascript"></script>		
		<div id="status">
            Connection Status : Not Connected        
        </div><br>
		<table>
			<tr>
				<td id="connect" width="300" >
					<form name = "connect" action ="" onsubmit = "return MQTTconnect()">
						<label>Server:</label>
                            <input type = "text" name = "server" ><br><br>
						<label>Port:</label>
                            <input type = "text" name = "port"><br><br>
						<label>Clean Session:</label>
                            <input type = "checkbox" name = "clean_sessions" value = "true" checked><br><br>
						<label>Username:</label>
                            <input type = "text" name = "username" ><br><br>
						<label>Password:</label>
                            <input type = "text" name = "password" ><br><br>
						<input name = "conn" type = "submit" value = "Connect">
						<input type = "button" name = "discon " value = "Disconnect" onclick = "disconnect()">
					</form>
				</td>
				<td id="subscribe" width = "300">
					<form name = "subs" action = "" onsubmit = "return sub_topics()">
                        <label>Subscribe Topic:</label>
                            <input type = "text" name = "Stopic"><br><br>
                        <label>Subscribe QOS:</label>
                            <input type = "text" name = "sqos" value = "0"><br><br>
						<input type = "submit" value = "Subscribe">
					</form> 
				</td>
				<td id="publish" width = "300">
					<form name = "smessage" action = "" onsubmit = "return send_message()">						
                        <label>Message:</label>
                            <input type = "text" name = "message"><br><br>
                        <label>Publish Topic:</label>
                            <input type = "text" name = "Ptopic"><br><br>
                        <label>Publish QOS:</label>
                            <input type = "text" name = "pqos" value = "0"><br><br>
                        <label>Retain Message:</label>
                            <input type = "checkbox" name = "retain" value = "true" ><br><br>
						<input type="submit" value = "Submit">
					</form>
				</td>
			</tr>
		</table>
		<br><br>
		<!-- Rounded switch -->	
		Testing Switch Toggle	<br>
		<div id = "switch">
			<label class="switch"> 		
				<input type="checkbox" id="switch_toggle" onclick = "send_switch_state()">
			  	<span class="slider round"></span>
			</label>
		</div>

		<br><br><br>
		Status Messages :
		
		<div id = "status_messages"></div><br>
		
		Received Messages :
		<div id = "out_messages"></div>
	
		<script>
			
		</script>
	</body>
</html>
