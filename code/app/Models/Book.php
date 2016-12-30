<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Auth;

class Book extends Model
{
	use Searchable;

	protected $guarded = ["id"];

	protected $hidden = [];

	protected $dates = ["published_date"];

	public $searchable = [
		"google_id", "amazon_id", "title", "authors",
		"publisher", "description", "isbn_13", "isbn_10",
	];
	
	public function userBooks()
	{
		return $this->hasMany("App\Models\UserBook");
	}
	
	public function getISBN13()
	{
		if (strlen($this->isbn_13) == 14)
			return $this->isbn_13;
		if (strlen($this->isbn_13) != 13)
			return null;
		return substr($this->isbn_13, 0, 3) . "-" . substr($this->isbn_13, 3, 10); 
	}
	
	public function getLastRead()
	{
		$userBook = $this->userBooks()
			->where("user_id", Auth::user()->id)
			->orderBy("date", "DESC")
			->first();
		if (!$userBook)
			return null;
		return $userBook->date->format("m/d/Y");
	}
	
	public static function getByISBN($isbn)
	{
		$isbn = preg_replace("/[^0-9X]/", "", $isbn);
		if (strlen($isbn) != 10 && strlen($isbn) != 13)
			return null;
		$column = strlen($isbn) == 10 ? "isbn_10" : "isbn_13";
		$book = Book::where($column, $isbn)->first();
		if ($book)
			return $book;
		$book = Book::lookupGoogle($isbn);
		if (!$book)
			$book = Book::lookupAmazon($isbn);
		return $book;
	}
	
	private static function lookupGoogle($isbn)
	{
		$isbn = preg_replace("/[^0-9X]/", "", $isbn);
		$client = new \Google_Client();
		$client->setApplicationName("Book Database");
		$client->setDeveloperKey(env("GOOGLE_DEV_KEY", ""));
		$service = new \Google_Service_Books($client);
		$results = $service->volumes->listVolumes("isbn:{$isbn}");
		if (count($results) == 0 || !isset($results[0]["volumeInfo"]))
			return null;
		$item = $results[0];
		$isbn10 = "";
		$isbn13 = "";
		if (isset($item["volumeInfo"]["industryIdentifiers"]) && is_array($item["volumeInfo"]["industryIdentifiers"]))
		{
			foreach ($item["volumeInfo"]["industryIdentifiers"] as $industryIdentifier)
			{
				if ($industryIdentifier["type"] == "ISBN_13")
					$isbn13 = $industryIdentifier["identifier"];
				if ($industryIdentifier["type"] == "ISBN_10")
					$isbn10 = $industryIdentifier["identifier"];
			}
		}

		$book = new Book;
		$book->google_id = $item["id"];
		$book->amazon_id = null;
		$book->title = $item["volumeInfo"]["title"];
		$book->authors = isset($item["volumeInfo"]["authors"]) ? implode(";", $item["volumeInfo"]["authors"]) : "";
		$book->publisher = isset($item["volumeInfo"]["publisher"]) ? $item["volumeInfo"]["publisher"] : "";
		$book->published_date = strtotime($item["volumeInfo"]["publishedDate"]) ? new \Carbon\Carbon($item["volumeInfo"]["publishedDate"]) : null;
		$book->description = $item["volumeInfo"]["description"];
		if (empty($isbn10) || empty($isbn13))
		{
			$lookupBook = Book::where("title", $book->title)->where("authors", $book->authors)->first();
			if ($lookupBook)
			{
				if (empty($lookupBook->isbn_13) && strlen($isbn) == 13)
					$lookupBook->isbn_13 = $isbn;
				if (empty($lookupBook->isbn_10) && strlen($isbn) == 10)
					$lookupBook->isbn_10 = $isbn;
				$lookupBook->save();
				return $lookupBook;
			}
		}
		if (empty($isbn10) && strlen($isbn) == 10)
			$isbn10 = $isbn;
		if (empty($isbn13) && strlen($isbn) == 13)
			$isbn13 = $isbn;
		$book->isbn_13 = $isbn13;
		$book->isbn_10 = $isbn10;
		$book->pages = is_numeric($item["volumeInfo"]["pageCount"]) ? $item["volumeInfo"]["pageCount"] : null;;
		$book->image_link = isset($item["volumeInfo"]["imageLinks"]) && isset($item["volumeInfo"]["imageLinks"]["thumbnail"]) ? $item["volumeInfo"]["imageLinks"]["thumbnail"] : "";
		$book->save();
		return $book;
	}
	
