<?php

namespace Classes;

use Classes\Interfaces\SliderableInterface;
use Classes\Collections\EntityCollection;
use Exceptions\SliderException;

class Slider{
    protected EntityCollection $slides;
    protected string $htmlClass = '';
    protected string $classSlider;
    protected string $template = 'slider';

    protected const TEMPLATE_PATH = 'tools/slider/';

    public function __construct(string $class)
    {
        if(in_array('Classes\Interfaces\SliderableInterface',class_implements($class))){
            $this->slides = new EntityCollection($class);
            $this->classSlider = $class;
            return $this;
        }
        throw new SliderException('Class '.$class.' must implement SliderableInterface');
    }

    public function getSlides(): EntityCollection
    {
        return $this->slides;
    }

    public function add(SliderableInterface $slide): self
    {
        if(get_class($slide) !== $this->classSlider){
            throw new SliderException('Slide must be an instance of '.$this->classSlider);
        }
        $this->slides->add($slide);
        return $this;
    }

    public function remove(int $index): self
    {
        $this->slides->remove($index);
        return $this;
    }

    public function htmlClass(string $class): self
    {
        $this->htmlClass = $class;
        return $this;
    }

    public function renderSlider(?string $template = null): string
    {
        if(!is_null($template)){
            $this->setTemplate($template);
        }
        $html = render(self::TEMPLATE_PATH . $this->template, ['slides' => $this->slides, 'htmlClass' => $this->htmlClass]); 
        return $html;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function __toString(): string
    {
        return $this->renderSlider();
    }
}
