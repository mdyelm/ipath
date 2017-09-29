<div id="container" class="ov">
    <div id="content-left">
        <div class="fix-menu" data-toggle="collapse" data-target="#menuDropdown">
            <?php //echo $this->Html->image('icon-menu.png', ['class' => 'fix-img-index']); ?>
            <amp-img class="fix-img-index" src="<?php echo $this->request->webroot; ?>img/icon-menu.png" width="287" height="32"/></amp-img>
            <p style="text-align: center;margin-left: 215px;font-weight: bold;color: #999;font-size: 12px;position: relative;top:-5px;"><?= h('Version' . ' ' . $versionWeb); ?> </p>
            <!--<button class="hiddenMap" on="tap:showImg.show()">Show Map</button>-->
            <span class="glyphicon glyphicon-menu-hamburger buttom-menu hiddenMap" aria-hidden="true" on="tap:showImg.show()"></span>
        </div>
        <div id="list_records">
            <?php $routeArrays = $routes->toArray(); ?>
            <?php if (!isset($id)) $id = $arrayIndex[$routeArrays[0]['id']] ?>
            <?php foreach ($routeArrays as $key => $route) : ?>
                <div class="item ov mb10" id="<?= $arrayIndex[$route['id']] ?>">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 showMapMB" on="tap:showImg.show()" data-bind="<?= $arrayIndex[$route['id']] ?>">
                        <?php if ($route['Users']['del_flg']) : ?>
                            <span><?= __('DELETED USER:') ?> </span><?php echo $route['Users']['username']; ?><br>
                        <?php else : ?>
                            <span><?= __('USER:') ?> </span><?php echo $route['Users']['username']; ?><br>
                        <?php endif; ?>
                        <span><?= __('Device ID:') ?> </span><?= $route['Devices']['name'] ?><br>
                        <span><?= __('SURVEY ID:') ?> </span><?php echo $arrayIndex[$route['id']] ?><br>
                        <span><?= __('DATE:') ?> </span><?= date('Y/m/d', strtotime($route['time_start'])) ?><br>
                        <span><?= __('LOCATION:') ?> </span><?= $route['country'] ?>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 showData showDataMB">
                        <?php
                        $url = $this->Url->build(["controller" => "mobiles", "action" => "detail", $arrayIndex[$route['id']]]) . '?token=' . $token;
                        if (!empty($filter) && !empty($type_filter)) {
                            $url .= '?filter=' . $filter . '&type=' . $type_filter;
                        }
                        ?>                        
                        <a rel="amphtml" href="<?= $url ?>" class="target targetOnly">
                            <?php echo $this->App->getImageFirstApp($route['id']); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($routes->count() > 10): ?>
            <button class="btn-primary pull-right" id="load-more" data-id="<?= $offset ?>"><?= __('Load More') ?></button>
        <?php endif; ?>
    </div>
    <div id="content-right" style="width: 100%;">
        <div id="showImg" class="fix-menu" data-toggle="collapse" data-target="#menuDropdown" hidden>
            <?php //echo $this->Html->image('icon-menu.png', ['class' => 'fix-img-index']); ?>
            <amp-img class="fix-img-index" src="<?php echo $this->request->webroot; ?>img/icon-menu.png" width="287" height="32"/></amp-img>
            <p style="text-align: center;margin-left: 215px;font-weight: bold;color: #999;font-size: 12px;position: relative;top:-5px;"><?= h('Version' . ' ' . $versionWeb); ?> </p>
            <span class="glyphicon glyphicon-menu-hamburger buttom-menu hiddenMap" aria-hidden="true" on="tap:showImg.show()"></span>
        </div>
        <div class="viewMap" style="width: 100%; height:100%;">
            <div id="map_canvas"></div>
            <img src="<?= $this->request->webroot ?>img/red-dots.png" style="display:none" id="rotateimage">
            <img src="<?= $this->request->webroot ?>img/car-end.png" style="display:none" id="car-end">
            <img src="<?= $this->request->webroot ?>img/car-start.png" style="display:none" id="car-start">
            <button id="showMap" class="activeMap">&nbsp;</button>
            <button id="showDate">&nbsp;</button>
            <button id="showCamera" class="activeCamera">&nbsp;</button>
            <select id="selectMinute">
                <option value="1">1 <?= __('minute') ?></option>
                <option value="5">5 <?= __('minutes') ?></option>
                <option value="10">10 <?= __('minutes') ?></option>
            </select>
        </div>
    </div>

