<?php
class MessageEmbed {

	/* Embed object */
	private object $embed;

	/* Total text length */
	public int $totalLength;

	/* Constants */
	const EMBED_MAX_TOTAL_LENGTH = 6000;
	const TITLE_MAX_LENGTH = 256;
	const DESCRIPTION_MAX_LENGTH = 2048;
	const FIELDS_MAX_SIZE = 25;
	const FIELD_NAME_MAX_LENGTH = 256;
	const FIELD_VALUE_MAX_LENGTH = 1024;
	const FOOTER_TEXT_MAX_LENGTH = 2048;
	const AUTHOR_NAME_MAX_LENGTH = 256;

	function __construct ()
	{
		/* Initialize the embed object */
		$this->embed = new stdClass();

		/* Initialize total length to 0 */
		$this->totalLength = 0;

		/* Initialize embed to "rich" type */
		$this->embed->type = "rich";

		/* Set default values for required params */
		$this->embed->title = null;
		$this->embed->description = null;
		$this->embed->color = null;
		$this->embed->fields = array();
		$this->embed->timestamp = null;
		$this->embed->thumbnail = (object) array("url"=>null);;
		$this->embed->image = (object) array("url"=>null);
		$this->embed->author = (object) array("name"=>null, "url"=>null, "icon_url"=>null);
		$this->embed->footer = (object) array("text"=>null, "icon_url"=>null);

		/* Initialize the errors array */
		$this->errors = array();
	}

	public function setColor (string|int|array $color) : object
	{
		if(in_array(gettype($color), ["string", "integer"]))
		{
			if(array_key_exists(strtoupper($color), Util::Colors))
			{
				$this->embed->color = hexdec(Util::Colors[strtoupper($color)]);
			}
			else
			{
				$this->embed->color = hexdec($color);
			}
		}
		else
		{
			$this->embed->color = ($color[0] << 16) + ($color[1] << 8) + $color[2];
		}

		return $this;
	}

	public function setTitle (string|int|array $title) : object
	{
		$title = Util::resolveString($title);
		if(strlen($title) > self::TITLE_MAX_LENGTH)
		{
			Util::triggerError(sprintf("Title must be fewer than or equal to %d characters", self::TITLE_MAX_LENGTH));
		}
		
		$this->updateTotalLength(strlen($title) - strlen($this->embed->title));

		$this->embed->title = $title;
		
		return $this;
	}

	public function setURL (string $url) : object
	{
		$this->embed->url = $url;

		return $this;
	}

	public function setAuthor (string $name, string $iconURL = null, string $url = null) : object
	{

		$this->updateTotalLength(strlen($name) - strlen($this->embed->author->name));
		$this->embed->author->name = $name;

		$this->embed->author->icon_url = $iconURL;
		$this->embed->author->url = $url;

		return $this;
	}

	public function setDescription (string|int $description)
	{
		$description = Util::resolveString($description);
		if(strlen($description) > self::DESCRIPTION_MAX_LENGTH)
		{
			Util::triggerError(sprintf("Description must be fewer than or equal to %d characters", self::DESCRIPTION_MAX_LENGTH));
		}

		$this->updateTotalLength(strlen($description) - strlen($this->embed->description));
		$this->embed->description = $description;

		return $this;
	}

	public function setThumbnail (string $thumbnail) : object
	{
		$this->embed->thumbnail->url = $thumbnail;

		return $this;
	}

	public function addField (string $name, string $value, bool $inline = false) : object
	{
		if(count($this->embed->fields) >= self::FIELDS_MAX_SIZE)
		{
			Util::triggerError(sprintf("Embed cannot exceed %d fields", self::FIELDS_MAX_SIZE));
		}
		else
		{
			if(strlen($name) > self::FIELD_NAME_MAX_LENGTH)
			{
				Util::triggerError(sprintf("Field name must be fewer than or equal to %d characters", self::FIELD_NAME_MAX_LENGTH));
			}

			if(strlen($name) > self::DESCRIPTION_MAX_LENGTH)
			{
				Util::triggerError(sprintf("Field description must be fewer than or equal to %d characters", self::DESCRIPTION_MAX_LENGTH));
			}

			$field = new stdClass();

			$this->updateTotalLength(strlen($name));
			$field->name = $name;

			$this->updateTotalLength(strlen($value));
			$field->value = $value;

			$field->inline = $inline;

			array_push($this->embed->fields, $field);
		}
		return $this;
	}

	public function addFields (array $fields) : object
	{
		if(count($fields) > 0)
		{
			$fields = (object) $fields;
			foreach($fields as $field)
			{
				$field = (object) $field;
				if(property_exists($field, 'name') && property_exists($field, 'value'))
				{
					if(property_exists($field, 'inline'))
					{
						if(gettype($field->inline) === "boolean")
						{
							$inline = $field->inline;
						}
						else
						{
							$inline = false;
						}
					}
					else
					{
						$inline = false;
					}

					$this->addField($field->name, $field->value, $inline);
				}
				else
				{
					Util::triggerError(sprintf("Incorrectly formatted field: %s", json_encode($field)));
				}
			}
		}

		return $this;
	}

	public function spliceFields (int $index, int $deleteCount = 1, array ...$fields) : object
	{
		if(($index + $deleteCount) > count($this->embed->fields))
		{
			Util::triggerError("Splice delete count exceeds array length from selected starting index", "OUT_OF_BOUNDS");
		}

		for($i = 0; $i < $deleteCount; $i++)
		{
			$currentField = $this->embed->fields[$index + $i];

			$this->updateTotalLength(-strlen($currentField->name));
			$this->updateTotalLength(-strlen($currentField->value));
		}

		array_splice($this->embed->fields, $index, $deleteCount);

		$this->addFields($fields);

		return $this;
	}

	public function setImage (string $imageURL) : object
	{
		$this->embed->image->url = $imageURL;

		return $this;
	}

	public function setTimestamp (string|int $timestamp = -1) : object
	{
		if($timestamp < 0) $timestamp = date("c");

		$this->embed->timestamp = $timestamp;

		return $this;
	}

	public function setFooter ($text, $iconURL = null) : object
	{
		if(strlen($text) > self::FOOTER_TEXT_MAX_LENGTH)
		{
			Util::triggerError(sprintf("Footer text must be fewer than or equal to %d characters", self::FOOTER_TEXT_MAX_LENGTH));
		}
		
		$this->updateTotalLength(strlen($text) - strlen($this->embed->footer->text));
		$this->embed->footer->text = $text;

		$this->embed->footer->icon_url = $iconURL;

		return $this;
	} 

	public function get () : object
	{
		if($this->totalLength > self::EMBED_MAX_TOTAL_LENGTH)
		{
			Util::triggerError(sprintf("The characters in all title, description, field->name, field->value, footer->text, and author->name fields must not exceed %d characters in total", self::EMBED_MAX_TOTAL_LENGTH));
		}

		return $this->embed;
	}

	private function updateTotalLength (int $length) : void
	{
		$this->totalLength += $length;

		if($this->totalLength < 0) $this->totalLength = 0;
	}
}
