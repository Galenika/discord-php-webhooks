<?php
include '../src/MessageEmbed.php';
include '../src/Util.php';

$embed = new MessageEmbed();
$embed  -> setTitle('Hello')																			// Set title
		-> setDescription('Hello World')																// Set description
		-> setColor("BLUE")																				// Set color using color name
		-> setColor("FF0000")																			// Set color using HEX code
		-> setColor([255, 255, 255])																	// Set color using RGB
		-> setURL('https://example.com')																// Set embed URL
		-> setAuthor('Author name', 'https://example.com/author_image.jpeg', 'https://example.com')		// Set author name, icon URL (o) and URL (o)
		-> setThumbnail('https://example.com/thumbnail.png')											// Set embed thumbnail
		-> addField('Field name', 'Field value')														// Add one field
		-> addFields(																					// Add multiple fields at once
				[																						// Initialize array of fields
					["name"=>"Field name 1", "value"=>"Field value 1"],									// Add new field
					["name"=>"Field name 2", "value"=>"Field value 2", "inline"=>true]					// Add new field
				]
			)
		-> spliceFields(
				1,																						// Starting index
				2, 																						// Delete count (o, default=1)
				["name"=> "New field 1", "value"=>"New field 1"],										// New field 1 (o)
				["name"=> "New field n", "value"=>"New field n"]										// New field n (o)
			)
		-> setImage('https://example.com/embed_image.png')												// Set embed image
		-> setVideo('https://example.com/embed_video.mp4')												// Set embed video (possible deprecated)
		-> setTimestamp()																				// Set embed footer timestamp to current one
		-> setTimestamp('2021-02-15T17:38:59+01:00')													// Set embed footer timestamp to custom one
		-> setFooter('My footer', 'https://example.com/footer_image.png')								// Set embed footer text and icon URL (o)
		;

$messageEmbed = $embed->get();																			// Retrieve embed object
echo json_encode($messageEmbed, JSON_PRETTY_PRINT);														// Convert embed object to JSON and print it

/* You can also access the embed properties

		$embed->get()->title
		$embed->get()->description
		$embed->get()->color
		$embed->get()->fields;
			$embed->get()->fields[i];
				$embed->get()->fields[i]->name;
				$embed->get()->fields[i]->value;
				$embed->get()->fields[i]->inline;
		$embed->get()->timestamp;
		$embed->get()->thumbnail
			$embed->get()->thumbnail->url
		$embed->get()->image
			$embed->get()->image->url
		$embed->get()->video
			$embed->get()->video->url
		$embed->get()->author
			$embed->get()->author->name
			$embed->get()->author->url
			$embed->get()->author->icon_url
		$embed->get()->footer
			$embed->get()->footer->text
			$embed->get()->footer->icon_url

*/

/* 

(o) => optional

* All of the MessageEmbed methods are optional
*/