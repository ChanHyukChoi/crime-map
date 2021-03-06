<?php
include 'dbinf.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>범죄 현황 알리미</title>
    <script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId=kfnhnf7qdq"></script>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/MarkerClustering.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Do+Hyeon&family=Nanum+Gothic+Coding&family=Nanum+Gothic:wght@800&display=swap" rel="stylesheet">
<style>
#title { 
	font-family: 'Do Hyeon', sans-serif;
	font-size: 50px;
}

#type { font-family: 'Nanum Gothic Coding', monospace;
	font-size: 20px;
}
	
img { 
	display: block;
	margin: 0px auto;
}
</style>

</head>
<body>
<hr width="40%">
<h1 id="title" style="text-align: center;">범 죄 &nbsp;현 황&nbsp; 알 리 미</h1>
<img src="img/bell.png" width="130px">
<br>
<hr width="40%">
<br><br>

<table id="type" width="40%" align="center">
<tr>
<td><input type="radio" name="type" value="전체">전체</td>
<td><input type="radio" name="type" value="살인">살인</td>
<td><input type="radio" name="type" value="성폭력">성폭력</td>
<td><input type="radio" name="type" value="아동폭행">아동폭행</td>
<td><input type="radio" name="type" value="강도">강도</td>
<td><input type="radio" name="type" value="도둑">도둑</td>
<td><input type="radio" name="type" value="사기">사기</td>
<td><input type="radio" name="type" value="기타">기타</td>

</tr>
</table>
<br>

<div id="map" style="width:40%;height:500px;margin-top: auto;margin-left: auto;margin: 0px auto; border: solid 1px gray;"></div>

<script>
var map = new naver.maps.Map('map', {
    center: new naver.maps.LatLng(36.299141, 128.044877),
	                    zoom: 7
});


var markers = [];


function CustomMarker(lat,lng,crimeID,crimeGubun,serious,crimeDate,crimeTime){
var contents_html="";
if(serious==1){
	contents_html='<div style="padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px;background-color:#b12121;color:white;text-align:center;border:1px solid #831616;border-radius:14px;opacity:75%" onmouseover="javascript:overCrime(\''+crimeID+'\');" onmouseout="javascript:outCrime(\''+crimeID+'\');">'+
	'<div style="font-weight:bold;font-size:14px">'+crimeGubun+'</div>'+
	'<div style="font-weight:normal;font-size:13px;margin-top:3px;display:none" id="'+crimeID+'">'+crimeDate+'<br/>'+crimeTime+'</div>'+
	'</div>';
}


if(serious==2){
        contents_html='<div style="padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px;background-color:#e0692f;color:white;text-align:center;border:1px solid #831616;border-radius:14px;opacity:75%" onmouseover="javascript:overCrime(\''+crimeID+'\');" onmouseout="javascript:outCrime(\''+crimeID+'\');">'+
       '<div style="font-weight:bold;font-size:14px">'+crimeGubun+'</div>'+
       '<div style="font-weight:normal;font-size:13px;margin-top:3px;display:none" id="'+crimeID+'">'+crimeDate+'<br/>'+crimeTime+'</div>'+
       '</div>';
}


if(serious==3){
        contents_html='<div style="padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px;background-color:#d3cc07;color:white;text-align:center;border:1px solid #a09b07;border-radius:14px;opacity:75%" onmouseover="javascript:overCrime(\''+crimeID+'\');" onmouseout="javascript:outCrime(\''+crimeID+'\');">'+
       '<div style="font-weight:bold;font-size:14px">'+crimeGubun+'</div>'+
       '<div style="font-weight:normal;font-size:13px;margin-top:3px;display:none" id="'+crimeID+'">'+crimeDate+'<br/>'+crimeTime+'</div>'+
       '</div>';
}



var marker = new naver.maps.Marker({
	position: new naver.maps.LatLng(lat,lng),
	map: map,
	title: crimeGubun,
	icon: {
		content: contents_html,
		size: new naver.maps.Size(38,58),
		anchor: new naver.maps.Point(19,58),
		},
	draggable: false
});
return marker;
};

<?php
$sql = "select crime_code,lat,lng,crime_gubun,crime_time,serious from table_crime_map";
$result = mysqli_query($conn,$sql);
$idx = 0;
while($row = mysqli_fetch_array($result)){
	$idx++;
	if($row["crime_time"]){
		$crime_time_arr = explode(" ",$row["crime_time"]);
		$crime_date = $crime_time_arr[0];
		$crime_time = $crime_time_arr[1];
	}
	$crimeID = "crime".$idx;

?>

var marker<?=$idx?> = new CustomMarker(<?=$row["lat"]?>,<?=$row["lng"]?>,"<?=$crimeID?>","<?=$row["crime_gubun"]?>",<?=$row["serious"]?>,"<?=$crime_date?>","<?=$crime_time?>");
markers.push(marker<?=$idx?>);
<?php
}
?>


$(document).ready(function() {
	var htmlMarker1 = {
	content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(https://navermaps.github.io/maps.js.ncp/docs/img/cluster-marker-1.png);background-size:contain;"></div>',
	size: N.Size(40, 40),
	anchor: N.Point(20, 20)
 },
	 
	htmlMarker2 = {
	content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(https://navermaps.github.io/maps.js.ncp/docs/img/cluster-marker-2.png);background-size:contain;"></div>',
	size: N.Size(40, 40),
	anchor: N.Point(20, 20)
} ,
																					        htmlMarker3 = {
																						content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(https://navermaps.github.io/maps.js.ncp/docs/img/cluster-marker-3.png);background-size:contain;"></div>',
	size: N.Size(40, 40),
	anchor: N.Point(20, 20)
	},
		
	htmlMarker4 = {
	content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(https://navermaps.github.io/maps.js.ncp/docs/img/cluster-marker-4.png);background-size:contain;"></div>',
	size: N.Size(40, 40),
	anchor: N.Point(20, 20)
	},
		
	htmlMarker5 = {
	content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(https://navermaps.github.io/maps.js.ncp/docs/img/cluster-marker-5.png);background-size:contain;"></div>',
	size: N.Size(40, 40),
	anchor: N.Point(20, 20)
	};


var markerClustering = new MarkerClustering({
	minClusterSize: 2,
	maxZoom: 13,
	map: map,
	markers: markers,
	disableClickZoom: false,
	gridSize: 120,
	icons: [htmlMarker1, htmlMarker2, htmlMarker3, htmlMarker4, htmlMarker5],
	indexGenerator: [10, 100, 200, 500, 1000],
	stylingFunction: function(clusterMarker, count) {
	$(clusterMarker.getElement()).find('div:first-child').text(count);
	}
});


});


function overCrime(childID){
        $("#"+childID).show();
}

function outCrime(childID){
        $("#"+childID).hide();
}


</script>
</body>
</html>

<?php
include 'dbend.php';
?>
