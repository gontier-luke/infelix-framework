$(document).ready( function () {

    let sliders = $('.slider');
    sliders.each(function(index, slider) {
        let sliderInstance = new Slider($(slider), currentIndex);

        $(slider).find('.next-slide').on('click', function() {
            sliderInstance.next();
        });

        $(slider).find('.prev-slide').on('click', function() {
            sliderInstance.prev();
        });
    });

});

class Slider {
    constructor(slider, currentIndex = 0) {
        this.slider = slider;
        this.slides = slider.find('.slides');
        this.slidesContainer = slider.find('.slides .slides-container');
        this.slideList = slider.find('.slide');
        this.slideCount = this.slidesContainer.children().length;
        this.currentIndex = currentIndex;

        this.init();
    }

    init() {
        $(this.slideList[this.currentIndex]).addClass('active');
    }

    next() {
        $(this.slideList[this.currentIndex]).removeClass('active');
        this.currentIndex = (this.currentIndex + 1) % this.slideCount;
        $(this.slideList[this.currentIndex]).addClass('active');
        this.slidesContainer.css('transform', `translateX(-${this.slides.width() * this.currentIndex}px)`);
        console.log(this.slideCount);
        console.log(this.currentIndex);
        console.log((this.currentIndex) % this.slideCount);
        console.log(this.slides.width() * this.currentIndex);
    }

    prev() {
        $(this.slideList[this.currentIndex]).removeClass('active');
        this.currentIndex = (this.currentIndex - 1 + this.slideCount) % this.slideCount;
        $(this.slideList[this.currentIndex]).addClass('active');
        this.slidesContainer.css('transform', `translateX(-${this.slides.width() * this.currentIndex}px)`);
        console.log(this.slides.width() * this.currentIndex);
    }
}