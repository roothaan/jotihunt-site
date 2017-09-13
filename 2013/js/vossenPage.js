var geocoder = typeof google !== 'undefined' ? new google.maps.Geocoder() : null;
var mapmarker = false;
function coordRD(){
	var x = document.getElementById("x").value;
	while(parseInt(x) < 100000){ x = x*10; }
	document.getElementById("x").value = x;
	var y = document.getElementById("y").value;
	while(parseInt(y) < 100000){ y = y*10; }
	document.getElementById("y").value = y;
	var f; var l;

	x0  = 155000.000;
	y0  = 463000.000;

	f0 = 52.156160556;
	l0 =  5.387638889;

	a01=3236.0331637 ; b10=5261.3028966;
	a20= -32.5915821 ; b11= 105.9780241;
	a02=  -0.2472814 ; b12=   2.4576469;
	a21=  -0.8501341 ; b30=  -0.8192156;
	a03=  -0.0655238 ; b31=  -0.0560092;
	a22=  -0.0171137 ; b13=   0.0560089;
	a40=   0.0052771 ; b32=  -0.0025614;
	a23=  -0.0003859 ; b14=   0.0012770;
	a41=   0.0003314 ; b50=   0.0002574;
	a04=   0.0000371 ; b33=  -0.0000973;
	a42=   0.0000143 ; b51=   0.0000293;
	a24=  -0.0000090 ; b15=   0.0000291;

	with(Math){
	dx=(x-x0)*pow(10,-5);
	dy=(y-y0)*pow(10,-5);

	df =a01*dy + a20*pow(dx,2) + a02*pow(dy,2) + a21*pow(dx,2)*dy + a03*pow(dy,3)
	df+=a40*pow(dx,4) + a22*pow(dx,2)*pow(dy,2) + a04*pow(dy,4) + a41*pow(dx,4)*dy
	df+=a23*pow(dx,2)*pow(dy,3) + a42*pow(dx,4)*pow(dy,2) + a24*pow(dx,2)*pow(dy,4);
	f = f0 + df/3600;

	dl =b10*dx +b11*dx*dy +b30*pow(dx,3) + b12*dx*pow(dy,2) + b31*pow(dx,3)*dy;
	dl+=b13*dx*pow(dy,3)+b50*pow(dx,5) + b32*pow(dx,3)*pow(dy,2) + b14*dx*pow(dy,4);
	dl+=b51*pow(dx,5)*dy +b33*pow(dx,3)*pow(dy,3) + b15*dx*pow(dy,5);
	l = l0 + dl/3600};
	
	document.getElementById("f").value = f;
	document.getElementById("l").value = l;
	setAddress(f,l);
	setMap(f,l);
	$('#mapcenter').hide();	
}

function coordLL(){
	var f = document.getElementById("f").value;
	var l = document.getElementById("l").value;
	setRD(f,l);
	setAddress(f,l);
	setMap(f,l);
	$('#mapcenter').hide();
}

function coordLoc(){
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position){
			var f = position.coords.latitude;
			var l = position.coords.longitude;
			setRD(f,l);
			setAddress(f,l);
			setMap(f,l);
			$('#mapcenter').hide();
		},function(error){
			alert("Er ging iets mis tijdens het verkrijgen van je locatie.");
		});
	} else {
		alert("Je browser staat niet toe je huidige locatie te gebruiken.");
	}
}

function coordAddress(){
	if (!geocoder) return;
	var streetname = document.getElementById("adres").value;
	geocoder.geocode( { 'address': streetname}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
		var f = results[0].geometry.location.lat();
		var l = results[0].geometry.location.lng();
		document.getElementById("f").value = f;
		document.getElementById("l").value = l;
		document.getElementById("adres").value = results[0].formatted_address;
		setRD(f,l);
		setMap(f,l);
		$('#mapcenter').hide();
      } else {
        alert("Berekenen van locatie via adres mislukt vanwege de volgende reden: " + status);
      }
    });
}

function coordCenter(){
	var ctr = map.getCenter();
	var f = ctr.lat();
	var l = ctr.lng();
	document.getElementById("f").value = f;
	document.getElementById("l").value = l;
	setAddress(f,l);
	setRD(f,l);
	setMap(f,l);
	$('#mapcenter').hide();
}

function setAddress(f,l){
	if (!geocoder) return;
	var latlng = new google.maps.LatLng(f, l);
	geocoder.geocode({'latLng': latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				document.getElementById("adres").value = results[0].formatted_address;
			}else if (results[1]) {
				document.getElementById("adres").value = results[1].formatted_address;
			}
		}
	});
}

function setMap(f,l){
	if (typeof google === 'undefined') return;
	var pos = new google.maps.LatLng(f,l);
	if(!mapmarker){
		mapmarker = new google.maps.Marker({position: pos, map: map});
	}
	mapmarker.setPosition(pos);
	map.setCenter(pos);
	document.getElementById("submit_hint").disabled = false;
	document.getElementById("submit_spot").disabled = false;
	if (document.getElementById("submit_hunt")) {
		document.getElementById("submit_hunt").disabled = false;
	}
}

function setRD(f,l){
	if(f > 0 && l > 0){
		x0  = 155000.00;
		y0  = 463000.00;

		f0 = 52.15616056;
		l0 =  5.38763889;

		c01=190066.98903 ;  d10=309020.31810;
		c11=-11830.85831 ;  d02=  3638.36193;
		c21=  -114.19754 ;  d12=  -157.95222;
		c03=   -32.38360 ;  d20=    72.97141;
		c31=    -2.34078 ;  d30=    59.79734;
		c13=    -0.60639 ;  d22=    -6.43481;
		c23=     0.15774 ;  d04=     0.09351;
		c41=    -0.04158 ;  d32=    -0.07379;
		c05=    -0.00661 ;  d14=    -0.05419;
							d40=    -0.03444;
		df=(f - f0) * 0.36;
		dl=(l - l0) * 0.36;

		with(Math){
		dx =c01*dl + c11*df*dl + c21*pow(df,2)*dl + c03*pow(dl,3);
		dx+=c31*pow(df,3)*dl + c13*df*pow(dl,3) + c23*pow(df,2)*pow(dl,3);
		dx+=c41*pow(df,4)*dl + c05*pow(dl,5);
		x=x0 + dx;
		x=round(100*x)/100;
		}
		document.getElementById("x").value = parseInt(x);
		

		with(Math){
		dy =d10*df + d20*pow(df,2) + d02*pow(dl,2) + d12*df*pow(dl,2);
		dy+=d30*pow(df,3) + d22*pow(df,2)*pow(dl,2) + d40*pow(df,4);
		dy+=d04*pow(dl,4) + d32*pow(df,3)*pow(dl,2) + d14*df*pow(dl,4);
		y=y0 + dy;
		y=round(100*y)/100}
		document.getElementById("y").value = parseInt(y);
	}else{
		alert('Deze locatie ligt buiten het mogelijke gebied.');
	}
}
$(document).ready(function(){
	$('#map').append('<div id="mapcenter"></div>');
	
	$("#submit_hunt").click(function(e) {
		if(($("input[name=code]").val() == "" || $("select[name=rider]").val() == "") && !confirm('Je hebt geen huntcode en/of huntteam opgegeven. Weet je zeker dat je deze locatie als hunt wil toevoegen zonder een hunt te loggen?')) {
			e.preventDefault();
		}
	});
});