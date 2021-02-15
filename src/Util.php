<?php
class Util {
	const Colors = array(
		"DEFAULT" => "0x000000",
		"WHITE" => "0xffffff",
		"AQUA" => "0x1abc9c",
		"GREEN" => "0x2ecc71",
		"BLUE" => "0x3498db",
		"YELLOW" => "0xffff00",
		"PURPLE" => "0x9b59b6",
		"LUMINOUS_VIVID_PINK" => "0xe91e63",
		"GOLD" => "0xf1c40f",
		"ORANGE" => "0xe67e22",
		"RED" => "0xe74c3c",
		"GREY" => "0x95a5a6",
		"NAVY" => "0x34495e",
		"DARK_AQUA" => "0x11806a",
		"DARK_GREEN" => "0x1f8b4c",
		"DARK_BLUE" => "0x206694",
		"DARK_PURPLE" => "0x71368a",
		"DARK_VIVID_PINK" => "0xad1457",
		"DARK_GOLD" => "0xc27c0e",
		"DARK_ORANGE" => "0xa84300",
		"DARK_RED" => "0x992d22",
		"DARK_GREY" => "0x979c9f",
		"DARKER_GREY" => "0x7f8c8d",
		"LIGHT_GREY" => "0xbcc0c0",
		"DARK_NAVY" => "0x2c3e50",
		"BLURPLE" => "0x7289da",
		"GREYPLE" => "0x99aab5",
		"DARK_BUT_NOT_BLACK" => "0x2c2f33",
		"NOT_QUITE_BLACK" => "0x23272a"
	);

	public static function resolveString (string|array $data) : string
	{
		if(gettype($data) === "string") return $data;
		return implode("\n", $data);
	}

	public static function triggerError (string $error, string $type = "EXCEPTION") : void
	{
		if($type === "EXCEPTION")
		{
			throw new Exception($error);
		}
		else if($type === "OUT_OF_BOUNDS")
		{
			throw new OutOfBoundsException($error);
		}
	}
}
