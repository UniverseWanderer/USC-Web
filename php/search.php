<?php
	require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
	$myToken = "EAACkSyLxrXEBAO0HPDRin9ZAdZBl73RH2F1ZAKSMg3TsxpFQN23gqPYGYOYtEbnzcjZBtGY7ZCJE8cJa7tTSDta8LqBvYZB4BbwK61IeyRbDefDs6VY899MhTQK4QD5TJm0VKXnvhGNbqxK209glIkrGbqDgHA2KkZD";
	$myAppId = "180642615766385";
	$myAppSecret = "fd0efcc434abb02d01f6663afc79c559";
	$googleKey = "AIzaSyBh4kx3UnprL6Ih5fGPzvBJH8YPici04GE";
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Homework 6</title>
<style>
.field1 {
	border:2px solid #cccccc;
	background-color:#eeeeee;
	width:700px;
	margin:0px auto;
}
.hide  {display:none;}
#header{
	text-align:center;
	font-family:"Times New Roman";
	font-style:italic;
	font-size:25px;
	border-bottom:1px solid #cccccc;
	padding:10px;
}

.mytable{
	width:900px;
	margin:0px auto;
	border-collapse: collapse;
	font-family:arial;
}

.tdnew{
	border:1px solid #aaaaaa;
	text-align: left;
	background-color:#eeeeee;
}
th{
	background-color:#eeeeee;
	border:1px solid #aaaaaa;
	text-align: left;
}
.th1{width:20%;}
.th2{width:65%;}
.th3{width:15%;}
.icon {width:30px;height:30px;}

.clickable{
	font-family:"Times New Roman";
	color:blue;
	text-decoration:underline;
}
.notfound{
	background-color:#eeeeee;
	text-align:center;
	font-family:"Times New Roman";
	width:900px;
	height:20px;
	margin:0px auto;
	border:2px solid #cccccc;
}
.found{
	background-color:#aaaaaa;
	text-align:center;
	font-family:"Times New Roman";	
	width:900px;
	height:20px;
	margin:0px auto;
}
.bigicon {width:80px;height:80px;}
</style>

<script type="text/javascript">
function optionChange(){
	var obj=document.getElementById("opt");
	if(obj.value=="place"){
		var objPlace = document.getElementsByClassName("hide");
		var i;
		for(i=0;i<objPlace.length;i++)
			objPlace[i].style.display='table-cell';
	}
	else{
		var objPlace = document.getElementsByClassName("hide");
		var i;
		for(i=0;i<objPlace.length;i++)
			objPlace[i].style.display='none';
	}
}
function onReset(){
	document.getElementById("kyw").removeAttribute("value");
	document.getElementById("opt").setAttribute("value","user");
	document.getElementById("loc").removeAttribute("value");
	document.getElementById("dis").removeAttribute("value");	
	optionChange();
	document.getElementById("phpContent").innerHTML="";
}

function toggleDisplayTable(index){
	var objA=document.getElementById("albumTable");
	var objP=document.getElementById("postTable");
	if(index==1){
	  objP.style.display='none';
	  if(objA.style.display!="none")
		objA.style.display='none';
	  else
		objA.style.display='table';
	}
	if(index==2){
	  objA.style.display='none';
	  if(objP.style.display!="none")
		objP.style.display='none';
	  else
		objP.style.display='table';
	}
}

function toggleDisplayPicture(index){
	if(index==0)
		var obj=document.getElementById("alb0");
	else if(index==1)
		var obj=document.getElementById("alb1");
	else if(index==2)
		var obj=document.getElementById("alb2");
	else if(index==3)
		var obj=document.getElementById("alb3");
	else if(index==4)
		var obj=document.getElementById("alb4");
	
	if(obj.style.display!="none")
		obj.style.display='none';
	else
		obj.style.display='table-cell';
}

</script>

</head>
<body>

