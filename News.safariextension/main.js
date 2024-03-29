var messages = [];
var currIndex = 0;
var maxAge = safari.extension.settings.timeSpan || "1d";

safari.extension.settings.addEventListener("change", function(e) {
	if (e.key == "timeSpan") {
		maxAge = e.newValue;
		
		reload();
	}
}, false);

function process() {
	window.devicePixelRatio = window.devicePixelRatio || 1;
	
	reload(function() {
		loop();
		setInterval(loop, 9200);
		setInterval(update, 1000 * 60 * 10);
	});
}

function reload(callback) {
	var xml = new XMLHttpRequest();
	xml.open("GET", "http://aseider.pf-control.de/newsAPI/?maxAge=" + (maxAge || "1d"), true);
	xml.setRequestHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0.1 NewsBar/602.2.14');
	xml.onreadystatechange = function() {
		if (xml.readyState == 4) {
			var ret = JSON.parse(xml.response);

			messages = ret;
			
			for (var i in messages) {
				var message = messages[i];
				message.date = new Date(message.date);
			}
			
			messages.sort(function(a, b) {
				return b.date.getTime() - a.date.getTime();
			});
			
			if (callback)
				callback();
		}
	};
	
	xml.onprogress = function(e) {
		console.dir(e.loaded);
	};

	xml.send();
}

function getIconSrc(iconInfo) {
	if (window.devicePixelRatio == 2) {
		return iconInfo.url2;
	}
	
	return iconInfo.url;
}

function getTimeString(date) {
	var delta = (new Date).getTime() - date.getTime();
	var secs = (delta / 1000).toFixed(0);
	
	if (secs < 60) {
		return secs + " s";
	}
	
	var min = (secs / 60).toFixed(0);
	
	if (min < 60) {
		return min + " min";
	}
	
	var hours = (min / 60).toFixed(0);
	
	if (hours < 24) {
		return hours + " std";
	}
	
	var days = (hours / 24).toFixed(0);
	
	if (days < 7) {
		return days + " t";
	}
	
	var weeks = (days / 7).toFixed(0);
	
	return weeks + " w";
}

function loop() {
	var newDisplay = document.createElement("div");
	newDisplay.className = "display";
	newDisplay.style.top = "100%";
	
	if (messages.length == 0) {
		document.body.innerHTML = ""; // remove all messages
		newDisplay.style.top = 0; // no animation will take place
		
		var newContent = document.createElement("div");
		newContent.className = "content";
		newContent.appendChild(document.createTextNode("No messages to display"));
		newContent.style.color = "#222";
		
		newDisplay.appendChild(newContent);
		document.body.appendChild(newDisplay);
		
		return;
	}
	
	currIndex = ++currIndex % messages.length;
	var newMessage = messages[currIndex];

	var newContent = document.createElement("div");
	newContent.className = "content";
	
	var iconInformation = newMessage.icon;
	var icon = document.createElement("img");
	icon.src = getIconSrc(iconInformation);
	icon.width = iconInformation.width;
	icon.height = iconInformation.height;
	icon.alt = iconInformation.alt;
	icon.title = iconInformation.alt;
	icon.style.verticalAlign = "-8%";
	icon.style.opacity = 0.7;
	
	var time = document.createElement("i");
	time.appendChild(document.createTextNode("\t(" + getTimeString(newMessage.date) + ")"));
	time.style.color = "gray";
	
	newContent.appendChild(icon);
	newContent.innerHTML += "&nbsp;";
	newContent.appendChild(document.createTextNode("\t " + newMessage.title));
	newContent.appendChild(time);

	newDisplay.appendChild(newContent);

	document.body.appendChild(newDisplay);

	setTimeout(function() {
		document.getElementsByClassName("display")[0].style.top = "-100%";
		newDisplay.style.top = "0";

		setTimeout(function() {
			document.body.removeChild(document.getElementsByClassName("display")[0]);
		}, 620);
	}, 0);
}

function update() {
	reload(function() {
		
	});
}

function clicked() {
	safari.application.activeBrowserWindow.openTab().url = messages[currIndex].url;
}