</div>

<script>
    var markersArray = new Array(), data, markersArrow = new Array(), markersDot = new Array(), map, bounds, drawRoutes = new Array();
    var dataLocal = '';
    var dataImg = '';
    var dataCoordinate = '';
    var infoWindows = new Array();
    var colorRoute = '#FF69B4';
    var strokeOpacity = 0;
    var strokeWeight = 2;
    var pointCoordinates = new Array();
    var markersDot5 = new Array();
    var markersDot10 = new Array();
    var scrollId = '<?= $id; ?>';
    var idShowMap = null;
    var dateStart5 = null;
    var dateStart10 = null;
    var currentMinute = 1;
    var scrolltoken = '<?= $token; ?>';
    var blankWinA = new Array();
    var blankUrl = new Array();
    var blankWin = null;

    function checkShowMap(id) {
        $('#content-left').css({"display": "none"});
        $('#content-right').css({"display": "block"});
        $(window).scrollTop(0);
        if (id === undefined || id === null) {
            id = scrollId;
            idShowMap = scrollId;
        } else if (id != scrollId) {
            if (idShowMap == null) {
                idShowMap = id;
            } else if (id != idShowMap) {
                idShowMap = id;
            }
        } else if (id === scrollId) {
            id = scrollId;
            idShowMap = id;
        }
        $('#showMarker').attr('data-bind', id);
        getCoordinates(id);
        if (dataLocal.length > 0) {
            // map options
            var options = {
                zoom: 14,
                center: new google.maps.LatLng(dataLocal[0].latitude, dataLocal[0].longitude), // centered US
                mapTypeId: google.maps.MapTypeId.TERRAIN,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain'],
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                },
                //                maxZoom: 19,
            };
            // init map
            map = new google.maps.Map(document.getElementById('map_canvas'), options);
            map.setTilt(0); //disable 45degree
            // call ajax route point
            callAjaxRoutePoints();
            checkCamera(0);
//                checkDate(0);
            checkMap(0);
            $('#content-left .item').css('background-color', '#ffffff');
            $(this).parent('.item').css('background-color', '#f1f1f1');
        }
    }
    /**
     * Scroll to current survey 
     * @returns {undefined}
     */
    function scrollAtLeftSideBar() {
        if (scrollId) {
            $('#list_records').animate({scrollTop: $('#' + scrollId).offset().top - 50});
        }
    }

    function load_more(offset) {
        $.ajax({
            url: '<?= $this->Url->build(["controller" => "mobiles", "action" => "loadMore"]); ?>',
            dataType: "json",
            data: {offset: offset, token: scrolltoken},
            type: "post",
            async: false,
            success: function (result) {
                if (result.status) {
                    $('#list_records').append(result.html);
<?php if (!empty($id)) : ?>
                        var id = <?= $id ?>;
                        $('#' + id).css('background-color', '#f1f1f1');
<?php endif; ?>
                } else {
                    $('#load-more').hide();
                }
            }
        });
    }

    function showTarget(sTarget) {
        var checkUrl = true;
        $.each(blankUrl, function (indexUrl, valueUrl) {
            if (sTarget === valueUrl) {
                $.each(blankWinA, function (index, value) {
                    if (indexUrl === index) {
                        /* check exist windows */
                        if (value && !value.closed) {
                            value.focus();
                            checkUrl = false;
                        } else {
                            blankWin = window.open(sTarget, "_blank");
                            blankWinA.push(blankWin);
                            blankUrl.push(sTarget);
                            blankUrl.splice(indexUrl, 1);
                            blankWinA.splice(index, 1);
                            checkUrl = false;
                        }
                    }
                });
            }
        });
        if (checkUrl == true) {
            blankWin = window.open(sTarget, "_blank");
            blankWinA.push(blankWin);
            blankUrl.push(sTarget);
        }
    }







    function changeLabelAfterZoomChanged() {
        console.log('1');
        // not class active date, hidden time label
        if (!$('#showDate').hasClass('activeDate2')) {
            $('.timeInner').hide();
        }
        // if not class active date 2, show marker
        if (!$('#showDate').hasClass('activeDate2')) {
            if (currentMinute == 1) {
                for (var i = 0; i < markersArray.length; i++) {
                    if (typeof markersArray[i] !== 'undefined') {
                        markersArray[i].setVisible(true);
                    }
                }
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner').show();
                }
            } else if (currentMinute == 5) {
                for (var i = 0; i < markersDot5.length; i++) {
                    if (typeof markersArray[markersDot5[i]] !== 'undefined') {
                        markersSelect[markersDot5[i]].setVisible(true);
                    }
                }
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner5').show();
                }
            } else if (currentMinute == 10) {
                for (var i = 0; i < markersDot10.length; i++) {
                    if (typeof markersArray[markersDot10[i]] !== 'undefined') {
                        markersArray[markersDot10[i]].setVisible(true);
                    }
                }
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner10').show();
                }
            }
        }
    }
    /**
     * set marker to map
     * @param {type} markersArray
     * @returns {undefined}
     */
    function setMarkerToMap(markersSelect) {
        //    console.log(markersArray);
//        console.log('markersDot5:');
//        console.log(markersDot5);
//        console.log('markersDot10:');
//        console.log(markersDot10);
        var show = false;
        if ($('#showMap').hasClass('activeMap') || $('#showDate').hasClass('activeDate')) {
            show = true;
        }
        if (show) {
            clearOverlays(markersSelect);
        }
        // not class active date, hidden time label
        if (!$('#showDate').hasClass('activeDate2')) {
            $('.timeInner').hide();
        }
        // if not class active date 2, show marker
        if (!$('#showDate').hasClass('activeDate2')) {
            if (currentMinute == 1) {
                for (var i = 0; i < markersSelect.length; i++) {
                    if (typeof markersSelect[i] !== 'undefined') {
                        markersSelect[i].setVisible(true);
                    }
                }
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner').show();
                }
            } else if (currentMinute == 5) {
                for (var i = 0; i < markersDot5.length; i++) {
                    if (typeof markersSelect[markersDot5[i]] !== 'undefined') {
                        markersSelect[markersDot5[i]].setVisible(true);
                    }
                }
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner5').show();
                }
            } else if (currentMinute == 10) {
                for (var i = 0; i < markersDot10.length; i++) {
                    if (typeof markersSelect[markersDot10[i]] !== 'undefined') {
                        markersSelect[markersDot10[i]].setVisible(true);
                    }
                }
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner10').show();
                }
            }
        }
        //markersArray.length = 0;
    }

    /**
     * From date string return array Year Month Day Hour Minute Second
     * @param {type} dateTimeString
     * @returns {Array}
     */
    function processStringDate(dateTimeString) {
        //2017-02-16T13:51:14+09:00
        var iso = /^(\d{4})(?:-(\d+))?-(\d+)(?:[T ](\d+):(\d+)(?::(\d+))+(.*))$/;
        var parts = dateTimeString.match(iso);
        return [parts[1] + "/" + parts[2] + "/" + parts[3], parts[4] + ":" + parts[5] + ":" + parts[6]];
    }


    /**
     * Hide draw route
     */
    function hiddenDrawRoute() {

        drawRoutes.forEach(function (path) {
            path.setMap(null);
        })
    }

    /**
     * Show draw route
     */
    function showDrawRoute() {
        drawRoutes.forEach(function (path) {
            path.setMap(map);
        })
    }

    function pushPointCoordinates(data) {
        for (var i = 0; i < data.length; i++) {
            pointCoordinates.push({lat: parseFloat(data[i].latitude), lng: parseFloat(data[i].longitude)});
        }
    }

    /**
     * Draw route at map
     * @returns {undefined}
     */
    function drawRoute() {

        var lineSymbol = {
            path: google.maps.SymbolPath.CIRCLE,
            strokeColor: colorRoute,
            fillColor: colorRoute,
            fillOpacity: 1
        };
        if (pointCoordinates.length > 1) {
            var flightPath = new google.maps.Polyline({
                path: pointCoordinates,
                strokeOpacity: strokeOpacity,
                icons: [{
                        icon: lineSymbol,
                        offset: '0',
                        repeat: '10px'
                    }],
            });
            flightPath.setMap(map);
            drawRoutes.push(flightPath);
        }
    }

    /**
     *	Create one marker and info window demo, add marker to array marker (easy for clear marker)
     */
    function createOneMarker(datum, index, type, last) {
        if (type == 1) {
            var dateArray = processStringDate(datum.catch_time);
            var dateCurrent = new Date(datum.catch_time);
            var classTime = "timeInner";
            // init markers
            if (index == 0) {
                var imgSrc = $('#car-start').attr('src');
            } else if (index != 0 && last == 'lastCordinate') {
                var imgSrc = $('#car-end').attr('src');
            } else {
                var imgSrc = $('#rotateimage').attr('src');
            }
            if (imgSrc == $('#rotateimage').attr('src')) {

                // check for 5 minus
                if (dateStart5 == null || Math.abs(dateCurrent - dateStart5) >= 1000 * 60 * 5) {
                    //console.log(datum.catch_time);
                    dateStart5 = dateCurrent;
                    markersDot5.push(index);
                    classTime += " timeInner5"
                }
                // check for 10 minus
                if (dateStart10 == null || Math.abs(dateCurrent - dateStart10) >= 1000 * 60 * 10) {
//                    console.log(datum.catch_time);
//                    console.log(index);
                    dateStart10 = dateCurrent;
                    markersDot10.push(index);
                    classTime += " timeInner10"
                }

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(datum.latitude, datum.longitude),
                    map: map,
                    icon: imgSrc,
                    label: '<span class="' + classTime + '">' + dateArray[1] + '</span>',
                    zIndex: 100
                });
                // set marker invisible
                //marker.setVisible(false);
                markersDot[index] = marker;
            } else {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(datum.latitude, datum.longitude),
                    map: map,
                    icon: imgSrc,
                    label: '<span class="timeStartStop">' + dateArray[0] + '<br/>' + dateArray[1] + '</span>',
                    zIndex: 100
                });
            }

            // push point to draw
            //pointCoordinates.push({lat: parseFloat(datum.latitude), lng: parseFloat(datum.longitude)});

            var latlng = marker.getPosition();
            bounds.extend(latlng);
        } else if (type == 2) {
            var dateArray = processStringDate(datum.Locations.catch_time);
//            console.log(datum);
            var marker1 = new google.maps.Marker({
                position: new google.maps.LatLng(datum.Locations.latitude, datum.Locations.longitude),
                map: map,
                icon: {
                    label: '<span class="timeInner timeInner5 timeInner10">' + dateArray[1] + '</span>',
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 4,
                    strokeColor: "red",
                    strokeWeight: 0,
                    fillColor: "red",
                    fillOpacity: 1,
                    anchor: new google.maps.Point(0, 2.5),
                    rotation: parseInt(datum.rotation)
                },
            });
            var marker = new google.maps.Marker({
                //label: '<span class="timeInner">2017/02/01<br/>18:00:00</span>',
                position: new google.maps.LatLng(datum.Locations.latitude, datum.Locations.longitude),
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 12,
                    strokeColor: "red",
                    strokeWeight: 2,
                },
                customInfo: index,
            });
            markersArray[index] = marker;
            markersArrow[index] = marker1;
            var contentString = '<div class="brite-div-img" data-zindex="' + index + '"><div><img style="width:300px;" src="<?= $this->request->webroot ?>files/image/' + datum.route_id + '/' + datum.name + '"/></div></div>';
            var infowindow = new google.maps.InfoWindow({
                content: contentString,
            });
            var icon = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                strokeColor: "red",
                strokeWeight: 3,
            };
            google.maps.event.addListener(marker, 'mouseover', function (event) {
                infowindow.open(map, marker);
                var iwOuter = $('.gm-style-iw');
                var iwBackground = iwOuter.prev();
                iwOuter.parent().parent().addClass('briteClass1');
                iwOuter.parent().addClass('briteClass');
                iwBackground.children(':nth-child(2)').css({'display': 'none'});
                iwBackground.children(':nth-child(4)').css({'display': 'none'});
                iwBackground.children(':nth-child(1)').css({'display': 'none'});
                iwBackground.children(':nth-child(3)').css({'display': 'none'});
                iwOuter.parent().parent().css({left: '15px', top: '30px'});
            });
            google.maps.event.addListener(marker, 'mouseout', function (event) {
                infowindow.open();
            });
            (function (marker, index) {
                // add click event
                google.maps.event.addListener(marker, 'click', function () {
                    window.location = '<?= $this->request->webroot ?>surveys/viewRoute/' + datum.route_id;
                });
            })(marker, index);
            var latlng = marker.getPosition();
            bounds.extend(latlng);
        }
    }

    function getCoordinates(id) {
        $.ajax({
            url: '<?= $this->Url->build(["controller" => "mobiles", "action" => "getDataAjax"]); ?>',
            dataType: "json",
            data: {id: id, first: 1},
            type: "post",
            async: false,
            success: function (result) {
                if (result.status) {
                    dataLocal = result.dataLocal;
                    dataImg = result.dataImg;
                    dataCoordinate = result.all;
                }
            }
        });
    }

    function callAjaxRoutePoints() {
        // reset array marker
        bounds = new google.maps.LatLngBounds();
        markersArray = new Array();
        markersArrow = new Array();
        pointCoordinates = new Array();
        markersDot5 = new Array();
        markersDot10 = new Array();
        drawRoutes = new Array();
        lastPoint = null;
        is_exist_marker = true;
        callMarkerRoute(dataImg, 2);
        callMarkerRoute(dataLocal, 1);
        pushPointCoordinates(dataCoordinate);
        drawRoute();
        map.setCenter(bounds.getCenter());
        map.fitBounds(bounds);
    }


    function checkCamera(isClick) {

        if (isClick) {
            if ($('#showCamera').hasClass('activeCamera')) {
                $('#showCamera').removeClass('activeCamera');
                clearOverlays(markersArray);
                clearOverlays(markersArrow);
            } else if ($('#showCamera').hasClass('activeCamera2')) {
                $('#showCamera').removeClass('activeCamera2');
                $('#showCamera').addClass('activeCamera');
                for (var i = 0; i < infoWindows.length; i++) {
                    infoWindows[i].close();
                }
            } else {
                $('#showCamera').addClass('activeCamera2');
                //display all image
                $.each(dataImg, function (index, datum) {
                    var contentString = '<div class="brite-div-img" data-zindex="' + index + '"><div><img style="width:300px;" src="<?= $this->request->webroot ?>files/image/' + datum.route_id + '/' + datum.name + '"/></div></div>';
                    var infowindow = new google.maps.InfoWindow({
                        content: contentString,
                        zIndex: index,
                    });
                    infowindow.open(map, markersArray[index]);
                    infoWindows.push(infowindow);
                    var iwOuter = $('.gm-style-iw');
                    iwOuter.parent().addClass('briteClass');
                    iwOuter.parent().parent().addClass('briteClass1');
                    var iwBackground = iwOuter.prev();
                    iwBackground.children(':nth-child(2)').css({'display': 'none'});
                    iwBackground.children(':nth-child(4)').css({'display': 'none'});
                    iwBackground.children(':nth-child(1)').css({'display': 'none'});
                    iwBackground.children(':nth-child(3)').css({'display': 'none'});
                    iwOuter.parent().parent().css({left: '15px', top: '30px'});
                });
                setMarkerToMap(markersArray);
                setMarkerToMap(markersArrow);
            }
        } else {
            if ($('#showCamera').hasClass('activeCamera')) {
                setMarkerToMap(markersArray);
                setMarkerToMap(markersArrow);
            } else if ($('#showCamera').hasClass('activeCamera2')) {
                //display all image
                $.each(dataImg, function (index, datum) {
                    var contentString = '<div class="brite-div-img" data-zindex="' + index + '"><div><img style="width:300px;" src="<?= $this->request->webroot ?>files/image/' + datum.route_id + '/' + datum.name + '"/></div></div>';
                    var infowindow = new google.maps.InfoWindow({
                        content: contentString,
                        zIndex: index,
                    });
                    infowindow.open(map, markersArray[index]);
                    infoWindows.push(infowindow);
                    var iwOuter = $('.gm-style-iw');
                    iwOuter.parent().addClass('briteClass');
                    iwOuter.parent().parent().addClass('briteClass1');
                    var iwBackground = iwOuter.prev();
                    iwBackground.children(':nth-child(2)').css({'display': 'none'});
                    iwBackground.children(':nth-child(4)').css({'display': 'none'});
                    iwBackground.children(':nth-child(1)').css({'display': 'none'});
                    iwBackground.children(':nth-child(3)').css({'display': 'none'});
                    iwOuter.parent().parent().css({left: '15px', top: '30px'});
                });
                setMarkerToMap(markersArray);
                setMarkerToMap(markersArrow);
            } else {
                clearOverlays(markersArray);
                clearOverlays(markersArrow);
            }
        }
    }

    function checkMap(isClick) {
        if (isClick) {
            if ($('#showMap').hasClass('activeMap')) {
                $('#showMap').removeClass('activeMap');
                //clearOverlays(markersDot);
                $('.timeInner').hide();
                hiddenDrawRoute();
            } else {
                $('#showMap').addClass('activeMap');
                //setMarkerToMap(markersDot);
                if ($('#showDate').hasClass('activeDate')) {
                    $('.timeInner').show();
                }
                showDrawRoute();
            }
        } else {
            if ($('#showMap').hasClass('activeMap')) {
                //setMarkerToMap(markersDot);
                showDrawRoute();
            } else {
                //clearOverlays(markersDot);
                hiddenDrawRoute();
            }
        }
    }

    function checkDate(isClick) {
        if (isClick) {
            if ($('#showDate').hasClass('activeDate')) {
                $('#showDate').removeClass('activeDate');
                $('#showDate').addClass('activeDate2');
                $('.timeInner').hide();
                clearOverlays(markersDot);
//                hiddenDrawRoute();
            } else if ($('#showDate').hasClass('activeDate2')) {
                $('#showDate').removeClass('activeDate2');
                setMarkerToMap(markersDot);
            } else {
                $('#showDate').addClass('activeDate');
                if (currentMinute == 5) {
                    $('.timeInner5').show();
                } else if (currentMinute == 10) {
                    $('.timeInner10').show();
                } else {
                    $('.timeInner').show();
                }
//                showDrawRoute();
            }
        } else {
            if ($('#showDate').hasClass('activeDate')) {
                setMarkerToMap(markersDot);
//                $('.timeInner').show();
                showDrawRoute();
            } else {
                $('.timeInner').hide();
//                clearOverlays(markersDot);
//                hiddenDrawRoute();
            }
        }
    }

    $(document).ready(function () {
        $('#showMap').on('click', function () {
            checkMap(1);
        });
        $('#showDate').on('click', function () {
            checkDate(1);
        });
        $('#showCamera').on('click', function () {
            checkCamera(1);
        });
//        $(document).on("click", ".brite-div-img", function () {
//            $(".brite-div-img").each(function (index) {
//                $(this).parent().parent().parent().parent().css('z-index', $(this).attr('data-zindex'));
//            });
//            $(this).parent().parent().parent().parent().css('z-index', '100000000000');
//        });




        $(document).on("click", ".briteClass", function () {
            $(".briteClass").each(function (index) {
                $(this).css('z-index', $(this).find('.brite-div-img').attr('data-zindex'));
            });
            $(this).css('z-index', '100000000000');
        });
        $(document).on("click", ".hiddenMap", function (event) {
            var addImg = ($('#content-right').find('.fix-img-index'));
            if ($('.mobile #content-left').is(':visible')) {
                $('#content-left').css({"display": "none"});
                $('#content-right').css({"display": "block"});
                checkShowMap(idShowMap);
            } else {
                $('#content-left').css({"display": "block"});
                $('#content-right').css({"display": "none"});
            }
        });
        $(document).on("click", ".showMapMB", function (event) {
            var id = $(this).attr('data-bind');
            checkShowMap(id);
        });
<?php if (!empty($id)) : ?>
            var id = <?= $id ?>;
            $('#' + id).css('background-color', '#f1f1f1');
<?php else: ?>
            $('#content-left .item:first').css('background-color', '#f1f1f1');
<?php endif; ?>
        // execute
        getCoordinates(<?= $id ?>);
        scrollAtLeftSideBar();
        // console.log(coordinate);
        // map options
        var options = {
            zoom: 14,
            center: new google.maps.LatLng(dataLocal[0].latitude, dataLocal[0].longitude), // centered US
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            mapTypeControl: true,
            scaleControl: true,
            mapTypeControlOptions: {
                mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain'],
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
            },
//            maxZoom: 19,
        };
//        tunglt : tùy biến label của marker để hiện thị ngày tháng ------ start
        google.maps.Marker.prototype.setLabel = function (label) {
            this.label = new MarkerLabel({
                map: this.map,
                marker: this,
                text: label
            });
            this.label.bindTo('position', this, 'position');
        };
        var MarkerLabel = function (options) {
            this.setValues(options);
            this.span = document.createElement('span');
            this.span.className = 'map-marker-label';
        };
        MarkerLabel.prototype = $.extend(new google.maps.OverlayView(), {
            onAdd: function () {
                this.getPanes().overlayImage.appendChild(this.span);
                var self = this;
                this.listeners = [
                    google.maps.event.addListener(this, 'position_changed', function () {
                        self.draw();
                    })
                ];
            },
            draw: function () {


                var text = String(this.get('text'));
                var position = this.getProjection().fromLatLngToDivPixel(this.get('position'));
                this.span.innerHTML = text;
                this.span.style.left = (position.x) - (30) + 'px';
                this.span.style.top = (position.y) + 'px';
                if ($('#showDate').hasClass('activeDate')) {
                    if (currentMinute == 5) {
                        $('.timeInner5').show();
                    } else if (currentMinute == 10) {
                        $('.timeInner10').show();
                    } else
                        $('.timeInner').show();
                } else {
                    $('.timeInner').hide();
                }
            }
        });
//        tunglt : tùy biến label của marker để hiện thị ngày tháng ------ end
        // for select minute
        $('#selectMinute').on('change', function () {
            currentMinute = $('#selectMinute').val();
            setMarkerToMap(markersDot);
        });
        // init map
        map = new google.maps.Map(document.getElementById('map_canvas'), options);
        map.setTilt(0); //disable 45degree
        callAjaxRoutePoints();
        $("#load-more").click(function () {
            var offset = $(this).attr('data-id');
            offset = parseInt(offset) + 10;
            load_more(offset);
            $(this).attr('data-id', offset);
            /* fix image to center*/
            fixHeightShowDataDiv();
        });
        /* open new windows Survey */
        $(document).on("click", ".target", function (e) {
            e.preventDefault();
            var sTarget = $(this).attr('href');
            if ($(this).hasClass('targetOnly')) {
                showTarget(sTarget);
            }
        });
        /* fix image to center*/
        fixHeightShowDataDiv();
        /* open new windows Survey */
//        $(document).on("click",".target",function (e) {
//            e.preventDefault();
//            var url = $(this).attr('rel');
//            if ($(this).hasClass('targetOnly')) {
//                $(this).removeClass('targetOnly');
//                window.open(url, '_blank');
//            }
//        });
        google.maps.event.addListener(map, 'zoom_changed', function (event) {
            zoomChangeBoundsListener = google.maps.event.addListener(map, 'bounds_changed', function (event) {
                changeLabelAfterZoomChanged();
                google.maps.event.removeListener(zoomChangeBoundsListener);
            });
        });
    });
    /* open new windows Survey */

    function showTarget(sTarget) {
        var checkUrl = true;
        $.each(blankUrl, function (indexUrl, valueUrl) {
            if (sTarget === valueUrl) {
                $.each(blankWinA, function (index, value) {
                    if (indexUrl === index) {
                        /* check exist windows */
                        if (value && !value.closed) {
                            value.focus();
                            checkUrl = false;
                        } else {
                            blankWin = window.open(sTarget, "_blank");
                            blankWinA.push(blankWin);
                            blankUrl.push(sTarget);
                            blankUrl.splice(indexUrl, 1);
                            blankWinA.splice(index, 1);
                            checkUrl = false;
                        }
                    }
                });
            }
        });
        if (checkUrl == true) {
            blankWin = window.open(sTarget, "_blank");
            blankWinA.push(blankWin);
            blankUrl.push(sTarget);
        }
    }

</script>