/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import $ from 'jquery';
import jQueryBridget from 'jquery-bridget';
import 'popper.js';
import 'bootstrap';
import 'bootstrap-select/dist/js/bootstrap-select';
import Filter from './controllers/modules/Filter'
import Global from './controllers/modules/Global'
import Cart from './controllers/modules/Cart'
import './controllers/comments/Comment.jsx'


new Filter(document.querySelector('.js-filter'));
new Global(document.querySelector('.js-body'));
new Cart(document.querySelector('.js-cart-form'));

import 'animsition';
import 'select2';

$(".js-select2").each(function () {
    $(this).select2({
        minimumResultsForSearch: 20,
        dropdownParent: $(this).next('.dropDownSelect2')
    });
});
import 'moment'
import 'daterangepicker/daterangepicker'
import './controllers/js/slick.min';
import './controllers/js/slick-custom';
import './controllers/js/parallax100';

$('.parallax100').parallax100();

import 'magnific-popup/dist/jquery.magnific-popup.min';

$('.gallery-lb').each(function () { // the containers for all your galleries
    $(this).magnificPopup({
        delegate: 'a', // the selector for gallery item
        type: 'image',
        gallery: {
            enabled: true
        },
        mainClass: 'mfp-fade'
    });
});

import Isotope from './controllers/js/isotope.pkgd.min';

jQueryBridget('isotope', Isotope, $);
import './controllers/js/sweetalert.min';

$('.js-addwish-b2').on('click', function (e) {
    e.preventDefault();
});

$('.header-cart-item-img').on('click', function (e) {
    e.preventDefault();
});

$('.js-addwish-b2').each(function () {
    var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
    $(this).on('click', function () {
        swal(nameProduct, "is added to wishlist !", "success");

        $(this).addClass('js-addedwish-b2');
        $(this).addClass('disabled');
        $(this).off('click');
    });
});

$('.js-addwish-detail').each(function () {
    var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

    $(this).on('click', function () {
        swal(nameProduct, "is added to wishlist !", "success");
        debugger
        $(this).addClass('js-addedwish-detail').addClass('disabled');
        $('#product-'+$(this).attr('id') + ' .js-addwish-b2').addClass('js-addedwish-b2').addClass('disabled');
        $(this).off('click');
    });
});

/*---------------------------------------------*/

$('.js-addcart-detail').each(function () {
    var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
    $(this).on('click', function () {
        swal(nameProduct, "is added to cart !", "success");
    });
});

import PerfectScrollbar from './controllers/js/perfect-scrollbar.min';

$('.js-pscroll').each(function () {
    $(this).css('position', 'relative');
    $(this).css('overflow', 'hidden');
    var ps = new PerfectScrollbar(this, {
        wheelSpeed: 1,
        scrollingThreshold: 1000,
        wheelPropagation: false,
    });

    $(window).on('resize', function () {
        ps.update();
    })
});

import './controllers/js/main';
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
import wNumb from 'wnumb'
import noUiSlider from 'nouislider'

var slider = document.getElementById('slider');
if (slider) {
    const min = document.getElementById('min')
    const max = document.getElementById('max')
    const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10
    const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10
    const range = noUiSlider.create(slider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        tooltips: [wNumb({decimals: 0}), wNumb({decimals: 0})],
        step: 10,
        range: {
            'min': minValue,
            'max': maxValue
        }
    });
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        min.dispatchEvent(new Event('change'))
    })
}

$('.sort-option').on('click', function () {
    if ($(this).hasClass('active')) {
        $(this).addClass($(this).children('a').attr('class'));
    } else {
        $('.sort-option').removeClass('active');
        $(this).addClass('active');
        $(this).addClass($(this).children('a').attr('class'));
    }
});

$(document).ready(function () {
    $('#tags label').addClass('dis-inline-block stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5');
});

$('#tags label').on('click', function () {
    if ($(this).hasClass('tag-filter-active')) {
        $(this).removeClass('tag-filter-active');
    } else {
        $(this).addClass('tag-filter-active');
    }
});

import places from './controllers/js/places'

let shopAddress = document.querySelector('#address');

if (shopAddress !== null) {
    let place = places({
        container: shopAddress
    });

    place.on('change', e => {
        document.querySelector('#lat').value = e.suggestion.latlng.lat;
        document.querySelector('#lng').value = e.suggestion.latlng.lng;
        document.querySelector('#city').value = e.suggestion.name;
        document.querySelector('#province').value = e.suggestion.administrative;
        document.querySelector('#postalCode').value = (e.suggestion.postcode != null)? e.suggestion.postcode : '' ;
    })
}

$('#updateTotals').on('click', function () {
    // let shippingValue = parseInt($('.js-select2').val() );
    // $('#total').text(parseInt($('#subtotal').text()) + shippingValue);
    // if(shippingValue !== 0){
    //     $('#proceed-to-checkout').removeAttr('disabled').removeAttr('data-original-title').attr('href', "/shopping-cart/checkout");
    // }else{
    //     $('#proceed-to-checkout').attr('disabled', true).attr('data-original-title', 'Calculate Shipping').attr('href', "javascript:void(0)");
    // }
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
