<?php
namespace MVCSystem\Library;
use \AIOSystem\Api\Xml;
use \AIOSystem\Api\Session;
use \AIOSystem\Api\Event;

use \MVCSystem\Api\Route;


class Router {

	const ROUTER_TABLE = 'Router';
	const ROUTER_TABLE_ROUTE = 'Route';
	const ROUTER_TABLE_PATTERN = 'Pattern';
	const ROUTER_TABLE_CONTROLLER = 'Controller';
	const ROUTER_TABLE_ACTION = 'Action';
	const ROUTER_TABLE_PARAMETER = 'Parameter';

	/** @var null|\AIOSystem\Core\ClassXmlNode $RoutingTableObject */
	private $RoutingTableObject = null;
	/** @var string $RoutingTable */
	private $RoutingTable = '';
	/** @var array $RoutingCache */
	private $RoutingCache = array();

	public static function Instance( $RouterTableFile ) {
		return new Router( $RouterTableFile );
	}
	private function __construct( $RouterTableFile ) {
		$this->RoutingTable( $RouterTableFile );
	}

	public function Search( $Route ) {
		$RoutingCache = $this->RoutingCache();
		/** @var Route $RouteObject */
		foreach( (array)$RoutingCache as $RouteObject ) {
			if( $RouteObject->isMatch( $Route ) ) {
				return $RouteObject;
			}
		}
		return false;
	}
	public function RoutingTable( $RouterTableFile = null ) {
		if( $RouterTableFile !== null ) {
			$this->RoutingTable = $RouterTableFile;
		} return $this->RoutingTable;
	}

	private function RoutingCache() {
		$this->_bootRoutingCache();
		return $this->RoutingCache;
	}
	private function _bootRoutingCache() {
		if( empty( $this->RoutingCache ) ) {
			if(
				false === (
					$this->RoutingCache = unserialize(
						Session::Decode( Session::Read(
							__METHOD__.':ARPC'.strtoupper( sha1_file( $this->RoutingTable() ) )
						) )
					) )
			) {
				$this->_bootRoutingTable();
				$RouteList = Xml::ListChild(
					Xml::Search(
						$this->RoutingTableObject,
						self::ROUTER_TABLE
					),
					self::ROUTER_TABLE_ROUTE
				);
				$RoutingCache = array();
				/** @var \AIOSystem\Core\ClassXmlNode $Route */
				foreach( (array)$RouteList as $Route ) {
					$ParameterList = Xml::ListChildAll( Xml::Search( $Route, self::ROUTER_TABLE_PARAMETER ) );
					$ArgumentList = array();
					foreach( (array)$ParameterList as $Parameter ) {
						$ArgumentList[Xml::Name( $Parameter )] = Xml::Content( $Parameter );
					}
					array_push( $RoutingCache,
						Route::Instance(
							Xml::Content( Xml::Search( $Route, self::ROUTER_TABLE_PATTERN ) ),
							Xml::Content( Xml::Search( $Route, self::ROUTER_TABLE_CONTROLLER ) ),
							Xml::Content( Xml::Search( $Route, self::ROUTER_TABLE_ACTION ) ),
							$ArgumentList
						)
					);
				}
				$this->RoutingCache = $RoutingCache;
				Session::Write(
					__METHOD__.':ARPC'.strtoupper( sha1_file( $this->RoutingTable() ) ),
					Session::Encode( serialize( $this->RoutingCache ) )
				);
			}
		}
	}
	private function _bootRoutingTable() {
		if( $this->RoutingTableObject === null ) {
			if(
				false === (
					$this->RoutingTableObject = unserialize(
						Session::Decode( Session::Read(
							__METHOD__.':ARP'.strtoupper( sha1_file( $this->RoutingTable() ) )
						) )
					) )
			) {
				$this->RoutingTableObject = Xml::Parser( $this->RoutingTable() );
				Session::Write(
					__METHOD__.':ARP'.strtoupper( sha1_file( $this->RoutingTable() ) ),
					Session::Encode( serialize( $this->RoutingTableObject ) )
				);
			}
		}
	}
}
