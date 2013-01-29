<?php
namespace AssetsBundle\Controller;
class ToolsController extends \Zend\Mvc\Controller\AbstractActionController{
    public function renderassetsAction(){
        $oServiceLocator = $this->getServiceLocator();
        try{
            $oModuleManager = $oServiceLocator->get('modulemanager');
        }
        catch(\Zend\ServiceManager\Exception\ServiceNotFoundException $oException){
            return $this->sendError('Cannot get Zend\ModuleManager\ModuleManager instance. Is your application using it?');
        }
        $oConsole = $this->getServiceLocator()->get('console');

        //Initialize AssetsBundle service
        $oAssetsBundleService = $oServiceLocator->get('AssetsBundleService')
        ->setRenderer(new \Zend\View\Renderer\PhpRenderer());

        //Empty cache directory
        $this->emptycacheAction();

        //Retrieve configuration
        $aConfiguration = $this->getServiceLocator()->get('config');
        if(!isset($aConfiguration['asset_bundle'])) return $this->sendError('AssetsBundle configuration is undefined');
       	$aConfiguration  = $aConfiguration['asset_bundle'];

        $oConsole->writeLine('Start rendering assets : ');
        $aUnwantedKeys = array(
        	\AssetsBundle\Service\Service::ASSET_CSS => true,
        	\AssetsBundle\Service\Service::ASSET_LESS => true,
        	\AssetsBundle\Service\Service::ASSET_JS => true,
        	\AssetsBundle\Service\Service::ASSET_MEDIA => true
        );

        //Render all assets
        foreach(array_diff_key($aConfiguration['assets'], $aUnwantedKeys) as $sControllerName => $aConfig){
        	$oConsole->writeLine($sControllerName.' : '.\AssetsBundle\Service\Service::NO_ACTION, \Zend\Console\ColorInterface::GREEN);
       		//Render assets for no_actions
       		$oAssetsBundleService->setControllerName($sControllerName)
       		->setActionName(\AssetsBundle\Service\Service::NO_ACTION)
       		->renderAssets();

       		foreach(array_diff_key($aConfiguration['assets'][$sControllerName], $aUnwantedKeys) as $sActionName => $aActionConfiguration){
       			$oConsole->writeLine($sControllerName.' : '.$sActionName, \Zend\Console\ColorInterface::GREEN);
       			$oAssetsBundleService->setActionName($sActionName)->renderAssets();
       		}
       	}

        $oConsole->writeLine(\AssetsBundle\Service\Service::NO_CONTROLLER.' : '.\AssetsBundle\Service\Service::NO_ACTION, \Zend\Console\ColorInterface::GREEN);

        //Render assets for no_controller
        $oAssetsBundleService->setControllerName($sControllerName)
        ->setControllerName(\AssetsBundle\Service\Service::NO_CONTROLLER)
        ->setActionName(\AssetsBundle\Service\Service::NO_ACTION)
        ->renderAssets();

        $oConsole->writeLine('Assets rendered');
    }

    public function emptycacheAction(){
    	//Initialize AssetsBundle service
        $oAssetsBundleService = $this->getServiceLocator()->get('AssetsBundleService');

    	//Empty cache directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($oAssetsBundleService->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}
    }

    /**
     * @param string $sMessage
     * @return \Zend\View\Model\ConsoleModel
     */
    private function sendError($sMessage){
        $oView = new \Zend\View\Model\ConsoleModel();
        $oView->setErrorLevel(2);
        return $oView->setResult($sMessage.PHP_EOL);
    }
}