	private static function lookupAmazon($isbn)
	{
		$isbn = preg_replace("/[^0-9X]/", "", $isbn);
		$parameters = [
			"AWSAccessKeyId" => env("AMAZON_AWS_ACCESS_KEY", ""),
			"AssociateTag" => env("AMAZON_ASSOCIATE_TAG", ""),
			"IdType" => "ISBN",
			"ItemId" => $isbn,
			"Operation" => "ItemLookup",
			"ResponseGroup" => "Large",
			"Service" => "AWSECommerceService",
			"SearchIndex" => "All",
		];
		$query = http_build_query($parameters);
		$url = "http://webservices.amazon.com/onca/xml?{$query}";
		$url = Book::amazonSign($url, env("AMAZON_SECRET_ACCESS_KEY", ""));
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$xml = new \SimpleXMLElement($output);
		if (!isset($xml->Items) && !isset($xml->Items->Item))
			return null;
		$item = null;
		foreach($xml->Items->Item as $innerItem)
		{
			if (isset($innerItem->ItemAttributes->EAN) && isset($innerItem->ItemAttributes->ISBN))
				$item = $innerItem;
		}
		if (!$item)
			return null;

		$imageLink = "";
		if (isset($item->LargeImage) && isset($item->LargeImage->URL))
			$imageLink = $item->LargeImage->URL->__toString();
		if (empty($imageLink) && isset($item->ImageSets) && isset($item->ImageSets->ImageSet) && isset($item->ImageSets->ImageSet->LargeImage) && isset($item->ImageSets->ImageSet->LargeImage->URL))
			$imageLink = $item->ImageSets->ImageSet->LargeImage->URL->__toString();

		$book = new Book;
		$book->google_id = null;
		$book->amazon_id = $item->ASIN->__toString();
		$book->title = $item->ItemAttributes->Title->__toString();
		$book->authors = $item->ItemAttributes->Author->__toString();
		$book->publisher = $item->ItemAttributes->Publisher->__toString();
		$book->published_date = strtotime($item->ItemAttributes->PublicationDate->__toString()) ? new \Carbon\Carbon($item->ItemAttributes->PublicationDate->__toString()) : null;
		$book->description = "";
		$book->isbn_13 = $item->ItemAttributes->EAN->__toString();
		$book->isbn_10 = $item->ItemAttributes->ISBN->__toString();
		$book->pages = is_numeric($item->ItemAttributes->NumberOfPages->__toString()) ? $item->ItemAttributes->NumberOfPages->__toString() : null;
		$book->image_link = $imageLink;
		$book->save();
		return $book;
	}
	
	public static function amazonEncode($text)
	{
		$encodedText = "";
		$j = strlen($text);
		for($i=0; $i < $j; $i++)
		{
			$c = substr($text, $i, 1);
			if (!preg_match("/[A-Za-z0-9\-_.~]/", $c))
			{
				$encodedText .= sprintf("%%%02X", ord($c));
			}
			else
			{
				$encodedText .= $c;
			}
		}
		return $encodedText;
	}
	
	public static function amazonSign($url, $secretAccessKey)
	{
		$url .= "&Timestamp=" . gmdate("Y-m-d\TH:i:s\Z");
		$urlParts = parse_url($url);
		parse_str($urlParts["query"], $queryVars);
		ksort($queryVars);
		$encodedVars = [];
		foreach($queryVars as $key => $value)
		{
			$encodedVars[Book::amazonEncode($key)] = Book::amazonEncode($value);
		}
		$encodedQueryVars = [];
		foreach($encodedVars as $key => $value)
		{
			$encodedQueryVars[] = $key . "=" . $value;
		}
		$encodedQuery = implode("&", $encodedQueryVars);
		$stringToSign = "GET";
		$stringToSign .= "\n" . strtolower($urlParts["host"]);
		$stringToSign .= "\n" . $urlParts["path"];
		$stringToSign .= "\n" . $encodedQuery;
		if (function_exists("hash_hmac"))
		{
			$hmac = hash_hmac("sha256", $stringToSign, $secretAccessKey, true);
		}
		elseif(function_exists("mhash"))
		{
			$hmac = mhash(MHASH_SHA256, $stringToSign, $secretAccessKey);
		}
		else
		{
			die("No hash function available!");
		}
		$hmacBase64 = base64_encode($hmac);
		$url .= "&Signature=" . Book::amazonEncode($hmacBase64);
		return $url;
	}
}
