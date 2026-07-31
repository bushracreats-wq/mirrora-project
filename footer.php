<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container px-3">
        <div class="row g-4">

            <!-- Brand Info -->
            <div class="col-md-4 mb-2 mb-md-0">
                <h4 class="fw-bold fs-5 footer-brand">MIRRORA</h4>

                <p class="text-secondary small mb-0 footer-text">
                    Virtual fitting room experience. Try before you buy with our advanced AI technology.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4 mb-2 mb-md-0">

                <h5 class="mb-3 fs-6 fw-bold">Quick Links</h5>

                <ul class="list-unstyled mb-0">

                    <li class="mb-2">
                        <a href="index.php" class="text-white text-decoration-none small">Home</a>
                    </li>

                    <li class="mb-2">
                        <a href="men.php" class="text-white text-decoration-none small">Men Collection</a>
                    </li>

                    <li>
                        <a href="women.php" class="text-white text-decoration-none small">Women Collection</a>
                    </li>

                </ul>

            </div>

            <!-- Contact -->

            <div class="col-md-4 mb-2 mb-md-0">

                <h5 class="mb-3 fs-6 fw-bold">Contact Us</h5>

                <p class="small text-secondary mb-2">
                    <i class="fa fa-envelope me-2"></i>
                    bushracreats@gmail.com
                </p>

                <div class="d-flex gap-3 mt-3">

                    <a href="#" class="text-white text-decoration-none">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>

                    <a href="#" class="text-white text-decoration-none">
                        <i class="fab fa-facebook fa-lg"></i>
                    </a>

                </div>

            </div>

        </div>

        <hr class="border-secondary my-4">

        <div class="text-center small text-secondary">
            &copy; 2026 MIRRORA | Designed by Bushra Khan
        </div>

    </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
<!-- Promo & Discount Popup Modal -->
<div class="modal fade" id="promoPopup" tabindex="-1" aria-labelledby="promoPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0 shadow-lg" style="background-color: #fff; overflow: hidden;">
            
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3 bg-white p-2 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            
            <div class="row g-0">
                <div class="col-12 p-4 p-md-5 text-center" style="background: linear-gradient(135deg, #4A0E17 0%, #2A080C 100%); color: #fff;">
                    <span class="badge bg-light text-dark px-3 py-1 rounded-pill text-uppercase fw-bold mb-3" style="font-size: 0.7rem; letter-spacing: 2px;">Limited Time Offer</span>
                    <h2 class="fw-bold mb-2" style="font-family: 'Cinzel', serif; font-size: 1.8rem; letter-spacing: 1px;">FLAT 20% OFF</h2>
                    <p class="text-white-50 small mb-4 px-2" style="font-size: 0.85rem;">Upgrade your wardrobe with our exclusive collection. Use code at checkout and enjoy special savings!</p>
                    
                    <!-- Promo Code Box -->
                    <div class="bg-white text-dark py-2 px-3 d-inline-block fw-bold rounded-0 mb-4 shadow-sm" style="letter-spacing: 2px; font-size: 0.95rem; border: 1px dashed #4A0E17;">
                        MIRRORA20
                    </div>

                    <div>
                        <a href="index.php" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-uppercase text-dark shadow-sm" style="font-size: 0.8rem; letter-spacing: 1px;" data-bs-dismiss="modal">
                            Shop Now <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- JavaScript: Sirf ek baar show hone ke liye -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Check karein ke kya popup pehle dikh chuka hai ya nahi
        if (!sessionStorage.getItem('popupShown')) {
            var myModal = new bootstrap.Modal(document.getElementById('promoPopup'));
            myModal.show();
            
            // Session mein save kar dein ke popup dikha diya gaya hai
            sessionStorage.setItem('popupShown', 'true');
        }
    });
   
document.addEventListener("DOMContentLoaded", function() {
    var myVideo = document.getElementById("bannerVideo");
    var myCarouselElem = document.getElementById("mainCarousel");
    
    if (myVideo && myCarouselElem) {
        // Carousel instance banayein (interval 3 seconds rakhein images ke liye)
        var myCarousel = new bootstrap.Carousel(myCarouselElem, {
            interval: 3000,
            wrap: true
        });
// Jab video bilkul aakhir tak poori chal jaye, tab hi carousel agli image par jaye aur images ko mazeed aage na chalaye
        myVideo.addEventListener('ended', function() {
            myCarousel.next();
            myCarousel.pause(); // Yahan timer rok diya taake video ke baad banner par ja kar slider ruk jaye
        });
        // Shuru mein carousel ko pause rakhein taake video aram se chal sake
        myCarousel.pause();

        // Jab video chal rahi ho, tab carousel hargiz na chale
        myVideo.addEventListener('play', function() {
            myCarousel.pause();
        });

        // Jab video bilkul aakhir tak poori chal jaye, tab hi carousel agli image par jaye
        myVideo.addEventListener('ended', function() {
            myCarousel.next();
            myCarousel.cycle(); // Ab images ke liye timer chala dein
        });

        // Jab wapas ghoom kar video wali slide par aaye, toh video shuru se play ho aur timer pause ho jaye
        myCarouselElem.addEventListener('slid.bs.carousel', function (e) {
            var activeItem = e.relatedTarget;
            var videoInSlide = activeItem.querySelector('video');
            if (videoInSlide) {
                videoInSlide.currentTime = 0;
                videoInSlide.play();
                myCarousel.pause();
            }
        });
    }
});

</script>
</html>

</footer>