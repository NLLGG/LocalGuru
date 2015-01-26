<?php
require_once __DIR__.'/init.php';
require_once LIBDIR.'/db_access.php';

$db = new Database();
if (!$db->connect())
{
	return NULL;
}
$result = $db->connection()->query('SELECT * from users');

if (!$result)
{
	die('No results on query! Error: ' . mysql_error());
}

// Creates the Document.
$dom = new DOMDocument('1.0', 'UTF-8');

// Creates the root KML element and appends it to the root document.
$node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$parNode = $dom->appendChild($node);

// Creates a KML Document element and append it to the KML element.
$dnode = $dom->createElement('Document');
$docNode = $parNode->appendChild($dnode);

// Creates the two Style elements, one for restaurant and one for bar, and append the elements to the Document element.
$guruStyleNode = $dom->createElement('Style');
$guruStyleNode->setAttribute('id', 'guruStyle');
$guruIconstyleNode = $dom->createElement('IconStyle');
$guruIconstyleNode->setAttribute('id', 'guruIcon');
$guruIconNode = $dom->createElement('Icon');
$guruHref = $dom->createElement('href', 'img/tux.png');
$guruIconNode->appendChild($guruHref);
$guruIconstyleNode->appendChild($guruIconNode);
$guruStyleNode->appendChild($guruIconstyleNode);
$docNode->appendChild($guruStyleNode);

// Iterates through the MySQL results, creating one Placemark for each row.
while ($row = @mysqli_fetch_assoc($result))
{
	// Creates a Placemark and append it to the Document.

	$node = $dom->createElement('Placemark');
	$placeNode = $docNode->appendChild($node);

	// Creates an id attribute and assign it the value of id column.
	$placeNode->setAttribute('id', 'placemark' . $row['id']);

	// Create name, and description elements and assigns them the values of the name and address columns from the results.
	$nameStr = $row['usr_firstname'] . ' ' . $row['usr_lastname'];
	$nameNode = $dom->createElement('name', $nameStr);
	$placeNode->appendChild($nameNode);
	$descNode = $dom->createElement('description', $row['usr_street']);
	$placeNode->appendChild($descNode);
	$styleUrl = $dom->createElement('styleUrl', '#guruStyle');
	$placeNode->appendChild($styleUrl);

	// Creates a Point element.
	$pointNode = $dom->createElement('Point');
	$placeNode->appendChild($pointNode);

	// Creates a coordinates element and gives it the value of the lng and lat columns from the results.
	$coorStr = $row['usr_loc_lon'] . ','  . $row['usr_loc_lat'];
	$coorNode = $dom->createElement('coordinates', $coorStr);
	$pointNode->appendChild($coorNode);
}

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml');
echo $kmlOutput;

?>
