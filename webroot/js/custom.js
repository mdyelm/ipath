function changeLoadImageSizeImg(widthDiv, heightDiv, el){
        var width = $(el).attr('data-width');
        var height = $(el).attr('data-height');
        var width_image = widthDiv;
        var height_image = heightDiv;        
        if (width/height > widthDiv/heightDiv){
            height_image = parseInt(widthDiv * height /width);
            if (isNaN(height_image)){
                height_image = heightDiv;
            }
        }
        else {
            width_image = parseInt(heightDiv * width /height);
            if (isNaN(width_image)){
                width_image = widthDiv;
            }
        }
        $(el).attr('height', height_image);
        $(el).attr('width', width_image);            
        $(el).css('opacity',1);        
}
function changeLoadImageSize(widthDiv, heightDiv, imgString){
    $(imgString).each(function(){
           changeLoadImageSizeImg(widthDiv, heightDiv, $(this));     
    });
}

/**
 * clear over lay
 * @param {type} markersArray
 * @returns {undefined}
 */
function clearOverlays(markersArray) {
    //console.log(markersArray);
    for (var i = 0; i < markersArray.length; i++) {
        if(typeof markersArray[i] !== 'undefined') {
            markersArray[i].setVisible(false);
        }
    }
    //markersArray.length = 0;
}

/**
 * Marker at google map
 */
function callMarkerRoute(data, type) {
    var len = data.length;
    $.each(data, function (index, datum) {
        if (index == len - 1) {
            createOneMarker(datum, index, type, 'lastCordinate');
        } else {
            createOneMarker(datum, index, type, '');
        }

    })
}
/**
 * Fix image to center by fix height showData div
 * @returns {undefined}
 */
function fixHeightShowDataDiv(){
    $('.showData').each(function(el){
       var height = $(this).prev().css('height');
       $(this).css('height', height);
       var width = $(this).css('width');
       var el = $(this).find('img');
       changeLoadImageSizeImg(width, height, el);
    });
}


