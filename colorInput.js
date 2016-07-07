function ColorInputOffsetLeft(element) {
	var offset = element.offsetLeft;
	if(element.offsetParent.tagName.toLowerCase() != 'body') {
		while((element = element.offsetParent) && element.tagName.toLowerCase() != 'body')
			offset += element.offsetLeft;
	};
	return offset;
}
function ColorInputMouseLeft(event) {
	return Math.round(event.clientX + window.pageXOffset - 1);
}
function ColorInputShow(downbar) {
	var colorInput = downbar.parentNode.parentNode.parentNode.parentNode;
	var rv = parseInt(colorInput.childNodes[0].childNodes[1].firstChild.value);
	var gv = parseInt(colorInput.childNodes[1].childNodes[1].firstChild.value);
	var bv = parseInt(colorInput.childNodes[2].childNodes[1].firstChild.value);
	if(isNaN(rv) || rv < 0) rv = 0;
	if(isNaN(gv) || gv < 0) gv = 0;
	if(isNaN(bv) || bv < 0) bv = 0;
	if(rv > 255) rv = 255;
	if(gv > 255) gv = 255;
	if(bv > 255) bv = 255;
	colorInput.style.backgroundColor = 'rgb(' + rv + ',' + gv + ',' + bv + ')';
}
function ColorInputChangeUnit(event) {
	var downbar = event.currentTarget;
	//var x = ColorInputOffsetLeft(downbar);
	//var y = Math.round(event.clientX);
	//alert('page left = ' + window.pageXOffset + ' client x = ' + y + ' downbar offset left = ' + x);
	var d = ColorInputMouseLeft(event) - ColorInputOffsetLeft(downbar);
	var w = downbar.offsetWidth - 1;
	var c = Math.round(255*d/w);
	if(c < 0) c = 0;
	if(c > 255) c = 255;
	downbar.firstChild.style.width = Math.round(c*100/255) + "%";
	var input = downbar.parentNode.parentNode.nextSibling.firstChild;
	input.value = c;
	ColorInputShow(downbar);
}
function ColorInputChangeTouchedUnit(event) {
	var downbar = event.currentTarget;
	var d = 0;
	if(event.changedTouches[0]) d = Math.round(event.changedTouches[0].pageX) - ColorInputOffsetLeft(downbar);
	var w = downbar.offsetWidth - 1;
	var c = Math.round(255*d/w);
	if(c < 0) c = 0;
	if(c > 255) c = 255;
	downbar.firstChild.style.width = Math.round(c*100/255) + "%";
	var input = downbar.parentNode.parentNode.nextSibling.firstChild;
	input.value = c;
	ColorInputShow(downbar);
}
function ColorInputChangeInput(input) {
	var value = parseInt(input.value);
	if(isNaN(value) || value < 0) value = 0;
	if(value > 255) value = 255;
	input.value = value;
	var downbar = input.parentNode.previousSibling.firstChild.firstChild;
	downbar.firstChild.style.width = Math.round(value*100/255) + "%";
	ColorInputShow(downbar);
}
function ColorInputLoad(id) {
	ColorInputChangeInput(document.getElementById(id + '-red'));
	ColorInputChangeInput(document.getElementById(id + '-green'));
	ColorInputChangeInput(document.getElementById(id + '-blue'));
}