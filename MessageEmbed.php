class MessageEmbed {

	/* Embed object */
	private object $embed;

	/* Errors array */
	private array $errors;

	/* Constants */
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
		$this->embed->video = (object) array("url"=>null);
		$this->embed->author = (object) array("name"=>null, "url"=>null, "icon_url");
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
			$this->setError(sprintf("Title must be fewer than or equal to %d characters.", self::TITLE_MAX_LENGTH));
		}
		else
		{
			$this->embed->title = $title;
		}

		return $this;
	}

	public function setURL (string $url) : object
	{
		$this->embed->url = $url;

		return $this;
	}

	public function setAuthor (string $name, string $iconURL = null, string $url = null) : object
	{
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
			$this->setError(sprintf("Description must be fewer than or equal to %d characters.", self::DESCRIPTION_MAX_LENGTH));
		}
		else
		{
			$this->embed->description = $description;
		}

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
			$this->setError(sprintf("Embed cannot exceed %d fields.", self::FIELDS_MAX_SIZE));
		}
		else
		{
			$field = new stdClass();
			$field->name = $name;
			$field->value = $value;
			$field->inline = $inline;

			array_push($this->embed->fields, $field);
		}
		return $this;
	}

	public function addFields (array $fields) : object
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
				$this->setError(sprintf("Incorrectly formatted field: %s", json_encode($field)));
			}
		}

		return $this;
	}

	public function setImage (string $imageURL) : object
	{
		$this->embed->image->url = $imageURL;

		return $this;
	}

	public function setVideo (string $videoURL) : object
	{
		$this->embed->video->url = $videoURL;

		return $this;
	}

	public function setTimestamp () : object
	{
		$this->embed->timestamp = date("c");

		return $this;
	}

	public function setFooter ($text, $iconURL = null) : object
	{
		if(strlen($text) > self::FOOTER_TEXT_MAX_LENGTH)
		{
			$this->setError(sprintf("Footer text must be fewer than or equal to %d characters.", self::FOOTER_TEXT_MAX_LENGTH));
		}
		else
		{
			$this->embed->footer->text = $text;
		}

		$this->embed->footer->icon_url = $iconURL;

		return $this;
	} 

	public function getEmbed () : object
	{
		return $this->embed;
	}

	private function setError (string $error) : void
	{
		array_push($this->errors, $error);
	}

	public function getErrors () : array
	{
		return $this->errors;
	}

	public function hasErrors () : bool
	{
		return count($this->errors) > 0;
	}
}
