<html>
<head><title>Loading Spell Checker</title>
<script ID="clientEventHandlersJS" LANGUAGE="javascript">
<!--

function window_onload() {
document.SPELLDATA.formname.value=opener.document.SPELLDATA.formname.value
document.SPELLDATA.subjectname.value=opener.document.SPELLDATA.subjectname.value
document.SPELLDATA.messagebodyname.value=opener.document.SPELLDATA.messagebodyname.value
document.SPELLDATA.companyID.value=opener.document.SPELLDATA.companyID.value
document.SPELLDATA.language.value=opener.document.SPELLDATA.language.value
document.SPELLDATA.opener.value=opener.document.SPELLDATA.opener.value
document.SPELLDATA.action=opener.document.SPELLDATA.formaction.value
													

var flen=opener.document.forms.length

var index=flen
for(i=0; i<flen; i++){
	if(opener.document.forms[i].name==document.SPELLDATA.formname.value){
		index=i
		i=flen
		}
	}
	
if(index<flen){
	var ilen=opener.document.forms[index].elements.length
	var indexcontrol=ilen
	if(document.SPELLDATA.subjectname.value!=""){
		for(i=0; i<ilen; i++){
			if(opener.document.forms[index].elements[i].name==document.SPELLDATA.subjectname.value){
				indexcontrol=i
				i=ilen
				}
			}	
		if(indexcontrol<ilen)		
			document.SPELLDATA.subject.value=opener.document.forms[index].elements[indexcontrol].value
		}
				
	if(document.SPELLDATA.messagebodyname.value!=""){
		indexcontrol=ilen
		for(i=0; i<ilen; i++){
			if(opener.document.forms[index].elements[i].name==document.SPELLDATA.messagebodyname.value){
				indexcontrol=i
				i=ilen
				}
			}	
		if(indexcontrol<ilen)		
			document.SPELLDATA.messagebody.value=opener.document.forms[index].elements[indexcontrol].value
		}	
	document.SPELLDATA.submit()
	}else{
		alert("no form found.  Check java function call")
		window.close()
		}
}

//-->
</script>
</head>
<body LANGUAGE=javascript onload="return window_onload()">
<form action="" method="post" name="SPELLDATA" LANGUAGE="javascript">

	<h1>Loading Spell Checker. Please wait</h1>
   <input name="formname"  type="hidden" > 
   <input name="messagebodyname" type="hidden" > 
   <input name="subjectname" type="hidden" >
   <input name="companyID"  type="hidden" >
   <input name="language"  type="hidden" >
   <input name="opener"  type="hidden" > 
   <input name="closer"  type="hidden" value="finish.asp"> 
   <input name="IsHTML"  type="hidden" value=0> 
   
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <textarea name="subject"></textarea> 
   <textarea name="messagebody"></textarea>
</form>
</body>
</html>
