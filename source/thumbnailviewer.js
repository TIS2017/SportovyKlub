
var thumbnailviewer={
enableTitle: true, 
enableAnimation: true, 
definefooter: '<div class="footerbar">X</div>', 
defineLoading: '<img src="loading.gif" /> Loading Image...', 



scrollbarwidth: 16,
opacitystring: 'filter:progid:DXImageTransform.Microsoft.alpha(opacity=10); -moz-opacity: 0.1; opacity: 0.1',
targetlinks:[], 

createthumbBox:function(){

document.write('<div id="thumbBox" onClick="thumbnailviewer.closeit()"><div id="thumbImage"></div>'+this.definefooter+'</div>')
document.write('<div id="thumbLoading">'+this.defineLoading+'</div>')
this.thumbBox=document.getElementById("thumbBox")
this.thumbImage=document.getElementById("thumbImage") 
this.thumbLoading=document.getElementById("thumbLoading") 
this.standardbody=(document.compatMode=="CSS1Compat")? document.documentElement : document.body 
},


centerDiv:function(divobj){ 
var ie=document.all && !window.opera
var dom=document.getElementById
var scroll_top=(ie)? this.standardbody.scrollTop : window.pageYOffset
var scroll_left=(ie)? this.standardbody.scrollLeft : window.pageXOffset
var docwidth=(ie)? this.standardbody.clientWidth : window.innerWidth-this.scrollbarwidth
var docheight=(ie)? this.standardbody.clientHeight: window.innerHeight
var docheightcomplete=(this.standardbody.offsetHeight>this.standardbody.scrollHeight)? this.standardbody.offsetHeight : this.standardbody.scrollHeight 
var objwidth=divobj.offsetWidth 
var objheight=divobj.offsetHeight 
var topposition=(docheight>objheight)? scroll_top+docheight/2-objheight/2+"px" : scroll_top+10+"px" 
divobj.style.left=docwidth/2-objwidth/2+"px" 
divobj.style.top=Math.floor(parseInt(topposition))+"px"
divobj.style.visibility="visible"
},

showthumbBox:function(){ 
thumbnailviewer.thumbLoading.style.visibility="hidden" 
this.centerDiv(this.thumbBox)
if (this.enableAnimation){ 
this.currentopacity=0.1 
this.opacitytimer=setInterval("thumbnailviewer.opacityanimation()", 20)
}
},


loadimage:function(link){ 
if (this.thumbBox.style.visibility=="visible") 
this.closeit() 
var imageHTML='<img src="'+link.getAttribute("href")+'"class="img4" style="'+this.opacitystring+'" />' 
if (this.enableTitle && link.getAttribute("title")) 
imageHTML+='<br />'+link.getAttribute("title")
this.centerDiv(this.thumbLoading) 
this.thumbImage.innerHTML=imageHTML 
this.featureImage=this.thumbImage.getElementsByTagName("img")[0] 
if (this.featureImage.complete)
thumbnailviewer.showthumbBox()
else{
this.featureImage.onload=function(){ 
thumbnailviewer.showthumbBox() 
}
}
if (document.all && !window.createPopup) 
this.featureImage.src=link.getAttribute("href")
this.featureImage.onerror=function(){ 
thumbnailviewer.thumbLoading.style.visibility="hidden" 
}
},

setimgopacity:function(value){ 
var targetobject=this.featureImage
if (typeof targetobject.style.opacity=="string") 
targetobject.style.opacity=value
else if (typeof targetobject.style.MozOpacity=="string") 
targetobject.style.MozOpacity=value
else if (targetobject.filters && targetobject.filters[0]){ 
if (typeof targetobject.filters[0].opacity=="number") 
targetobject.filters[0].opacity=value*100
else 
targetobject.style.filter="alpha(opacity="+value*100+")"
}
else 
this.stopanimation()
},

opacityanimation:function(){ 
this.setimgopacity(this.currentopacity)
this.currentopacity+=0.1
if (this.currentopacity>1)
this.stopanimation()
},

stopanimation:function(){
if (typeof this.opacitytimer!="undefined")
clearInterval(this.opacitytimer)
},


closeit:function(){ 
this.stopanimation()
this.thumbBox.style.visibility="hidden"
this.thumbImage.innerHTML=""
this.thumbBox.style.left="-2000px"
this.thumbBox.style.top="-2000px"
},

cleanup:function(){ 
this.thumbLoading=null
if (this.featureImage) this.featureImage.onload=null
this.featureImage=null
this.thumbImage=null
for (var i=0; i<this.targetlinks.length; i++)
this.targetlinks[i].onclick=null
this.thumbBox=null
},

dotask:function(target, functionref, tasktype){ 
var tasktype=(window.addEventListener)? tasktype : "on"+tasktype
if (target.addEventListener)
target.addEventListener(tasktype, functionref, false)
else if (target.attachEvent)
target.attachEvent(tasktype, functionref)
},

init:function(){ 
if (!this.enableAnimation)
this.opacitystring=""
var pagelinks=document.getElementsByTagName("a")
for (var i=0; i<pagelinks.length; i++){
if (pagelinks[i].getAttribute("class") && pagelinks[i].getAttribute("class")=="thumbnail"){ 
pagelinks[i].onclick=function(){
thumbnailviewer.stopanimation() 
thumbnailviewer.loadimage(this) 
return false
}
this.targetlinks[this.targetlinks.length]=pagelinks[i] 
} 
} 
this.dotask(window, function(){if (thumbnailviewer.thumbBox.style.visibility=="visible") thumbnailviewer.centerDiv(thumbnailviewer.thumbBox)}, "resize")


} 

}

thumbnailviewer.createthumbBox() 
thumbnailviewer.dotask(window, function(){thumbnailviewer.init()}, "load") 
thumbnailviewer.dotask(window, function(){thumbnailviewer.cleanup()}, "unload")