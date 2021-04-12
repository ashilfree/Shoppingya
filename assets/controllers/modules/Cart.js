/**
 * @property {HTMLFormElement} form
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
        this.form =document.querySelector('.js-cart-form');
        this.table = document.querySelector('table');
        this.updateCart = document.querySelector('.js-update-cart');
        this.subtotal = document.querySelector('#subtotal');
        this.total = document.querySelector('#total');
        this.updateTotals = document.querySelector('#updateTotals');
        this.proceedToCheckout = document.querySelector('#proceed-to-checkout');
        this.overlay = document.querySelector('.js-overlay');
        this.bindEvents();
    }


    bindEvents() {

        $('.js-select2').on('select2:select', (e) => {
            this.loadForm();
        });
        this.table.querySelectorAll('input[type="number"]').forEach( input => {

            input.addEventListener('change', evt => {
                this.loadForm();
            })
        })
        this.table.querySelectorAll('.js-quantity').forEach( div => {

            div.addEventListener('click', evt => {
                this.loadForm();
            })
        })
        // this.updateCart.querySelectorAll('a').forEach(a =>{
        //         a.addEventListener('click', e => {
        //             console.log('Cart');
        //         e.preventDefault();
        //         let data = {};
        //         this.table.querySelectorAll('.table_row').forEach(tr =>{
        //             data[tr.querySelector('input[type="hidden"]').value] = tr.querySelector('input[type="number"]').value;
        //         });
        //             this.loadUrl(a, 'POST', data);
        //     })
        // });
        //
        // this.table.querySelectorAll('[data-delete]').forEach(a => {
        //         a.addEventListener('click', e => {
        //         e.preventDefault();
        //         console.log('DELETE');
        //         this.loadUrl(a, 'DELETE');
        //     })
        // });

        // this.updateTotals.addEventListener('click', e =>{
        //     e.preventDefault();
        //     console.log('Update');
        //     this.loadUrl(this.updateTotals, 'POST', {'shipping': this.select.value})
        // });
    }

    async loadUrl(a, data = null) {
        this.showLoader();
        let init = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            method: 'POST',
            body: JSON.stringify(data)
        };
        const request = new Request(a, init);
        await fetch(request);

        location.reload();
    }

    async loadForm() {
        const data = new FormData(this.form);
        const url = this.form.getAttribute('action');

        let params = {};
        data.forEach((value, key) => {
            params[key] = value.toString();
        });
        return this.loadUrl(url, params);
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