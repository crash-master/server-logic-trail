<?php
namespace Middleware\Kernelevents;

class Cache{
	/**
	 * [call when used exists cache data]
	 *
	 * @method cache_data_used
	 *
	 * @param  [string] $cache_alias [alias of cache file]
	 * @param  [array] $cache_data [data of cache]
	 *
	 */
	public function cache_data_used($cache_alias, $cache_data){

	}

	/**
	 * [call when created new cache data]
	 *
	 * @method cache_data_create
	 *
	 * @param  [string] $cache_alias [alias of cache file]
	 * @param  [array] $cache_data [data of cache]
	 *
	 */
	public function cache_data_create($cache_alias, $cache_data){

	}
}