<?php
require_once __DIR__.'/lib/init.php';
?>

<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test</title>
    <!-- CSS: OpenLayers -->
    <link href="/localguru/3rdparty/bower-asset/openlayers3/css/ol.css" rel="stylesheet">
    <!-- CSS: Bootstrap -->
    <link href="/localguru/3rdparty/bower-asset/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <script type="text/css">
      #map {
         width:100%;
         height:800px;
      }
      #info {
        position: absolute;
        height: 1px;
        width: 1px;
        z-index: 100;
      }
      .tooltip.in {
        opacity: 1;
        filter: alpha(opacity=100);
      }
      .tooltip.top .tooltip-arrow {
        border-top-color: white;
      }
      .tooltip-inner {
        border: 2px solid white;
      }    </script>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">BuurtLinux</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Kaart</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
       <div class="page-header">
            <div id="map" class="map"><div id="info"></div></div>
       </div>
    </div>
    <script src="/localguru/3rdparty/bower-asset/openlayers3/build/ol.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/localguru/3rdparty/bower-asset/bootstrap/dist/js/bootstrap.js"></script>
    <!-- JS: LocalGuru -->
    <script src="js/GuruMap.js"></script>
  </body>
</html>
