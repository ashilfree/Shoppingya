import $ from "jquery";

/**
 * @property {HTMLElement} select
 * @property {HTMLElement} checkoutButton
 * @property {HTMLElement} locale
 * @property {HTMLElement} orderId
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
        this.locale = document.getElementById('#locale');
        this.orderId = document.getElementById('#orderId');
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
            console.log(this.locale);
                if($('.js-select2').val()) {
                    let data = {
                        payment: $('.js-select2').select2('data')[0].text
                    };
                    let url = "/" + this.locale.value +"/order/create-session/" + this.orderId.value
                    fetch(url, {
                        method: 'POST', // or 'PUT'
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (session) {
                            if(session.error === 'order'){
                                window.location.replace("/{{ locale }}/order/{{ order.id }}");
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