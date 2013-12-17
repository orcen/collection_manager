function getMaterial(part,fieldId)
{
	if( part == 'handle'){
 		$(fieldId).value = 'drevo';
	}
	else{
		$(fieldId).value = 'ocel';
	}
}

function $(id)
{
 return document.getElementById(id);
}