const bars = safari.extension.bars;
const activeBrowserWindow = safari.application.activeBrowserWindow;
for (var i = 0; i < bars.length; ++i) {
	var bar = bars[i];
    console.log(bar);
    if (bar.browserWindow === activeBrowserWindow) {
	    var xml = new XMLHttpRequest();
		xml.open("GET", "http://aseider.pf-control.de/newsAPI/", true);
		xml.onreadystatechange = function() {
			if (xml.readyState == 4) {
				var ret = JSON.parse(xml.response);
				
				document.getElementsByClassName("content")[0].innerHTML = ret[0];
			}
		};
		
		xml.send();
    }
}
