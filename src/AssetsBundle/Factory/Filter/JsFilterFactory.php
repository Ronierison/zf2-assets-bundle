<?php
namespace Neilime\AssetsBundle\Factory;
class JsFilterFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service\Filter\CssFilter
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		return new \Neilime\AssetsBundle\Service\Filter\JsFilter();
	}
}