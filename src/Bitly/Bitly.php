<?php namespace Jiko\Shorten\Bitly;

class Bitly
{

  public function __construct()
  {
    $this->links = cache()->rememberForever('bitly.links', function () {
      return BitlyLink::all();
    });
  }

  public function shorten($url, $endpoint = 'https://api-ssl.bitly.com/v3/shorten')
  {
    if ($link = $this->links->filter(function ($value, $key) use ($url) {
      return $value->long_url == $url;
    })->first()
    ) {
      return $link;
    }

    $link = new BitlyLink(['long_url' => $url]);

    // @todo move config to package. ex. bitly::token
    $query_string = http_build_query([
      'access_token' => getenv('BITLY_TOKEN'),
      'longUrl' => $url
    ]);

    $short_link = getJson("{$endpoint}?{$query_string}");
    if (property_exists($short_link, "error")) {
      return "";
    }


    $link->url = $short_link->data->url;
    $link->hash = $short_link->data->hash;
    $link->global_hash = $short_link->data->global_hash;
    $link->save();

    return $link;
  }

  public function url($url)
  {
    $link = $this->shorten($url);

    return $link->url;
  }
}