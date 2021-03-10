import $ from "jquery";

/**
 * @property {HTMLElement} cart
 * @property {HTMLElement} removeWish
 * @property {HTMLElement} addWish
 * @property {HTMLElement} iconWish
 * @property {HTMLElement} iconCart
 * @property {HTMLElement} iconWishModal
 * @property {HTMLElement} clearWishList
 */

export default class Global {

    /**
     *
     * @param {HTMLElement|null} element
     */
    constructor(element) {
        if (element === null) {
            return
        }

        this.cart = document.querySelector('.js-panel-cart');
        this.wishList = document.querySelector('.js-panel-wish');
        this.addWish = document.querySelector('.js-filter-content');
        this.addCart = document.querySelector('.js-modal1');
        this.iconWish = document.querySelector('.js-wish-list-icon');
        this.iconCart = document.querySelector('.js-cart-icon');
        this.iconWishModal = document.querySelector('#wich-icon');
        this.clearWishList = document.querySelector('.js-panel-wish .header-cart-buttons');
        this.clearCart = document.querySelector('.js-panel-cart .header-cart-buttons');

        this.bindEvents();
    }

    bindEvents() {
        // if(this.addWish){
        //     this.addWish.querySelectorAll('.js-addwish-b2').forEach(a => {
        //         a.addEventListener('click', e => {
        //             e.preventDefault();
        //             this.loadUrl(a);
        //         })
        //     });
        // }
        // if(this.iconWishModal){
        //     this.iconWishModal.querySelectorAll('a').forEach(a => {
        //         a.addEventListener('click', e =>{
        //             e.preventDefault();
        //             this.loadUrl(a);
        //         })
        //     });
        // }
        if (this.addCart) {
            let addCart = this.addCart.querySelector('#cart-add-button');
            let selectedSize = this.addCart.querySelector('.js-select2');
            let nameProduct = this.addCart.querySelector('.js-name-detail');

            if (addCart) {
                 addCart.addEventListener('click', e => {

                    if (selectedSize.value > 0) {
                        addCart.href = addCart.getAttribute('href') + '/' + selectedSize.value
                       // swal(nameProduct.innerHTML, "is added to cart !", "success");
                       //  e.returnValue = true
                    }else{
                        e.preventDefault();
                        swal("PLZ", "Select a size !", "info");
                    }
                    //  console.log(addCart.getAttribute('href'));
                    //   this.loadUrl(addCart, 'POST');
                })
            }
        }
        // this.wishList.querySelectorAll('[data-delete]').forEach(a => {
        //     a.addEventListener('click', e => {
        //         e.preventDefault();
        //         this.loadUrl(a, 'DELETE');
        //     })
        // });
        // this.clearWishList.querySelectorAll('a').forEach(a => {
        //     a.addEventListener('click', e => {
        //         e.preventDefault();
        //         this.loadUrl(a, 'DELETE', true);
        //     })
        // });
        // this.clearCart.querySelectorAll('[data-token]').forEach(a => {
        //     a.addEventListener('click', e => {
        //         e.preventDefault();
        //        this.loadUrl(a, 'DELETE', true);
        //     })
        // });
        // this.cart.querySelectorAll('[data-delete]').forEach(a => {
        //     a.addEventListener('click', e => {
        //         e.preventDefault();
        //         this.loadUrl(a, 'DELETE');
        //     })
        // });
    }

    async loadUrl(a, method = 'GET', clear = false) {
        let init = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        if (method === 'DELETE') {
            init = {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({'token': a.dataset.token})
            };
        }
        if (method === 'POST') {
            const form = new FormData(this.addCart.querySelector('form'));
            let data = {};
            form.forEach((value, key) => {
                data[key] = value;
            });
            init = {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            };
        }
        const request = new Request(a.getAttribute('href'), init);
        const response = await fetch(request);
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            if (data.type === 'cart') {
                this.iconCart.dataset.notify = data.countItemsCart;
                this.cart.querySelector('#sumItemsCart').textContent = data.sumItemsCart;
                if (request.method === 'DELETE') {
                    if (clear) {
                        this.cart.querySelector('ul').innerHTML = data.cart;
                        this.cart.querySelector('div.notEmpty').style.display = 'none';
                        this.cart.querySelector('div.empty').removeAttribute('style');
                    } else {
                        a.parentNode.parentNode.removeChild(a.parentNode);
                        if (data.countItemsCart === 0) {
                            this.cart.querySelector('div.notEmpty').style.display = 'none';
                            this.cart.querySelector('div.empty').removeAttribute('style');
                        }
                    }
                } else {
                    this.cart.querySelector('ul').innerHTML += data.cart;
                    if (data.countItemsCart === 1) {
                        this.cart.querySelector('div.empty').style.display = 'none';
                        this.cart.querySelector('div.notEmpty').removeAttribute('style');
                    }
                    this.cart.querySelectorAll('[data-delete]').forEach(a => {
                        a.addEventListener('click', e => {
                            e.preventDefault();
                            this.loadUrl(a, true);
                        })
                    });
                }
            } else {
                this.iconWish.dataset.notify = data.countItemsWishList
                this.wishList.querySelector('#sumItemsWishList').textContent = data.sumItemsWishList;
                if (request.method === 'DELETE') {
                    if (clear) {
                        this.wishList.querySelector('ul').innerHTML = data.wishList;
                        this.addWish.querySelectorAll('.js-addwish-b2').forEach(a => {
                            a.classList.remove('js-addedwish-b2');
                            a.classList.remove('disabled');
                            this.wishList.querySelector('div.notEmpty').style.display = 'none';
                            this.wishList.querySelector('div.empty').removeAttribute('style');
                        });
                    } else {
                        a.parentNode.parentNode.removeChild(a.parentNode);
                        this.addWish.querySelector('#product-' + a.dataset.id).querySelector('.js-addwish-b2').classList.remove('js-addedwish-b2');
                        this.addWish.querySelector('#product-' + a.dataset.id).querySelector('.js-addwish-b2').classList.remove('disabled');
                        if (data.countItemsWishList === 0) {
                            this.wishList.querySelector('div.notEmpty').style.display = 'none';
                            this.wishList.querySelector('div.empty').removeAttribute('style');
                        }
                    }
                } else {
                    this.wishList.querySelector('ul').innerHTML += data.wishList;
                    if (data.countItemsWishList === 1) {
                        this.wishList.querySelector('div.empty').style.display = 'none';
                        this.wishList.querySelector('div.notEmpty').removeAttribute('style');
                    }
                    this.wishList.querySelectorAll('[data-delete]').forEach(a => {
                        a.addEventListener('click', e => {
                            e.preventDefault();
                            this.loadUrl(a, true);
                        })
                    });
                }
            }
        } else {
            console.error(response);
        }
    }
}