//=============================================================================
// Image Preloader based on http://www.silver-daggers.co.uk/demo/ImagePreloader.html

function ImagePreloader(image,callback)
{
	// store the callback
	this.callback = callback;
	this.imageSrc = image;
	this.preload(image);
}

ImagePreloader.prototype.preload = function(image)
{
	// create new Image object and add to array
	var oImage = new Image;
	this.image = oImage;
	
	// set up event handlers for the Image object
	oImage.onload = ImagePreloader.prototype.onload;
	oImage.onerror = ImagePreloader.prototype.onerror;
	oImage.onabort = ImagePreloader.prototype.onabort;
	
	// assign pointer back to this.
	oImage.oImagePreloader = this;
	oImage.bLoaded = false;
	oImage.source = image;
	
	// assign the .src property of the Image object
	oImage.src = image;
}
ImagePreloader.prototype.onComplete = function()
{
	this.callback(this.image, this.imageSrc);
}
ImagePreloader.prototype.onload = function()
{
	this.bLoaded = true;
	this.oImagePreloader.onComplete();
}
ImagePreloader.prototype.onerror = function()
{
	this.bError = true;
	log(10,"[ImagePreloader.prototype.onerror] error for file: " + this.imageSrc);
	this.oImagePreloader.onComplete();
}
ImagePreloader.prototype.onabort = function()
{
	this.bAbort = true;
	this.oImagePreloader.onComplete();
}