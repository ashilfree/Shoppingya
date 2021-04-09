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
        this.productDetail = document.querySelector('.js-product-detail');
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

            if (addCart) {
                 addCart.addEventListener('click', e => {
                     var quantity = selectedSize.options[selectedSize.selectedIndex].getAttribute('data-quantity');
                    if (selectedSize.value > 0) {
                        if(quantity > 0){
                            addCart.href = addCart.getAttribute('href') + '/' + selectedSize.value
                        }else{
                            e.preventDefault();
                            if(document.dir == 'rtl'){
                                swal("تحذير", "المنتج نفذ من المخزون!", "warning");
                            }else{
                                swal("Warning", "Product Out of Stock !", "warning");
                            }

                        }
                    }else{
                        e.preventDefault();
                        if(document.dir === 'rtl'){
                            swal("من فضلك", "اختر حجما !", "info");
                        }else{
                            swal("PLZ", "Select a size !", "info");
                        }

                    }
                    //  console.log(addCart.getAttribute('href'));
                    //   this.loadUrl(addCart, 'POST');
                })
            }
        }
        if(this.productDetail){
            let addToCart = this.productDetail.querySelector('#add-to-cart');
            let selectedSize = this.productDetail.querySelector('.js-select2');
            if (addToCart) {
                addToCart.addEventListener('click', e => {
                    var quantity = selectedSize.options[selectedSize.selectedIndex].getAttribute('data-quantity');
                    if (selectedSize.value > 0) {
                        if(quantity > 0){
                            addToCart.href = addToCart.getAttribute('href') + '/' + selectedSize.value
                        }else{
                            e.preventDefault();
                            if(document.dir === 'rtl'){
                                swal("تحذير", "المنتج نفذ من المخزون!", "warning");
                            }else{
                                swal("Warning", "Product Out of Stock !", "warning");
                            }

                        }
                    }else{
                        e.preventDefault();
                        if(document.dir === 'rtl'){
                            swal("من فضلك", "اختر حجما !", "info");
                        }else{
                            swal("PLZ", "Select a size !", "info");
                        }

                    }
                })
            }
        }
        this.reinitializeModal();
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

    reinitializeModal() {

        $('.js-show-modal1').on('click',function(e){

            e.preventDefault();
            let discount = $(this).data('discount');
            if(discount === '0.00 KWD' || discount === '0.00 دينار كويتي'){
                $('#price-block1').css('display','none')
                $('#price-block2').css('display','block')
            }else{
                $('#price-block2').css('display','none')
                $('#price-block1').css('display','block')
            }
            $('#name').text($(this).data('name'));
            $('#price').text($(this).data('price'));
            $('#price2').text($(this).data('price'));
            $('#discount').text($(this).data('discount'));
            $('#description').text($(this).data('description'));
            $('#cart-add-button').attr('href', $(this).data('href-a'));

            var i = 1;
            var access = true;

            $('.js-select2').empty();
            if(document.dir === 'ltr'){
                $('.js-select2').append('<option value="-1">Choose an option</option>');
                while (access){

                    var size = $(this).data('size' + i);
                    var catalog = $(this).data('catalog' + i);
                    var quantity = $(this).data('quantity' + i);
                    if(!size)
                        break;

                    $('.js-select2').append("<option value='" + catalog + "' data-quantity='" + quantity + "'> Size " + size + "</option>");
                    i++;
                }
            }else{
                $('.js-select2').append('<option value="-1">اختر أحد الخيارات أدناه</option>');
                while (access){

                    var size = $(this).data('size' + i);
                    var catalog = $(this).data('catalog' + i);
                    var quantity = $(this).data('quantity' + i);
                    if(!size)
                        break;

                    $('.js-select2').append("<option value='" + catalog + "' data-quantity='" + quantity + "'> الحجم " + size + "</option>");
                    i++;
                }
            }


            i = 1;
            $('.slick3.gallery-lb').empty();
            $('.slick3').slick('removeSlide', null, null, true);
            while (access){

                var picture = $(this).data('image' + i);
                if(!picture)
                    break;
                var picturePath = "/media/images/product/"+picture;
                var sold_out = (document.dir === "ltr")?'Sold out' :'نفاذ المخزون';
                $('.slick3.gallery-lb').append('<div class="item-slick3" data-thumb=" ' + picturePath +' "><a class="sold_out" style="display:none" href="https://abc.com/">'+sold_out+'</a><div class="wrap-pic-w pos-relative"><img src=" ' + picturePath + ' " alt="IMG-PRODUCT"><a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href=" ' + picturePath + ' "><i class="fa fa-expand"></i></a></div></div>')
                // $('div.img'+i).data('thumb', picturePath);
                // $('img.img'+i).attr('src', picturePath);
                // $('a.img'+i).attr('href', picturePath);
                i++;
            }
            $('.slick3').slick('refresh');
            $('.js-modal1').addClass('show-modal1');
        });
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