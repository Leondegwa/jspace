<?php
/**
 * A JSpace factory class.
 * 
 * @package		JSpace
 * @copyright	Copyright (C) 2012 Wijiti Pty Ltd. All rights reserved.
 * @license     This file is part of the JSpace library for Joomla!.

   The JSpace library for Joomla! is free software: you can redistribute it 
   and/or modify it under the terms of the GNU General Public License as 
   published by the Free Software Foundation, either version 3 of the License, 
   or (at your option) any later version.

   The JSpace library for Joomla! is distributed in the hope that it will be 
   useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with the JSpace library for Joomla!.  If not, see 
   <http://www.gnu.org/licenses/>.

 * Contributors
 * Please feel free to add your name and email (optional) here if you have 
 * contributed any source code changes.
 * Name							Email
 * Hayden Young					<haydenyoung@wijiti.com> 
 * 
 */
defined('JPATH_PLATFORM') or die;

jimport('jspace.repository.cache');
jimport('jspace.repository.connector');
jimport('jspace.repository.endpoint');
jimport('jspace.crosswalk.mapper');
jimport('jspace.crosswalk.crosswalk');
jimport('jspace.messenger.messenger');
jimport('jspace.repository.repository');
jimport('jspace.debug.debug');

JLoader::discover("JSpaceTable", JPATH_SITE . "/libraries/jspace/database/table/");
JLoader::discover("JSpaceTool", dirname(__FILE__) . "/tool/");

class JSpaceFactory
{
	const JSPACE_NAME = 'com_jspace'; 
	
	/**
	 * Instantiates an instance of the JSpaceRepositoryConnector class, loading the 
	 * correct repository driver.
	 * 
	 * @params array $options An optional array of connection options. If empty, 
	 * the default com_jspace connection information will be used.
	 * 
	 * @return JSpaceRepositoryConnector An instance of the JSpaceRepositoryConnector class.
	 */
	public static function getConnector($options = null)
	{
		$config = self::getConfig();
		
		if (is_null($options)) {
			$options = array();
			$options['driver'] = $config->get('driver');
			$options['url'] 		= $config->get( $options['driver'] . '_rest_url');
			$options['username'] 	= $config->get( $options['driver'] . '_username');
			$options['password'] 	= $config->get( $options['driver'] . '_password');
		}
		
		return JSpaceRepositoryConnector::getInstance($options);
	}
	
	public static function getConfig()
	{
		$config = new JRegistry();
		
		$component = JComponentHelper::getComponent(self::JSPACE_NAME);
		
		if ($component->enabled) {
			$config = $component->params;
		}
		
		return $config;
	}
	
	/**
	 * Get the repository configured in app config or pass config by param.
	 */
	public static function getRepository( $options = null) {
		$config = self::getConfig();
		if (is_null($options)) {
			$options = array();
			$options['driver'] = $config->get('driver');
			
			/* ToDo: later these options should be moved to specyfic repository constructor. */
			$options['url'] 		= $config->get( $options['driver'] . '_rest_url');
			$options['username'] 	= $config->get( $options['driver'] . '_username');
			$options['password'] 	= $config->get( $options['driver'] . '_password');
			$options['base_url'] 	= $config->get( $options['driver'] . '_base_url');
			
			$options['mapper'] = self::getMapper( $config->get($options['driver'] . '_crosswalk', JSpaceMapper::MAPPER_DUBLINCORE) ); //ToDo: move to config
			$options['cache'] = array(
				'enabled' 	=> true,
// 				'options'	=> array(
// 					'driver'	=> 'simple',
// 					'storageDirectory'	=> JPATH_BASE . "/tmp/cache/",
// 				),
			);
		}
		
		return JSpaceRepository::getInstance( $options );
	}
	
	/**
	 * 
	 * @param array $options
	 * @return JSpaceCache
	 */
	public static function getCache( $options = null ) {
		if( is_null($options) ) {
			$options = array(
// 				'driver'	=> 'simple', 
				'driver'	=> 'jcache',
				'storageDirectory'	=> JPATH_BASE . "/tmp/cache/",
			);
		}
		return JSpaceRepositoryCache::getInstance( $options );
	}

	/**
	 * Instantiates an instance of the JSpaceEndpoint class.
	 *
	 * @param string $endpoint The relative url of the REST API endpoint.
	 * @param array $vars An array of extra querystring variables.
	 * @param boolean $anonymous True if the REST endpoint does not require authentication, false
	 * @param JObject $data An instance of the JObject class which contains values to be submitted 
	 * to the repository. 
	 * otherwise.
	 *
	 * @return JSpaceEndpoint An instance of the JSpaceEndpoint class.
	 */
	public static function getEndpoint($url, $vars = null, $anonymous = true, $data = null) {
		return new JSpaceRepositoryEndpoint($url, $vars, $anonymous, $data);
	}
	
	/**
	 * Instantiates JSpaceMapper
	 * @param string $type
	 * @return JSpaceMapper
	 */
	public static function getMapper( $type ) {
		return new JSpaceMapper( $type );
	}
	
	/**
	 * Instanciates a JSpaceCrosswalk class.
	 * Imports it if not already done.
	 * @param string $type
	 * @return JSpaceCrosswalk An instance of JSpaceCrosswalk class (singleton for each type).
	 */
	public static function getCrosswalk( $type ) {
		return JSpaceCrosswalk::factory( $type );
	}
	
	/**
	 * Get messenger object.
	 * @author Michał Kocztorz
	 * @return JSpaceMessenger
	 */
	public static function getMessenger() {
		return new JSpaceMessenger();
	}
	
	/**
	 * 
	 * @param JSpaceRepositoryEndpoint $endpoint
	 * @param string $baseUrl
	 * @return JSpaceRepositoryCacheKey
	 */
	public static function getCacheKey( JSpaceRepositoryEndpoint $endpoint, $baseUrl ) {
		return new JSpaceRepositoryCacheKey($endpoint, $baseUrl);
	}
	
	/**
	 * 
	 * @param JSpaceRepositoryItem $item
	 * @return JSpaceToolMetadataSet
	 */
	public static function getMetadataSet( JSpaceRepositoryItem $item ) {
		return new JSpaceToolMetadataSet( $item );
	}

}