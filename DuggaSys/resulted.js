var sessionkind=0;
var querystring=parseGet();
var filez;

AJAXService("GET",{cid:querystring['cid']},"RESULT");

$(function() {
  $( "#release" ).datepicker({dateFormat: "yy-mm-dd"});
    $( "#deadline" ).datepicker({dateFormat: "yy-mm-dd"});
});

//----------------------------------------
// Commands:
//----------------------------------------

function makeSelect(gradesys,cid,vers,moment,uid,mark,ukind)
{
		var str="";
		str+="<select onchange='changeGrade(this,\""+gradesys+"\",\""+cid+"\",\""+vers+"\",\""+moment+"\",\""+uid+"\",\""+mark+"\",\""+ukind+"\");'>";

		// Irrespective of marking system we allways print - and U
		if(mark==null||mark==0) str+="<option selected='selected' value='0'>-</option>"
		else str+="<option value='0'>-</option>";
		if(mark==1) str+="<option selected='selected' value='1'>U</option>"
		else str+="<option value='1'>U</option>";

		// Gradesystem: 1== UGVG 2== UG 3== U345
		if(gradesys==1){
				if(mark==2) str+="<option selected='selected' value='2'>G</option>"
				else str+="<option value='2'>G</option>";
				if(mark==3) str+="<option selected='selected' value='3'>VG</option>"
				else str+="<option value='3'>VG</option>";
		}else if(gradesys==2){
				if(mark==2) str+="<option selected='selected' value='2'>G</option>"
				else str+="<option value='2'>G</option>";		
		}else if(gradesys==3){
				if(mark==4) str+="<option selected='selected' value='4'>3</option>"
				else str+="<option value='4'>3</option>";		
				if(mark==5) str+="<option selected='selected' value='5'>4</option>"
				else str+="<option value='5'>4</option>";		
				if(mark==6) str+="<option selected='selected' value='6'>5</option>"
				else str+="<option value='6'>5</option>";				
		}

		str+="</select>";
		return str;
}

function changeGrade(elem,gradesys,cid,vers,moment,uid,mark,ukind)
{
		mark=elem.value;
		AJAXService("CHGR",{cid:cid,vers:vers,moment:moment,uid:uid,mark:mark,ukind:ukind},"RESULT");
}

//----------------------------------------
// Renderer
//----------------------------------------

function returnedResults(data)
{
		str=" YOWZA!";
		
		results=data['results'];
		
		str+="<table class='list'>";

		str+="<tr><th></th>";
		
		for(j=0;j<data['moments'].length;j++){
				var jtem=data['moments'][j];					
				if((jtem['kind']==3&&jtem['moment']==null)||(jtem['kind']==4)){
						// Td-s for each variant or non-connected dugga.
						str+="<th style='border-left:2px solid white;'>";
						str+=jtem['entryname'];
						str+="</th>";
				}
		}

		str +="</tr>";
		
		if (data['entries'].length > 0) {
				for(i=0;i<data['entries'].length;i++){
						var user=data['entries'][i];
				
						str+="<tr class='fumo'>";

						// One row for each student
						str+="<td>";
						str+=user['firstname']+" "+user['lastname']+"<br/>"+user['ssn'];
						str+="</td>";
						
						// Each of the 
						for(j=0;j<data['moments'].length;j++){
								var moment=data['moments'][j];					

								if((moment['kind']==3&&moment['moment']==null)||(moment['kind']==4)){
										str+="<td style='padding:0px'>";

										// We have data if there is a set of result elements for this student in this course... otherwise null
										studres=results[user['uid']];
										// There are results to display.
										str+="<table width='100%' class='innertable' >";
										str+="<tr>";

										if(moment['kind']==3&&moment['moment']==null){
												// Standalone Dugga -- we just need to make a dugga entry with the correct marking system.
												str+="<td>&nbsp;</td></tr><tr  style='border-top:2px solid #dbd0d8;'><td>";
												// We are now processing the moment entry in the moment object
												var foundres=null;
												if(studres!=null){
														for(var l=0;l<studres.length;l++){
																var resultitem=studres[l];		
																if(resultitem['moment']==moment['lid']){
																		// There is a result to print
																		foundres=resultitem['grade'];
																		
																		// gradesys cid vers moment uid mark
																		str+=makeSelect(moment['gradesystem'],querystring['cid'],querystring['coursevers'],moment['lid'],user['uid'],resultitem['grade'],"U");
																}
														}
												}
												if(foundres==null){
														str+=makeSelect(moment['gradesystem'],querystring['cid'],querystring['coursevers'],moment['lid'],user['uid'],null,"I");
												}
										
												str+="</td>";
										}
										if(moment['kind']==4){
												// Moment - which may or may not have quizes
												
												ttr="";
												duggacnt=0;
												for(var k=0;k<data['moments'].length;k++){
														var dugga=data['moments'][k];		
														
														// If the id of current item is same as moment of a dugga
														if((dugga['moment']==moment['lid'])&&(dugga['kind']==3)){
																if(duggacnt>0) ttr+="<td style='border-left:2px solid #dbd0d8;'>"
																else ttr+="<td>";
																	
																duggacnt++;

																// We now have number of listentry, student data, course information etc, are there any results?
																var foundres=null;
																if(studres!=null){
																		for(var l=0;l<studres.length;l++){
																				var resultitem=studres[l];		
																				if(resultitem['moment']==dugga['lid']){
																						// There is a result to print
																						foundres=resultitem['grade'];
																						
																						// gradesys cid vers moment uid mark
																						ttr+=makeSelect(dugga['gradesystem'],querystring['cid'],querystring['coursevers'],dugga['lid'],user['uid'],resultitem['grade'],"U");
																				}
																		}
																}
																if(foundres==null){
																		ttr+=makeSelect(dugga['gradesystem'],querystring['cid'],querystring['coursevers'],dugga['lid'],user['uid'],null,"I");
																}
																ttr+="</td>";
														}														
												}
												
												if(duggacnt==0){
														ttr+="<td>&nbsp;</td>"
														str+="<td colspan='1'>"
												}else{
														str+="<td colspan='"+duggacnt+"'>"
												} 

												// We are now processing the moment entry in the moment object
												var foundres=null;
												if(studres!=null){
														for(var l=0;l<studres.length;l++){
																var resultitem=studres[l];		
																if(resultitem['moment']==moment['lid']){
																		// There is a result to print
																		foundres=resultitem['grade'];
																		
																		// gradesys cid vers moment uid mark
																		str+=makeSelect(moment['gradesystem'],querystring['cid'],querystring['coursevers'],moment['lid'],user['uid'],resultitem['grade'],"U");
																}
														}
												}
												if(foundres==null){
														str+=makeSelect(moment['gradesystem'],querystring['cid'],querystring['coursevers'],moment['lid'],user['uid'],null,"I");
												}
										
												str+="</td></tr><tr style='border-top:2px solid #dbd0d8;'>";
												str+=ttr;
										}

										str+="</tr>";
										str+="</table>";
		
										str+="</td>";
								}
						}
						str+="</tr>";
				}
		}	

		var slist=document.getElementById("content");
		slist.innerHTML=str;

	  if(data['debug']!="NONE!") alert(data['debug']);

}

		