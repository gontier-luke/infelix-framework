<?php

namespace Classes;

use Classes\Interfaces\SliderableInterface;
use Classes\Collections\EntityCollection;
use Exceptions\SliderException;

class Slider{
    protected EntityCollection $slides;
    protected string $htmlClass = '';

    public function __construct(string $class)
    {
        if(in_array('SliderableInterface',class_implements($class))){
            $repository = str_replace('Model','',$class);
            $this->slides = $repository::getAll();
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

    public function renderSlider(): string
    {
        foreach ($this->slides->getAll() as $slide) {
            if(!$slide->isActive()){
                $this->slides->remove($this->slides->indexOf($slide));
            }
        }
        $html = render('tools/slider', ['slides' => $this->slides, 'htmlClass' => $this->htmlClass]); 
        return $html;
    }

    public function __toString(): string
    {
        return $this->renderSlider();
    }
}
