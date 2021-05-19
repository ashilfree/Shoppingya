import $ from "jquery";

/**
 * @property {HTMLElement} select
 * @property {HTMLElement} checkoutButton
 */

export default class Checkout {

    /**
     * @param {HTMLElement|null} element
     */
    constructor(element) {
        if (element === null) {
            return
        }
        this.checkoutButton = document.querySelector('#checkout-button');
        this.select = document.querySelector('.js-select2');
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
        if (this.checkoutButton) {

            checkoutButton.addEventListener("click", function () {
            console.log(this.dataset.id);
            let id = this.dataset.id;
            let locale = this.dataset.locale;
                if($('.js-select2').val()) {
                    let data = {
                        payment: $('.js-select2').select2('data')[0].text
                    };
                    let url = "/" + locale +"/order/create-session/" + id
                    fetch(url, {
                        method: 'POST', // or 'PUT'
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                        .then(function (response) {
                            window.location.replace(response.url);
                        })
                        .then(function (session) {
                            if(session.error === 'order'){
                                window.location.replace("/" + locale +"/order/" + id);
                            }else{
                                // return stripe.redirectToCheckout({ sessionId: session.id });
                            }
                        })
                        .then(function (result) {
                            // If redirectToCheckout fails due to a browser or network
                            // error, you should display the localized error message to your
                            // customer using error.message.
                            if (result.error) {
                                alert(result.error.message);
                            }
                        })
                        .catch(function (error) {
                            console.error("Error:", error);
                        });
                }
            });
        }
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