<?php
//define variables and set to empty
$keyword = $type = $location = $distance = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!empty($_POST["keyword"])) {
    $keyword = test_input($_POST["keyword"]);
  }
  
  if (!empty($_POST["type"])) {
    $type = test_input($_POST["type"]);
	
	if($type=="event"){
	  $FBurl = "/search?q=$keyword&type=$type&fields=id,name,place,picture.width(700).height(700)";
	}
	else if(($type=="place")&&!empty($_POST["location"])){
		$location = test_input($_POST["location"]);
		// obtain position from Google API
		$address="";
		$c=array();
		for($i=0;$i<strlen($location);$i=$i+1){
			$c[]=substr($location,$i,1);
			if($c[$i]==' ')
				$address=$address.'+';
			else
				$address=$address.$c[$i];
		}
		$GMurl = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$googleKey";
		$GMJson = file_get_contents($GMurl);
		$GMdata = json_decode($GMJson);
		// get latitude and longitude
		$lat = $GMdata->results[0]->geometry->location->lat;
		$lng = $GMdata->results[0]->geometry->location->lng;
		if (!empty($_POST["distance"])) {
			$distance = test_input($_POST["distance"]);
		}
		$FBurl = "/search?q=$keyword&type=$type&center=$lat,$lng&distance=$distance&fields=id,name,picture.width(700).height(700)";
	}
	else{
	  $FBurl = "/search?q=$keyword&type=$type&fields=id,name,picture.width(700).height(700)";
	}
  }
  
  //Obtain data from FB
  $fb = new Facebook\Facebook([
	  'app_id' => $myAppId,
	  'app_secret' => $myAppSecret,
	  'default_graph_version' => 'v2.8',
	  'default_access_token' => $myToken,
  ]);
  
  $FBresponse = $fb->get($FBurl);
  $FBdata = $FBresponse->getDecodedBody();

}
else if($_GET){
	$keyword=$_GET['keyword'];
	$type=$_GET['type'];
	$location=$_GET['a'];
	$distance=$_GET['distance'];
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

<fieldset class="field1">
<div id="header">Facebook Search</div>

<form method="post" id="myForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table>
  <tr>
    <td>Keyword</td>
	<td><input type="text" id="kyw" name="keyword" value="<?php echo $keyword;?>" required></td></tr>
    <br>
  <tr>
	<td>Type:  </td>
	<td><select name="type" id="opt" value="<?php echo $type;?>" onchange="optionChange()">
			<option value="user">Users</option>
			<option value="page">Pages</option>
			<option value="event">Events</option>
			<option value="place">Places</option>			
			<option value="group">Groups</option>
		</select>
    </td></tr>
  
  <tr>
    <td class="hide">Location</td>
	<td class="hide"><input type="text" id="loc" name="location" value="<?php echo $location;?>"></td>
	<td class="hide">Distance(meters)</td>
	<td class="hide"><input type="text" id="dis" name="distance" value="<?php echo $distance;?>"></td>
	<td><br/></td>
  </tr>

  <tr>
	<td></td>
    <td><input type="submit" name="search" value="Search">
        <input type="reset" name="clear" value="Clear" onClick="onReset()">
	</td></tr>
	
  </table>
</form>
</fieldset>
<br/><br/>

<div id="phpContent">
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if($type!="event"){
	  echo "<div><table class='mytable'>";
	  echo "<tr><th class='th1'>"."Profile Photo"."</th><th class='th2'>"."Name"."</th><th class='th3'>"."Details"."</th></tr>"; 
	  $nums=count($FBdata["data"]);
	  $detailText="Details";
	  for($i=0;$i<$nums;$i=$i+1){
		  $url=$FBdata["data"][$i]["picture"]["data"]["url"];
		  $name=$FBdata["data"][$i]["name"];
		  $myId=$FBdata["data"][$i]["id"];
		  $newurl="?id=$myId&keyword=$keyword&type=$type&a=$location&distance=$distance";
		  echo "<tr>
				<td class='tdnew'><a href=".$url." target='_blank'><img class='icon' src=".$url."></a></td>
				<td class='tdnew'>".$name."</td>
				<td class='tdnew'><a href='$newurl'>".$detailText."</a></td>
				</tr>";
	  }
  }
  else{
	  echo "<div><table class='mytable'>";
	  echo "<tr><th class='th1'>"."Profile Photo"."</th><th class='th2'>"."Name"."</th><th class='th3'>"."Place"."</th></tr>"; 
	  $nums=count($FBdata["data"]);	  
	  for($i=0;$i<$nums;$i=$i+1){
		  $url=$FBdata["data"][$i]["picture"]["data"]["url"];
		  $name=$FBdata["data"][$i]["name"];
		  $detailText=$FBdata["data"][$i]["place"]["name"];
		  echo "<tr>
				<td class='tdnew'><a href=".$url." target='_blank'><img class='icon' src=".$url."></a></td>
				<td class='tdnew'>".$name."</td>
				<td class='tdnew'>".$detailText."</td>
				</tr>";
	  }
  }
}
else if($_GET){	
	$getId=$_GET['id'];
	
	//Obtain data from FB
	$fb = new Facebook\Facebook([
	  'app_id' => $myAppId,
	  'app_secret' => $myAppSecret,
	  'default_graph_version' => 'v2.8',
	  'default_access_token' => $myToken,
	]);
	
	$FBurl="/$getId?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
	$FBresponse = $fb->get($FBurl);
	$FBdata = $FBresponse->getDecodedBody();
	
	//Display albums and post	
	if((array_key_exists("albums",$FBdata))&&(array_key_exists("posts",$FBdata))){
		$albums=$FBdata["albums"]["data"];
		$album_nums=count($albums);
		$posts=$FBdata["posts"]["data"];
		$post_nums=count($posts);

		echo "<div class='found'><a href=# class='clickable' onClick='toggleDisplayTable(1)'>Albums</a></div><br/>";	
		echo "<div><table id='albumTable' class='mytable' style='display:none;'>";
		for($i=0;$i<$album_nums&&$i<5;$i=$i+1){
			$album_name=$albums[$i]["name"];			
			echo "<tr><td class='tdnew'><a href=# class='clickable' onClick='toggleDisplayPicture($i)'>".$album_name."</a></td></tr><tr><td class='tdnew' style='display:none;' id='alb$i'>";
			$photo_nums=count($albums[$i]["photos"]["data"]);
			for($j=0;$j<$photo_nums&&$j<2;$j=$j+1){
				$photoId=$albums[$i]["photos"]["data"][$j]["id"];
				$Purl="/$photoId/picture/?redirect=false";
				$Presponse = $fb->get($Purl);			
				$Pdata = $Presponse->getDecodedBody();
				$hd_photo=$Pdata["data"]["url"];
				$album_photo=$albums[$i]["photos"]["data"][$j]["picture"];
				echo "<a href=".$hd_photo." target='_blank'><img class='bigicon' src=".$album_photo."></a>&nbsp";
			}
			echo "</td></tr>";
		}
		echo "</table></div><br/>";
		
		echo "<div class='found'><a href=# class='clickable' onClick='toggleDisplayTable(2)'>Posts</a></div><br/>";
		echo "<div><table id='postTable' class='mytable' style='display:none;'>";
		echo "<tr><th>Message</th></tr>";
		for($i=0;$i<$post_nums&&$i<5;$i=$i+1){
			$post_msg=$posts[$i]["message"];
			echo "<tr><td class='tdnew'>".$post_msg."</td></tr>";
		}
		echo "</div></table><br/>";
	}
	else{
		echo "<div class='notfound'>No Albums has been found</div>";
		echo "<br/>";
		echo "<div class='notfound'>No Posts has been found</div>";
	}
}

?>
</div>

</body>
</html>



