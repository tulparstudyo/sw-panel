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
 * Abstract class for slider managers.
 *
 * @package MShop
 * @subpackage Frigian
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	use \Aimeos\MShop\Common\Manager\ListRef\Traits;

	/**
	 * Returns the slider provider which is responsible for the slider item.
	 *
	 * @param \Aimeos\MShop\Frigian\Item\Iface $item Delivery or payment slider item object
	 * @param string $type Frigian type code
	 * @return \Aimeos\MShop\Frigian\Provider\Iface Frigian provider object
	 * @throws \Aimeos\MShop\Frigian\Exception If provider couldn't be found
	 */

	public function getProvider( \Aimeos\MShop\Frigian\Item\Iface $item, string $type ) : \Aimeos\MShop\Frigian\Provider\Iface
	{
		
		$type = ucwords( $type );
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \Aimeos\MShop\Frigian\Exception( sprintf( 'Invalid characters in type name "%1$s"', $type ) );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \Aimeos\MShop\Frigian\Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \Aimeos\MShop\Frigian\Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$classname = '\Aimeos\MShop\Frigian\Provider\\' . $type . '\\' . $provider;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Frigian\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->getContext();
		$config = $context->getConfig();
		$provider = new $classname( $context, $item );

		self::checkClass( \Aimeos\MShop\Frigian\Provider\Factory\Iface::class, $provider );

		$decorators = $config->get( 'mshop/slider/provider/' . $item->getType() . '/decorators', [] );

		$provider = $this->addFrigianDecorators( $item, $provider, $names );
		return $this->addFrigianDecorators( $item, $provider, $decorators );
	}


	/**
	 * Wraps the named slider decorators around the slider provider.
	 *
	 * @param \Aimeos\MShop\Frigian\Item\Iface $sliderItem Frigian item object
	 * @param \Aimeos\MShop\Frigian\Provider\Iface $provider Frigian provider object
	 * @param array $names List of decorator names that should be wrapped around the provider object
	 * @return \Aimeos\MShop\Frigian\Provider\Iface
	 */
	protected function addFrigianDecorators( \Aimeos\MShop\Frigian\Item\Iface $sliderItem,
		\Aimeos\MShop\Frigian\Provider\Iface $provider, array $names ) : \Aimeos\MShop\Frigian\Provider\Iface
	{
		$classprefix = '\Aimeos\MShop\Frigian\Provider\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Frigian\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Frigian\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $provider, $this->getContext(), $sliderItem );

			self::checkClass( \Aimeos\MShop\Frigian\Provider\Decorator\Iface::class, $provider );
		}

		return $provider;
	}
}
