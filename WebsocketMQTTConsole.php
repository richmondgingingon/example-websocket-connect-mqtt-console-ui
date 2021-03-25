<html>
	<head>
		<meta charset="utf-8"/>
      	<title>JavaScript MQTT WebSocket Example</title>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
		<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

		<script type="text/javascript">	
			var server = "m23.cloudmqtt.com";	
			var port = 32084;	
			var user = "giysjyxm";	
			var pass = "N1E4rKYJiJhS";	
			var topic = "cloud_mqtt";

			// Create a client instance
			client = new Paho.MQTT.Client(server, port, "mqttclient");

			// set callback handlers
			client.onConnectionLost = onConnectionLost;
			client.onMessageArrived = onMessageArrived;

			var options = 
			{				
				userName: user,
				password: pass,
				onSuccess:onConnect,
				onFailure:doFail
			}
		
			// connect the client
			client.connect(options);
		
			// called when the client connects
			function onConnect() 
			{
				// Once a connection has been made, make a subscription and send a message.				
				console.log("Set Topic Subscribe to : "+topic);
				client.subscribe(topic);
				message = new Paho.MQTT.Message("Hello CloudMQTT");
				message.destinationName = topic;
				client.send(message);				
			}
		
			function doFail(e)
			{
				console.log(JSON.stringify(e));;
			}
		
			// called when the client loses its connection
			function onConnectionLost(responseObject) 
			{
				if (responseObject.errorCode !== 0) 
				{
					console.log("onConnectionLost:"+responseObject.errorMessage);
				}
			}
		
			// called when a message arrives
			function onMessageArrived(message) 
			{
				console.log("Message Arrived : "+message.payloadString);
			}
		</script>
   	</head>
	<body>
   	</body>	
</html>