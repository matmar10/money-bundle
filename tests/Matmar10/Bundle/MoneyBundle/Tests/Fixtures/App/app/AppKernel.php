<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
 
class AppKernel extends Kernel
{
    public function registerBundles()
    {   
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Matmar10\Bundle\MoneyBundle\Matmar10MoneyBundle(),
        );
    }   
 
    public function registerContainerConfiguration(LoaderInterface $loader)
    {   
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }   
 
    /** 
     * @return string
     */
    public function getCacheDir()
    {   
        return sys_get_temp_dir().'/Matmar10MoneyBundle/cache';
    }   
 
    /** 
     * @return string
     */
    public function getLogDir()
    {   
        return sys_get_temp_dir().'/Matmar10MoneyBundle/logs';
    }   
}
