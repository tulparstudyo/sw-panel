<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Slider
 */


namespace Aimeos\MShop\Swordbros\Manager;


/**
 * Factory for a slider manager.
 *
 * @package MShop
 * @subpackage Swordbros
 */
class Factory
	extends \Aimeos\MShop\Common\Factory\Base
	implements \Aimeos\MShop\Common\Factory\Iface
{
	/**
	 * Creates a slider manager DAO object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param string|null $name Manager name
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object implementing the manager interface
	 * @throws \Aimeos\MShop\Swordbros\Exception|\Aimeos\MShop\Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'swordbros/manager/name', 'Standard' );
		}

		$iface = \Aimeos\MShop\Swordbros\Manager\Iface::class;
		$classname = '\Aimeos\MShop\Swordbros\Manager\\' . $name;

		if( ctype_alnum( $name ) === false ) {
			throw new \Aimeos\MShop\Slider\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$manager = self::createManager( $context, $classname, $iface );
		return self::addManagerDecorators( $context, $manager, 'swordbros' );
	}

}
