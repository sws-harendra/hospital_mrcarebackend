<script src="https://mrcarehealth.com/frontend/js/cookieconsent.min.js"></script>



<!--bootstrap js-->
<script src="https://mrcarehealth.com/frontend/js/bootstrap.bundle.min.js"></script>
<!--font-awesome js-->
<script src="https://mrcarehealth.com/frontend/js/Font-Awesome.js"></script>
<!-- select js -->
<script src="https://mrcarehealth.com/frontend/js/select2.min.js"></script>
<!-- counter up js -->
<script src="https://mrcarehealth.com/frontend/js/jquery.waypoints.min.js"></script>
<script src="https://mrcarehealth.com/frontend/js/jquery.countup.min.js"></script>
<!-- slick js -->
<script src="https://mrcarehealth.com/frontend/js/slick.min.js"></script>
<!-- calender js -->
<script src="https://mrcarehealth.com/frontend/js/jquery.calendar.js"></script>
<!-- sticky sidebar -->
<script src="https://mrcarehealth.com/frontend/js/sticky_sidebar.js"></script>
<script src="https://mrcarehealth.com/backend/js/bootstrap-datepicker.min.js"></script>
<!--main/custom js-->
<script src="https://mrcarehealth.com/frontend/js/main.js"></script>

<script src="https://mrcarehealth.com/toastr/toastr.min.js"></script>

<script src="https://mrcarehealth.com/js/app.js"></script>

<script>
    const toggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    toggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
    const swiper = new Swiper('.swiper', {

        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            // mobile
            0: {
                slidesPerView: 1,
            },
            // tablet
            768: {
                slidesPerView: 1,
            },
            // laptop
            1024: {
                slidesPerView: 3,
            }
        },
    });
</script>
