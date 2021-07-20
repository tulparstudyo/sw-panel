<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Swordbros\Theme;

sprintf( 'sw-panel' ); // for translation

/**
 * Default implementation of frigian JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
    extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
    implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
    public function addData( \Aimeos\MW\View\Iface $view ) : \Aimeos\MW\View\Iface
    {

        return $view;
    }

    public function copy() : ?string
    {
        $view = $this->getObject()->addData( $this->getView() );
        return $this->render( $view );
    }

    public function create() : ?string
    {
        $view = $this->getObject()->addData( $this->getView() );
        return $this->render( $view );
    }

    public function delete() : ?string
    {
        return $this->search();
    }

    public function get() : ?string
    {
        $view = $this->getObject()->addData( $this->getView() );
        return $this->render( $view );
    }

    public function save() : ?string
    {
        $context = $this->getContext();
        $config = $context->getConfig();
        $manager = \Aimeos\MShop::create( $context, 'frigian' );
        $view = $this->getView();

    }

    public function searchData() : ?array
    {
        $manager = \Aimeos\MShop::create( $this->getContext(), 'swordbros/theme' );
        $search = $manager->createSearch();
        $items = $manager->searchItems( $search, [] )->first()->toArray();
        return $items;

    }
    public function search() : ?string
    {

        $view = $this->getView();
        $view->items = $this->searchData();
        $tplconf = 'admin/jqadm/frigian/template-list';
        $default = 'options/list-standard';

        return $view->render( $view->config( $tplconf, $default ) );
    }

    public function getSubClient( string $type, string $name = null ) : \Aimeos\Admin\JQAdm\Iface
    {

        return $this->createSubClient( 'frigian/' . $type, $name );
    }

    public function getConfigAttributes( \Aimeos\MShop\Frigian\Item\Iface $item ) : array
    {
        $manager = \Aimeos\MShop::create( $this->getContext(), 'frigian' );

        try {
            return $manager->getProvider( $item, $item->getType() )->getConfigBE();
        } catch( \Aimeos\MShop\Exception $e ) {
            return [];
        }
    }

    protected function getDomains() : array
    {
        return $this->getContext()->getConfig()->get( 'mshop/domains', [] );
    }

    protected function getSubClientNames() : array
    {
        return $this->getContext()->getConfig()->get( 'mshop/standard/subparts', [] );
    }

    protected function getTypeItems() : \Aimeos\Map
    {
        $typeManager = \Aimeos\MShop::create( $this->getContext(), 'frigian/type' );

        $search = $typeManager->createSearch( true )->setSlice( 0, 10000 );
        $search->setSortations( [$search->sort( '+', 'frigian.type.position' )] );

        return $typeManager->searchItems( $search );
    }

    protected function fromArray( array $data ) : \Aimeos\MShop\Frigian\Item\Iface
    {
        $conf = [];

        $manager = \Aimeos\MShop::create( $this->getContext(), 'frigian' );

        $item = $manager->createItem($data);

        $item->fromArray( $data, true );

        return $item;
    }

    protected function toArray( \Aimeos\MShop\Frigian\Item\Iface $item, bool $copy = false ) : array
    {
        $config = $item->getConfig();
        $data = $item->toArray( true );
        $data['config'] = [];

        if( $copy === true )
        {
            $data['frigian.siteid'] = $this->getContext()->getLocale()->getSiteId();
            $data['frigian.code'] = $data['frigian.code'] . '_copy';
            $data['frigian.id'] = '';
        }

        ksort( $config );

        foreach( $config as $key => $value )
        {
            $data['config']['key'][] = $key;
            $data['config']['val'][] = $value;
        }

        return $data;
    }

    protected function render( \Aimeos\MW\View\Iface $view ) : string
    {
        $tplconf = 'admin/jqadm/frigian/template-item';
        $default = 'ajax/item-'.$view->param('id');
        $view->context = $this->getContext();
        $view->aimeos = $this->getAimeos();

        return $view->render( $view->config( $tplconf, $default ) );
    }
}
