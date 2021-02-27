/**
 * @property {HTMLElement} table
 * @property {HTMLElement} updateCart
 * @property {HTMLElement} ApplyCoupon
 * @property {HTMLElement} subtotal
 * @property {HTMLElement} total
 * @property {HTMLElement} select
 * @property {HTMLElement} updateTotals
 * @property {HTMLElement} proceedToCheckout
 * @property {HTMLElement} overlay
 */

export default class Cart {

    /**
     *
     * @param {HTMLElement|null} element
     */
    constructor(element) {
        if (element === null) {
            return
        }

        this.table = document.querySelector('table');
        this.updateCart = document.querySelector('.js-update-cart');
        this.subtotal = document.querySelector('#subtotal');
        this.total = document.querySelector('#total');
        this.select = document.querySelector('.js-select2');
        this.updateTotals = document.querySelector('#updateTotals');
        this.proceedToCheckout = document.querySelector('#proceed-to-checkout');
        this.overlay = document.querySelector('.js-overlay');
        this.bindEvents();
    }


    bindEvents() {
        this.updateCart.querySelectorAll('a').forEach(a =>{
                a.addEventListener('click', e => {
                    console.log('Cart');
                e.preventDefault();
                let data = {};
                this.table.querySelectorAll('.table_row').forEach(tr =>{
                    data[tr.querySelector('input[type="hidden"]').value] = tr.querySelector('input[type="number"]').value;
                });
                    this.loadUrl(a, 'POST', data);
            })
        });

        this.table.querySelectorAll('[data-delete]').forEach(a => {
                a.addEventListener('click', e => {
                e.preventDefault();
                console.log('DELETE');
                this.loadUrl(a, 'DELETE');
            })
        });

        this.updateTotals.addEventListener('click', e =>{
            e.preventDefault();
            console.log('Update');
            this.loadUrl(this.updateTotals, 'POST', {'shipping': this.select.value})
        });
    }

    async loadUrl(a, method = 'GET', data = null) {
        this.showLoader();
        let init = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        if (method === 'DELETE'){
            init = {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({'token': a.dataset.token})
            };
        }
        if (method === 'POST'){
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
        if (response.status >= 200 && response.status < 300)
        {
            const data = await response.json();
                if(request.method === 'DELETE'){
                        a.parentNode.parentNode.parentNode.removeChild(a.parentNode.parentNode);
                }
                else
                {
                    if(data.type === 'shipping'){
                        if(parseInt(this.select.value) !== 0){
                            this.proceedToCheckout.removeAttribute('disabled');
                            this.proceedToCheckout.removeAttribute('data-original-title');
                            this.proceedToCheckout.setAttribute('href', "/shopping-cart/checkout");
                        }else{
                            this.proceedToCheckout.setAttribute('disabled', 'true');
                            this.proceedToCheckout.setAttribute('data-original-title', 'Calculate Shipping');
                            this.proceedToCheckout.setAttribute('href', "javascript:void(0)");
                        }
                    }else{
                        this.table.querySelectorAll('.table_row').forEach(tr =>{
                            tr.querySelector('.column-5 span').textContent =  (tr.querySelector('.column-5').dataset.price * tr.querySelector('input[type="number"]').value).toString();
                        });
                    }
                }
            this.subtotal.textContent = data.sumItemsCart;
            this.total.textContent = (parseInt(data.sumItemsCart) + parseInt(this.select.value)).toString();
        }else {
            console.error(response);
        }
        this.hideLoader();
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
}