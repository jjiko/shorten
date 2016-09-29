<?php namespace Jiko\Shorten\Bitly;

use Illuminate\Database\Eloquent\Model;

class BitlyLink extends Model
{
  protected $connection = "mysql";
  protected $fillable = ['long_url', 'url', 'hash', 'global_hash'];
}