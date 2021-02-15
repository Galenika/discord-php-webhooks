<?php
class Webhook {

	/* Message object */
	private object $webhook;

	/* Webhook URL */
	private string $webhookURL;

	/* Allowed mention types */
	private array $allowedMentionTypes;
	private bool $allowedMentionsIsUsed;

	const CONTENT_MAX_LENGTH = 2000;
	const EMBEDS_MAX_SIZE = 10;
	const ALLOWED_MENTIONS_ROLES_SIZE = 100;
	const ALLOWED_MENTIONS_USERS_SIZE = 100;

	function __construct(string $webhookURL)
	{
		/* Initialize webhook URL */
		$this->webhookURL = $webhookURL;

		/* Initialize allowed mention types */
		$this->allowedMentionTypes = ["roles", "users", "everyone"];
		$this->allowedMentionsIsUsed = false;

		/* Set default values for required params */
		$this->webhook = new stdClass();
		$this->webhook->content = null;
		$this->webhook->username = null;
		$this->webhook->avatar_url = null;
		$this->webhook->tts = null;
		$this->webhook->file = null;
		$this->webhook->embeds = [];
		$this->webhook->allowed_mentions = new stdClass();
		$this->webhook->allowed_mentions->parse = array();
		$this->webhook->allowed_mentions->roles = array();
		$this->webhook->allowed_mentions->users = array();
	}

	public function setContent (string|array $content) : object
	{
		$content = Util::resolveString($content);

		if(strlen($content) > self::CONTENT_MAX_LENGTH)
		{
			Util::triggerError(sprintf("Message content must be fewer than or equal to %d characters", self::CONTENT_MAX_LENGTH));
		}

		$this->webhook->content = $content;

		return $this;
	}

	public function setUsername (string $username = null) : object
	{
		$this->webhook->username = $username;

		return $this;
	}

	public function setAvatar (string $avatarURL = null) : object
	{
		$this->webhook->avatar_url = $avatarURL;

		return $this;
	}

	public function setTTS (bool $status) : object
	{
		$this->webhook->tts = $status;

		return $this;
	}

	public function addEmbed (object $embed) : object
	{
		if(count($this->webhook->embeds) >= self::EMBEDS_MAX_SIZE)
		{
			Util::triggerError(sprintf("Message cannot have more than %d embeds", self::EMBEDS_MAX_SIZE));
		}

		array_push($this->webhook->embeds, $embed);

		return $this;
	}

	public function addMentionsParse(string ...$parse) : object
	{
		$this->allowedMentionsIsUsed = true;
		foreach($parse as $value)
		{
			$value = strtolower($value);
			if(!array_key_exists($value, $this->webhook->allowed_mentions->parse) && in_array($value, $this->allowedMentionTypes))
			{
				if($value !== "everyone" && count($this->webhook->allowed_mentions->$value) > 0)
				{
					Util::triggerError("parse:[\"{$value}\"] and {$value}: [ids...] are mutually exclusive");
				}

				array_push($this->webhook->allowed_mentions->parse, $value);
			}
		}

		return $this;
	}

	public function addMentionsRoles(string ...$roles) : object
	{
		$this->allowedMentionsIsUsed = true;
		foreach($roles as $role)
		{
			if(!array_key_exists($role, $this->webhook->allowed_mentions->roles))
			{
				if(in_array("roles", $this->webhook->allowed_mentions->parse))
				{
					Util::triggerError("parse:[\"roles\"] and roles: [ids...] are mutually exclusive");
				}

				array_push($this->webhook->allowed_mentions->roles, $role);
			}
		}

		return $this;
	}

	public function addMentionsUsers(string ...$users) : object
	{
		$this->allowedMentionsIsUsed = true;
		foreach($users as $user)
		{
			if(!array_key_exists($user, $this->webhook->allowed_mentions->users))
			{
				if(in_array("users", $this->webhook->allowed_mentions->parse))
				{
					Util::triggerError("parse:[\"users\"] and users: [ids...] are mutually exclusive");
				}

				array_push($this->webhook->allowed_mentions->users, $user);
			}
		}

		return $this;
	}

	public function get () : object
	{
		$webhook = $this->webhook;

		if(!$this->allowedMentionsIsUsed) unset($webhook->allowed_mentions); // If allowed mentions is not used, default to normal behavior

		return $webhook;
	}

	public function updateWebhookUrl (string $webhookURL) : object
	{
		$this->webhookURL = $webhookURL;

		return $this;
	}

	public function send () : string
	{
		$data = (array) $this->webhook;

		if(!$this->allowedMentionsIsUsed) unset($data['allowed_mentions']); // If allowed mentions is not used, default to normal behavior

		$ch = curl_init();
		curl_setopt_array($ch, [
		    CURLOPT_URL => $this->webhookURL,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_POST => true,
		    CURLOPT_POSTFIELDS => json_encode($data),
		    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
		    CURLOPT_CONNECTTIMEOUT => 10,
		    CURLOPT_TIMEOUT => 10
		]);

		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}
}