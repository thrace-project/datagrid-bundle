<?php
/*
 * This file is part of ThraceDataGridBundle
*
* (c) Nikolay Georgiev <azazen09@gmail.com>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/
namespace Thrace\DataGridBundle\DataGrid;

/**
 * CustomButtom class
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 *
 */
class CustomButton
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var string
     */
    protected $caption = '';
    
    /**
     * @var string
     */
    protected $buttonIcon = 'none';
    
    /**
     * @var string
     */
    protected $position = 'last';
    
    /**
     * @var string | null
     */
    protected $uri;
    
    /**
     * Construct
     * 
     * @param string $name
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct($name, array $options = array())
    {
        $this->setName($name);
        
        $methods = get_class_methods(get_class($this));
        
        foreach ($options as $key => $value){
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)){
                $this->$method($value);
            } else {
                throw new \InvalidArgumentException(sprintf('Method "%s" does not exist', $method));
            }
        }
    }
    
    /**
     * Sets button unique name
     * 
     * Provides a fluent interface
     * 
     * @param string $name
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Gets name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets button title
     * 
     * Provides a fluent interface
     * 
     * @param string $title
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Gets button title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set button caption
     * 
     * Provides a fluent interface
     * 
     * @param string $caption
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     */
    public function setCaption($caption)
    {
        $this->caption = (string) $caption;
        return $this;
    }
    
    /**
     * Gets button caption
     * 
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }
    
    /**
     * Sets button icon
     * 
     * Provides a fluent interface
     * 
     * @param string $buttonIcon
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     */
    public function setButtonIcon($buttonIcon)
    {
        $this->buttonIcon = (string) $buttonIcon;
        return $this;
    }
    
    /**
     * Gets button icon
     * 
     * @return string
     */
    public function getButtonIcon()
    {
        return $this->buttonIcon;
    }
    
    /**
     * Sets the position where the button will be added  (before|after)
     * Default is "last"
     * 
     * Provides a fluent interface
     * 
     * @param string $position
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     * @throws \InvalidArgumentException
     */
    public function setPosition($position)
    {
        if (!in_array($position, array('first', 'last'))){
            throw new \InvalidArgumentException(sprintf('Button position "%s" does not exist', $position));
        }
        $this->position = $position;
        return $this;
    }
    
    /**
     * Gets button position
     * 
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Set button uri
     * 
     * Provides a fluent interface
     * 
     * @param string $uri
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     */
    public function setUri($uri)
    {
        $this->uri = (string) $uri;
        return $this;
    }
    
    /**
     * Gets button uri
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * 
     * @return array
     */
    public function getOptions()
    {
        return array(
            'title' => $this->getTitle(),
            'caption' => $this->getCaption(),
            'buttonicon' => $this->getButtonIcon(),
            'position' => $this->getPosition(),
            'uri' => $this->getUri()
        );
    }
}