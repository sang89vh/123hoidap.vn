<?php 
namespace Web\Helper;
use Zend\View\Helper\HelperInterface;
class Timer implements HelperInterface{
    /**
     * View object instance
     *
     * @var Renderer
     */
    protected $view = null;

    /**
     * Set the View object
     *
     * @param  Renderer $view
     * @return AbstractHelper
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }
    
    
    public function __invoke()
    {
    	echo 'Timer';
    }
}
?>