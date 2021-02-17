<?php
include '../src/DiscordWebhook.php';
include '../src/MessageEmbed.php';
include '../src/Util.php';

$webhook = new DiscordWebhook('WEBHOOK_URL');

$embed = new MessageEmbed();
$messageEmbed = $embed
		-> setTitle('Hello')																			// Set title
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
		-> setTimestamp()																				// Set embed footer timestamp to current one
		-> setTimestamp('2021-02-15T17:38:59+01:00')													// Set embed footer timestamp to custom one
		-> setFooter('My footer', 'https://example.com/footer_image.png')								// Set embed footer text and icon URL (o)
		-> get();

$webhook -> setContent('Hello world')																	// Set webhook message content
		 -> setUsername('PHP Webhook')																	// Set webhook username (o)
		 -> setAvatar('https://example.com/webhook_avatar.png')											// Set webhook avatar (o)
		 -> attachFile("https://i.imgur.com/LPqyb6S.png", "image/png", "remoteImage.png")				// Attach file from URL, type, name (o)
		 -> attachFile("../resources/image.png", "image/png", "localImage.png")							// Attach file using local path (o)
		 -> setTTS(false)																				// Set webhook content TTS (o, default=false)
		 -> addEmbed($messageEmbed)																		// Add embed to webhook (from MessageEmbed class)
		 //-> addMentionsParse("everyone", "roles", "users")											// Add allowed_mentions parse
		 //-> addMentionsRoles("000000000000000000")													// Add allowed_mentions roles 
		 //-> addMentionsUsers("000000000000000000")													// Add allowed_mentions users
		;

/* Whether the webhook sends or errors, you can get the Discord API response object by calling the getResponse() method after the send() method */
if($webhook->send())
{
	echo "Webhook has been successfully sent.\n";
}
else
{
	echo "There was an error sending the webhook.\n";
}

$discordWebhook = $webhook->get();
echo json_encode($discordWebhook, JSON_PRETTY_PRINT);

/* You can also access the webhook properties

		$webhook->get()->content
		$webhook->get()->username
		$webhook->get()->avatar_url
		$webhook->get()->tts
		$webhook->get()->file
		$webhook->get()->embeds
			$webhook->get()->embeds[i]->title
			$webhook->get()->embeds[i]->description
			$webhook->get()->embeds[i]->color
			$webhook->get()->embeds[i]->fields;
				$webhook->get()->embeds[i]->fields[i];
					$webhook->get()->embeds[i]->fields[i]->name;
					$webhook->get()->embeds[i]->fields[i]->value;
					$webhook->get()->embeds[i]->fields[i]->inline;
			$webhook->get()->embeds[i]->timestamp;
			$webhook->get()->embeds[i]->thumbnail
				$webhook->get()->embeds[i]->thumbnail->url
			$webhook->get()->embeds[i]->image
				$webhook->get()->embeds[i]->image->url
			$webhook->get()->embeds[i]->video
				$webhook->get()->embeds[i]->video->url
			$webhook->get()->embeds[i]->author
				$webhook->get()->embeds[i]->author->name
				$webhook->get()->embeds[i]->author->url
				$webhook->get()->embeds[i]->author->icon_url
			$webhook->get()->embeds[i]->footer
				$webhook->get()->embeds[i]->footer->text
				$webhook->get()->embeds[i]->footer->icon_url
		$webhook->get()->allowed_mentions
			$webhook->get()->allowed_mentions->parse
			$webhook->get()->allowed_mentions->roles
			$webhook->get()->allowed_mentions->users

*/

/* 

(o) => optional

*/