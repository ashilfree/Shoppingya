import {Flipper, spring} from 'flip-toolkit'
import Isotope from '../js/isotope.pkgd.min';



/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLElement} overlay
 * @property {HTMLFormElement} form
 * @property {HTMLFormElement} search
 * @property {number} page
 * @property {boolean} moreNav
 */

export default class Filter {

    /**
     *
     * @param {HTMLElement|null} element
     */
    constructor(element) {
        if (element === null) {
            return
        }

        this.pagination = document.querySelector('.js-filter-pagination');
        this.content = document.querySelector('.js-filter-content');
        this.sorting = document.querySelector('.js-filter-sorting');
        this.overlay = document.querySelector('.js-overlay');
        this.form = document.querySelector('.js-filter-form');
        this.search = document.querySelector('.js-filter-search');
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1);
        this.moreNav = this.page === 1;
        this.bindEvents();
    }


    bindEvents() {
        const aClickListener = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        };
        this.sorting.addEventListener('click', e => {
            aClickListener(e);
            this.page = 1;
        });
        if (this.moreNav) {
            this.pagination.innerHTML = '<button  class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04"  data-page="1"> Load More </button>';
            this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this))
        } else {
            this.pagination.addEventListener('click', aClickListener)
        }
        this.form.querySelector('select').addEventListener('change', this.loadForm.bind(this));
        this.search.querySelector('a').addEventListener('click', this.loadSearch.bind(this));
        this.form.querySelectorAll('input:not(#address)').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        });
    }

    async loadMore() {
        const button = this.pagination.querySelector('button');
        button.setAttribute('disabled', 'disabled');
        this.page++;
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        params.set('page', this.page.toString());
        await this.loadUrl(url.pathname + '?' + params.toString(), true);
        button.removeAttribute('disabled');
    }

    async loadForm() {
        this.page = 1;
        const data = new FormData(this.form);
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        const params = new URLSearchParams();
        data.forEach((value, key) => {
            params.append(key, value.toString());
        });
        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    async loadSearch() {
        this.page = 1;
        const data = new FormData(this.search);
        const url = new URL(this.search.getAttribute('action') || window.location.href);
        const params = new URLSearchParams();
        data.forEach((value, key) => {
            params.append(key, value.toString());
        });
        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    async loadUrl(url, append = false) {
        this.showLoader();
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', '1');
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            this.flipContent(data.content, append);
            this.sorting.innerHTML = data.sorting;
            if (!this.moreNav) {
                this.pagination.innerHTML = data.pagination;
            } else if (this.page === data.pages) {
                this.pagination.style.display = 'none';
            } else {
                this.pagination.style.display = null;
            }

            if(data.min || data.max){
                this.updatePrices(data);
            }


            params.delete('ajax');
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
        } else {
            console.error(response);
        }
        this.hideLoader();
        //  this.reinitializeIsotope();
    }

    flipContent(content, append) {
        const springConfig = 'gentle';
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({translateY, opacity}) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            })
        };
        const appearSpring = function (element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({translateY, opacity}) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 20
            })
        };

        const flipper = new Flipper({
            element: this.content
        });
        this.content.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                shouldFlip: false,
                onExit: exitSpring
            })
        });
        flipper.recordBeforeUpdate();
        if (append) {
            this.content.innerHTML += content;
        } else {
            this.content.innerHTML = content;
        }
        this.content.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                onAppear: appearSpring
            })
        });
        flipper.update();
        this.reinitializeModal();
    }

    reinitializeModal() {
        $('.js-show-modal1').on('click',function(e){
            e.preventDefault();
            $('#title').text($(this).data('title'));
            $('#price').text($(this).data('price'));
            $('#description').text($(this).data('description'));
            $('#quantity').attr('max', $(this).data('quantity'));
            $('#wich-icon a').attr('href', $(this).data('href')).attr('id', $(this).data('id')).addClass($(this).data('disabled'));
            $('#cart-add-button').attr('href', $(this).data('href-a'));
            debugger

            var i = 1;
            var access = true;
            $('#product_cart_size').empty();
            $('#product_cart_size').append("<option value selected>Choose an option</option>");


            while (access){

                var size = $(this).data('size' + i);
                if(!size)
                    break;

                $('#product_cart_size').append("<option value='" + i + "' > Size " + size + "</option>");
                i++;
            }


            $('#product_cart_color').append("<option value='1'>Red</option>");





            i = 1;

            while (access){

                var picture = $(this).data('pictures' + i);
                if(!picture)
                    break;
                var picturePath = "/media/products/"+picture;
                $('div.img'+i).data('thumb', picturePath);
                $('img.img'+i).attr('src', picturePath);
                $('a.img'+i).attr('href', picturePath);
                i++;
            }
            $('.slick3').slick('refresh');
            $('.js-modal1').addClass('show-modal1');
        });
    }

    showLoader() {
        this.overlay.classList.add('is-loading');
        const loader = this.overlay.querySelector('.js-loader');
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'false');
        loader.style.display = null;
    }

    hideLoader() {
        this.overlay.classList.remove('is-loading');
        const loader = this.overlay.querySelector('.js-loader');
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'true');
        loader.style.display = 'none';
    }

    updatePrices({min, max}) {
        const slider = document.getElementById('slider');
        if (slider === null) {
            return
        }
        slider.noUiSlider.updateOptions({
            start: [parseInt(min), parseInt(max)],
            range: {
                min: [parseInt(min)],
                max: [parseInt(max)]
            }
        })
    }

    reinitializeIsotope() {
        $('.isotope-grid').each(function () {
            $(this).isotope('reloadItems').isotope();
        })
    }
}