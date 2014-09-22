function setup()
{
		$('.bit').click(function(){
				bitClick(this.id);
		});

		$('.hexo').click(function(){
				hexClick(this.id);
		});
}

var retdata=null;

function returnedDugga(data)
{
	  if(data['debug']!="NONE!") alert(data['debug']);

		if(data['param']=="UNK"){
				alert("UNKNOWN DUGGA!");
		}else{
			  retdata=jQuery.parseJSON(data['param'].replace(/&quot;/g, '"'));
				$("#talet").html(retdata['tal']);
		}
}

function bitClick(divid)
{
			if($("#"+divid).html()=="1"){
					$("#"+divid).html("0");
					$("#"+divid).removeClass("ett" );
					$("#"+divid).addClass("noll" );
			}else{
					$("#"+divid).html("1");
					$("#"+divid).addClass("ett" );
					$("#"+divid).removeClass("noll" );
			}
}

var hc=null;
function hexClick(divid)
{

	dw=$(window).width();
	dpos=$("#"+divid).position();
	dwid=$("#"+divid).width();
	dhei=$("#"+divid).height();
	bw=Math.round(dwid)*2.0;
	if(bw<128) bw=128;
	
	lpos=dpos.left;
	
	popclass="arrow-top";
	if((lpos+bw)>dw){
			popclass="arrow-topr";
			lpos=lpos-bw+dwid;
	}
	 
	var hh=(dhei*2);
	if(hh<160) hh=160;
	hh+="px";
	
	$("#pop").css({top: (dpos.top+dhei+10), left:lpos, width:bw,height:hh,display:"block"})
	$("#pop").removeClass("arrow-topr");
	$("#pop").removeClass("arrow-top");
	$("#pop").addClass(popclass);
	
	hc=divid;
}

function setval(sval)
{
		if(hc!=null){
				$("#"+hc).html(sval);		
		}
		$("#pop").css({display:"none"})
}

function saveClick()
{
		// Loop through all bits
		bitstr="";
		$(".bit").each(function( index ) {
				bitstr=bitstr+this.innerHTML;
		});
		
		bitstr+=" "+$("#H0").html();
		bitstr+=" "+$("#H1").html();
		
		bitstr+=" "+window.screen.width;
		bitstr+=" "+window.screen.height;
		
		bitstr+=" "+$(window).width();
		bitstr+=" "+$(window).height();
		
		// Duggastr includes only the local information, duggasys adds the dugga number and the rest of the information.
		saveDuggaResult(bitstr);
}