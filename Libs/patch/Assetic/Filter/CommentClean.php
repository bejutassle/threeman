<?php
namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;


class CommentClean implements FilterInterface{
    private $filters;
    private $plugins;

    public function __construct(){
        $this->filters = array();
        $this->plugins = array();
    }

    public function setFilters(array $filters){
        $this->filters = $filters;
    }

    public function setFilter($name, $value){
        $this->filters[$name] = $value;
    }

    public function setPlugins(array $plugins){
        $this->plugins = $plugins;
    }

    public function setPlugin($name, $value){
        $this->plugins[$name] = $value;
    }

    public function filterLoad(AssetInterface $asset){
    }

    public function filterDump(AssetInterface $asset){
        $filters = $this->filters;
        $plugins = $this->plugins;

        if (isset($filters['ImportImports']) && true === $filters['ImportImports']) {
            if ($dir = $asset->getSourceDirectory()) {
                $filters['ImportImports'] = array('BasePath' => $dir);
            } else {
                unset($filters['ImportImports']);
            }
        }

        $asset->setContent($this->cleanComment($asset->getContent()), $filters, $plugins);
    }

   public function cleanComment($content){
            return preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#', '', $content);
   }
}
