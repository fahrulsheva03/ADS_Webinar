    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/aos.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        $(window).on('load', function(){
        // Preloader
        $('.loader').fadeOut();
        $('.loader-mask').delay(350).fadeOut('slow');
        });
    </script>
    <script>
        $('.data').hide()
        jQuery('button.showBtn1').on('click', function () {
            jQuery('.data1').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn2').on('click', function () {
            jQuery('.data2').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn3').on('click', function () {
            jQuery('.data3').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn4').on('click', function () {
            jQuery('.data4').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn5').on('click', function () {
            jQuery('.data5').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn6').on('click', function () {
            jQuery('.data6').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn7').on('click', function () {
            jQuery('.data7').toggle();
        })
        $('.data').hide()
        jQuery('button.showBtn8').on('click', function () {
            jQuery('.data8').toggle();
        })
    </script>
    <script>
        $('#owl-carouselone').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            navText: ["<i class='fas fa-arrow-left'></i>", "<i class='fas fa-arrow-right'></i>"],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
        $('#owl-carouseltwo').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            navText: ["<i class='fas fa-arrow-left'></i>", "<i class='fas fa-arrow-right'></i>"],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
    </script>
    <script>
        AOS.init();
        function lightbox_open() {
            var lightBoxVideo = document.getElementById("VisaChipCardVideo");
            //   window.scrollTo(0, 0);
            document.getElementById('light').style.display = 'block';
            document.getElementById('fade1').style.display = 'block';
            lightBoxVideo.play();
        }

        function lightbox_close() {
            var lightBoxVideo = document.getElementById("VisaChipCardVideo");
            document.getElementById('light').style.display = 'none';
            document.getElementById('fade1').style.display = 'none';
            lightBoxVideo.pause();
        }
    </script>
    <script>
        $(document).ready(function () {

            var counters = $(".count");
            var countersQuantity = counters.length;
            var counter = [];

            for (i = 0; i < countersQuantity; i++) {
                counter[i] = parseInt(counters[i].innerHTML);
            }

            var count = function (start, value, id) {
                var localStart = start;
                setInterval(function () {
                    if (localStart < value) {
                        localStart++;
                        counters[id].innerHTML = localStart;
                    }
                }, 40);
            }

            for (j = 0; j < countersQuantity; j++) {
                count(0, counter[j], j);
            }
        });



        $('.count').each(function () {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 3300,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    </script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            effect: "cards",
            grabCursor: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    </script>
    <script>
        var btn = $('#button');

         $(window).scroll(function() {
           if ($(window).scrollTop() > 300) {
           btn.addClass('show');
         }
          else {
           btn.removeClass('show');
         }
         });
         btn.on('click', function(e) {
         e.preventDefault();
         $('html, body').animate({scrollTop:0}, '300');
       });
    </script>


