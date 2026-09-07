class Slider {
    sliderElement;
    currentSlide;
    slides;
    slidesContainer;
    totalSlides;
    duration = 500; // Duration in milliseconds
    autoPlayInterval = null;
    autoPlay = false; // Set to true to enable auto-play
    dragging = false;
    isDragging = false;

    constructor(querySelector, duration = 500, autoPlay = false, autoPlayDuration = 3000, dragging = false) {
        this.sliderElement = $(querySelector);
        this.currentSlide = 0;
        this.slides = this.sliderElement.find('.slide-container');
        this.slidesContainer = this.sliderElement.find('.slides');
        this.totalSlides = this.slides.length;
        this.duration = duration;
        this.autoPlay = autoPlay;
        this.autoPlayDuration = autoPlayDuration;
        this.dragging = dragging;
        this.slideWidth = this.sliderElement.width();
        this.init();
    }

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updateSlider();
    }

    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.updateSlider();
    }

    updateSlider() {
        this.slidesContainer.css('transform', `translateX(-${this.currentSlide * 100/this.totalSlides}%)`);
    }

    init() {
        this.sliderElement.find('.controls .next').on('click', () => this.nextSlide());
        this.sliderElement.find('.controls .prev').on('click', () => this.prevSlide());
        this.slidesContainer.css('transition', `transform ${this.duration}ms ease-in-out`);
        this.sliderElement.find('.slide-image').css('pointer-events', 'none'); // Disable pointer events on images to allow dragging
        this.initAutoPlay();
        this.initDragging();
        this.updateSlider();
    }

    initAutoPlay() {
        if (this.autoPlay) {
            this.autoPlayInterval = setInterval(() => this.nextSlide(), this.autoPlayDuration);
            this.sliderElement.on('mouseenter', () => clearInterval(this.autoPlayInterval));
            this.sliderElement.on('mouseleave', () => { this.autoPlayInterval = setInterval(() => this.nextSlide(), this.autoPlayDuration); });
        }
    }

    initDragging() {
        if (this.dragging) {
            let startX = 0;
            let isDragging = false;

            this.slidesContainer.on('mousedown touchstart', (e) => {
                isDragging = true;
                startX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
                this.slidesContainer.css('transition', 'none');
            });

            this.slidesContainer.on('mousemove touchmove', (e) => {
                if (!isDragging) return;
                const currentX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
                const diffX = currentX - startX;
                this.slidesContainer.css('transform', `translateX(calc(-${this.currentSlide * 100/this.totalSlides}% + ${diffX}px))`);
                if (Math.abs(diffX) > this.slideWidth) {
                    console.log('diffX :', diffX);
                    if (diffX > 0) {
                        this.prevSlide();
                        diffX = 0; // Reset diffX to prevent multiple slide changes
                    } else {
                        this.nextSlide();
                        diffX = 0; // Reset diffX to prevent multiple slide changes
                    }
                }
            });

            this.slidesContainer.on('mouseup touchend', () => {
                isDragging = false;
                this.slidesContainer.css('transition', `transform ${this.duration}ms ease-in-out`);
            });
        }
    }
}