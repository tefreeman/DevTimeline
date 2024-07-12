
// Encode Address parts to create Googleapi sending addresds full
function createAddress()
	{
		var streetAddress = document.getElementById("streetAddress").value;
		var city = document.getElementById("city").value;
		var state = document.getElementById("state").value;
		var zipCode = document.getElementById("zipCode").value;
		var geocoder= new google.maps.Geocoder();
		var address = streetAddress + " " + city + ", " + state + " " + zipCode;
		geocoder.geocode({'address': address}, function(results, status)
		{
				if (status === google.maps.GeocoderStatus.OK) 
			{
			var lat = results[0].geometry.location.lat();
			var lng = results[0].geometry.location.lng();
			document.getElementById("lat").value = lat;
			document.getElementById("lng").value = lng;		
			document.getElementById("myForm").submit();
			}
			else
			{
			alert("Your address cannot be found. Try checking to make sure you have entered the right address.");
			}
		});
	}
	