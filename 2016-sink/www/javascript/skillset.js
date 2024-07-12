
// activates types of skills under the catorgory
var count = 0;
var z = 0;
var arrayCounter = [];
var f = document.createElement("form");
f.setAttribute('method',"post");
f.setAttribute('action',"/php/registerProfessionalSkillSet.php");
f.setAttribute('id', 'myForm');
// create count input to pass to php to coun;t the number of inputs being sent
var h = document.createElement("input");
var j = document.createElement("input");
 //input element, text
j.setAttribute('type',"text");
j.setAttribute('name',"count");
j.setAttribute('value',count);
j.setAttribute("hidden", true);
f.appendChild(j);
document.getElementsByTagName('body')[0].appendChild(f);


	function activateButton(clicked_name)
	{
		 
		var e = document.getElementById(clicked_name);
		if(e.style.display == 'block')
			{
				 e.style.display = 'none';
			}
			else 
				{
					e.style.display = 'block';
				}
	}
	function changeClass(clicked_id, clicked_name)
	{

		var e = document.getElementById(clicked_id);
			if(e.className == 'btn3')	
			{
				arrayCounter.push(clicked_id);
				count +=1;
				update();
				document.getElementById(clicked_id).className = "btn2";


			}	
				else
			{
				count -=1;
				update();
				
				
				// grab the name of the input field which is produced by the count variable
				for (i = 0; i < arrayCounter.length; i++) 
				{
				if(arrayCounter[i] == clicked_id)
					{
					arrayCounter.splice(i, 1);
					}
				}
				$('input[value="'+ clicked_id +'"]').remove();
				//toggles the id value for the mysql database
				document.getElementById(clicked_id).className = "btn3";	
			}
	}		
		
	function buildForm()
	{
		for (s = 0; s < arrayCounter.length; s++)
		{
		z += 1;
		var m = document.createElement("input"); //input element, text
					m.setAttribute('type',"text");
					m.setAttribute('name',z);
					m.setAttribute('value',arrayCounter[s]);
					m.setAttribute("hidden", true);
					f.appendChild(m);
		
		}
	}
	
	
	function update()
		{
		j.setAttribute('value',count);
		}
		
	function submitIt()
	{
	buildForm();
	document.getElementById("myForm").submit();
	}
	