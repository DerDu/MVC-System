<?php
namespace MVCSystem\Library;
use \AIOSystem\Api\Event;

class Route {

	const ROUTE_PLACEHOLDER_CONTROLLER = '{isController}';
	const ROUTE_PLACEHOLDER_ACTION = '{isAction}';

	/** @var string $RoutePattern */
	private $RoutePattern = '/';
	/** @var string $RouteController */
	private $RouteController = '{isController}';
	/** @var string $RouteAction */
	private $RouteAction = '{isAction}';
	/** @var array $RouteParameter */
	private $RouteParameter = array();
	/** @var string $RouteMatch */
	private $RouteDefinition = '/';

	public static function Instance( $Pattern, $Controller = '{isController}', $Action = '{isAction}', $Parameter = array() ) {
		return new Route( $Pattern, $Controller, $Action, $Parameter );
	}
	private function __construct( $Pattern, $Controller, $Action, $Parameter ) {
		$this->Pattern( $Pattern );
		$this->Controller( $Controller );
		$this->Action( $Action );
		$this->Parameter( $Parameter );
	}
	/**
	 * @param string $Route
	 * @return bool
	 */
	public function isMatch( $Route ) {
		if( preg_match( '!^'.$this->_rewritePattern().'$!is', $Route ) ) {
			$this->RouteDefinition = $Route;
			$this->_rewriteController();
			$this->_rewriteAction();
			$this->_rewriteParameter();
			return true;
		} return false;
	}
	/**
	 * @param null|string $Pattern
	 * @return string
	 */
	public function Pattern( $Pattern = null ) {
		if( $Pattern !== null ) {
			$this->RoutePattern = $Pattern;
		} return $this->RoutePattern;
	}
	/**
	 * Set/Get Controller
	 *
	 * Usage: ControllerName
	 * OR:    Name-Space-Before-ControllerName => Name\Space\Before\ControllerName
	 *
	 * ATTENTION:
	 * "Name\Space\Before\" controller name is additional to PathToController
	 *
	 * Example:
	 *
	 * $this->PathToApplication('Path\To\Application');
	 * $this->PathToController('Path\To\ControllerDir');
	 * $this->Controller('Name-Space-Before-ControllerClass');
	 *
	 * Result: Path\To\Application\Path\To\ControllerDir\Name\Space\Before\ControllerClass
	 *
	 * @param null|string $Controller
	 * @return string
	 */
	public function Controller( $Controller = null ) {
		if( $Controller !== null ) {
			$this->RouteController = str_replace( '-', '\\', $Controller );
		} return $this->RouteController;
	}
	public function Action( $Action = null ) {
		if( $Action !== null ) {
			$this->RouteAction = $Action;
		} return $this->RouteAction;
	}
	public function Parameter( $Parameter = null ) {
		if( $Parameter !== null ) {
			$this->RouteParameter = $Parameter;
		} return $this->RouteParameter;
	}

	private function _rewriteController() {
		$Pattern = explode( '/', $this->Pattern() );
		$Definition = explode( '/', $this->RouteDefinition );
		$Rewrite = array_diff_assoc( array_flip( $Pattern ), array_flip( $Definition ) );
		if( isset( $Rewrite[self::ROUTE_PLACEHOLDER_CONTROLLER] ) ) {
			$this->Controller( $Definition[$Rewrite[self::ROUTE_PLACEHOLDER_CONTROLLER]] );
		}
	}
	private function _rewriteAction() {
		$Pattern = explode( '/', $this->Pattern() );
		$Definition = explode( '/', $this->RouteDefinition );
		$Rewrite = array_diff_assoc( array_flip( $Pattern ), array_flip( $Definition ) );
		if( isset( $Rewrite[self::ROUTE_PLACEHOLDER_ACTION] ) ) {
			$this->Action( $Definition[$Rewrite[self::ROUTE_PLACEHOLDER_ACTION]] );
		}
	}
	private function _rewriteParameter() {
		$Pattern = explode( '/', $this->Pattern() );
		$Definition = explode( '/', $this->RouteDefinition );
		$Rewrite = array_diff_assoc( array_flip( $Pattern ), array_flip( $Definition ) );
		if( isset( $Rewrite[self::ROUTE_PLACEHOLDER_CONTROLLER] ) ) {
			unset( $Rewrite[self::ROUTE_PLACEHOLDER_CONTROLLER] );
		}
		if( isset( $Rewrite[self::ROUTE_PLACEHOLDER_ACTION] ) ) {
			unset( $Rewrite[self::ROUTE_PLACEHOLDER_ACTION] );
		}
		foreach( (array)$Rewrite as $Name => $Key ) {
			if( isset( $Definition[$Key] ) ) {
				$this->RouteParameter[str_replace( array('{','}','?'), '', $Name )] = $Definition[$Key];
			}
		}
	}
	private function _rewritePattern() {
		$Return = preg_replace(
			array( '!{[^\?]*?}/!is', '!{[^\?]*?}!is' ),
			array( '[^/]*?/', '[^/]*?' ),
			$this->Pattern()
		);
		$Return = preg_replace(
			array( '!{\?.*?}/!is', '!{\?.*?}!is' ),
			array( '?[^/]*?/', '?[^/]*?' ),
			$Return
		);
		$Return .= '(\?|$)';
		return $Return;
	}
}
