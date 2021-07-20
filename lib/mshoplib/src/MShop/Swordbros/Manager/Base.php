<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Swordbros
 */

namespace Aimeos\MShop\Swordbros\Manager;

/**
 * Abstract class for slider managers.
 *
 * @package MShop
 * @subpackage Swordbros
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	use \Aimeos\MShop\Common\Manager\ListRef\Traits;

	/**
	 * Returns the slider provider which is responsible for the slider item.
	 *
	 * @param \Aimeos\MShop\Swordbros\Item\Iface $item Delivery or payment slider item object
	 * @param string $type Swordbros type code
	 * @return \Aimeos\MShop\Swordbros\Provider\Iface Swordbros provider object
	 * @throws \Aimeos\MShop\Swordbros\Exception If provider couldn't be found
	 */

	public function getProvider( \Aimeos\MShop\Swordbros\Item\Iface $item, string $type ) : \Aimeos\MShop\Swordbros\Provider\Iface
	{
		
		$type = ucwords( $type );
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \Aimeos\MShop\Swordbros\Exception( sprintf( 'Invalid characters in type name "%1$s"', $type ) );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \Aimeos\MShop\Swordbros\Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \Aimeos\MShop\Swordbros\Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$classname = '\Aimeos\MShop\Swordbros\Provider\\' . $type . '\\' . $provider;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Swordbros\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->getContext();
		$config = $context->getConfig();
		$provider = new $classname( $context, $item );

		self::checkClass( \Aimeos\MShop\Swordbros\Provider\Factory\Iface::class, $provider );

		$decorators = $config->get( 'mshop/slider/provider/' . $item->getType() . '/decorators', [] );

		$provider = $this->addSwordbrosDecorators( $item, $provider, $names );
		return $this->addSwordbrosDecorators( $item, $provider, $decorators );
	}


	/**
	 * Wraps the named slider decorators around the slider provider.
	 *
	 * @param \Aimeos\MShop\Swordbros\Item\Iface $sliderItem Swordbros item object
	 * @param \Aimeos\MShop\Swordbros\Provider\Iface $provider Swordbros provider object
	 * @param array $names List of decorator names that should be wrapped around the provider object
	 * @return \Aimeos\MShop\Swordbros\Provider\Iface
	 */
	protected function addSwordbrosDecorators( \Aimeos\MShop\Swordbros\Item\Iface $sliderItem,
		\Aimeos\MShop\Swordbros\Provider\Iface $provider, array $names ) : \Aimeos\MShop\Swordbros\Provider\Iface
	{
		$classprefix = '\Aimeos\MShop\Swordbros\Provider\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Swordbros\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Swordbros\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $provider, $this->getContext(), $sliderItem );

			self::checkClass( \Aimeos\MShop\Swordbros\Provider\Decorator\Iface::class, $provider );
		}

		return $provider;
	}
}
