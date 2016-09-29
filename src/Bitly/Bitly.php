<?php namespace Jiko\Shorten\Bitly;

class Bitly
{
  public static function shorten($url, $endpoint='https://api-ssl.bitly.com/v3/shorten')
  {
    $link = BitlyLink::firstOrCreate(['long_url' => $url]);
    if(empty($link->url)) {

      // @todo move config to package. ex. bitly::token
      $query_string = http_build_query([
        'access_token' => getenv('BITLY_TOKEN'),
        'longUrl' => $url
      ]);

      $short_link = getJson("{$endpoint}?{$query_string}");
      if(property_exists($short_link, "error")) {
        return "";
      }

      $link->update([
        'url' => $short_link->data->url,
        'hash' => $short_link->data->hash,
        'global_hash' => $short_link->data->global_hash
      ]);
    }

    return $link;
  }

  public static function url($url)
  {
    $link = static::shorten($url);

    return $link->url;
  }
}