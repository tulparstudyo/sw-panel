<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Frigian
 */

namespace Aimeos\MShop\Frigian\Manager;


/**
 * Delivery and payment frigian manager.
 *
 * @package MShop
 * @subpackage Frigian
 */
class Standard
	extends \Aimeos\MShop\Frigian\Manager\Base
	implements \Aimeos\MShop\Frigian\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array();
	private $date; 

	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->setResourceName( 'db-frigian' );
		$this->date = $context->getDateTime();
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'frigian/manager/sitemode', $level );

	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Frigian\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( array $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		die('clear');
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Frigian\Item\Iface New frigian item object
	 */
	public function createItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		die('getResourceType');
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		die('clear');
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Frigian\Manager\Iface Manager object for chaining method calls
	 */
	public function deleteItems( array $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		die('deleteItems');
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( string $code, array $ref = [], string $domain = null, string $type = null,
		bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		die('findItem');
	}


	/**
	 * Returns the frigian item specified by the given ID.
	 *
	 * @param string $id Unique ID of the frigian item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Frigian\Item\Iface Returns the frigian item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		die('getItem');
	}


	/**
	 * Adds a new or updates an existing frigian item in the storage.
	 *
	 * @param \Aimeos\MShop\Frigian\Item\Iface $item New or existing frigian item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Frigian\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Frigian\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Frigian\Item\Iface
	{
		$data = $item->toArray();

		foreach($data as $key=>$value ){
			$is_array = 0;
			if(is_array($value)){
				$value = json_encode($value, JSON_UNESCAPED_UNICODE);
				$is_array = 1;
			} 
			//echo "$key=>$value<br>";
			
			\DB::table('sw_options')->where([
                    ['option_group', 'theme'],
                    ['option_key', $key],
                ])->delete();
			\DB::table('sw_options')->insertOrIgnore([
				[
					'option_group' => 'theme', 
					'option_key' => $key, 
					'value' => $value,  
					'is_json' => $is_array,  
				]
			]);
			
		}
		return $item;
	}


	/**
	 * Searches for frigian items based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Frigian\Item\Iface with ids as keys
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$rows = \DB::table('sw_options')
                    ->where('option_group', 'theme')
                    ->get();
		if($rows){
            $items = [];
			foreach($rows as $row){
				if($row->is_json){
					$items[$row->option_key] = json_decode($row->value, 1) ;
				} else {
					$items[$row->option_key] = $row->value;
				}
				
			}
			$map[] = $items;
		}
		
		return $this->buildItems( $map, $ref, 'theme' );
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Sub manager
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		die('getSubManager');
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool $default Add default criteria (optional)
	 * @return \Aimeos\MW\Criteria\Iface New search criteria object
	 */
	public function createSearch( bool $default = false ) : \Aimeos\MW\Criteria\Iface
	{
		return parent::createSearch();
	}

	/**
	 * Creates a new frigian item initialized with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listitems List of list items
	 * @param \Aimeos\MShop\Text\Item\Iface[] $refItems List of referenced items
	 * @return \Aimeos\MShop\Frigian\Item\Iface New frigian item
	 */
	protected function createItemBase( array $values = [], array $listitems = [], array $refItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return new \Aimeos\MShop\Frigian\Item\Standard( $values, $listitems, $refItems );

	}
}
