<?php 
include 'header.php'; 
include 'config.php'; 

$status_msg = "";
if (isset($_POST['submit_contact'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO messages(name,email,message)
            VALUES('$name','$email','$message')";

    if(mysqli_query($conn,$sql))
    {
        $status_msg = "<div class='alert alert-success border-0 shadow-sm rounded-3'>
        <i class='fas fa-circle-check me-2'></i>
        Message sent successfully! Hamari team jald aapse contact karegi.
        </div>";
    }
    else
    {
        $status_msg = "<div class='alert alert-danger border-0 shadow-sm rounded-3'>
        Database Error : ".mysqli_error($conn)."
        </div>";
    }
}
?>

<!-- Minimalist Header Intro Section (Clean Red and White Gradient Matrix) -->
<div class="container-fluid py-4 py-md-5 text-center mb-4 px-3" style="background: radial-gradient(circle at 50% 50%, #fffcfc 0%, #fff5f5 100%); border-bottom: 1px solid #fce8e8;">
    <div class="py-2">
        <span class="badge px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm" style="background: linear-gradient(135deg, #722F37 0%, #d91b5c 100%); color: #fff; letter-spacing: 2px; font-size: 0.75rem;">GET IN TOUCH</span>
        <h1 class="display-5 display-md-4 fw-bold mb-2 fs-2 fs-md-1" style="font-family: 'Playfair Display', 'Cinzel', serif; color: #222; letter-spacing: -1px;">Contact Our Support</h1>
        <p class="lead text-muted mx-auto fs-6 fs-md-5" style="max-width: 600px; line-height: 1.6;">Koi query ho ya AI Try-on engine ke mutabiq help chahiye? We are here to assist you 24/7.</p>
    </div>
</div>

<div class="container my-4 my-md-5 px-3">
    <div class="row g-4 g-lg-5">
        
        <!-- LEFT COLUMN: PREMIUM WHITE & RED FORM FIELD MATRIX -->
        <div class="col-lg-7">
            <div class="p-3 p-md-5 rounded-4 shadow-lg border-0 position-relative overflow-hidden bg-white text-dark" style="box-shadow: 0 15px 45px rgba(114, 47, 37, 0.06) !important; border: 1px solid #f5e6e6 !important;">
                <!-- Glowing Primary Red Accent Line -->
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #722F37, #b8323d, #d91b5c);"></div>
                
                <h4 class="fw-bold mb-2 fs-4" style="font-family: 'Playfair Display', serif; letter-spacing: -0.5px; color: #222;">Humein Message Bheinjein</h4>
                <p class="text-muted small mb-4">Apna real name aur dynamic active working email use karein fast verification ke liye.</p>
                
                <?php echo $status_msg; ?>

                <form action="contact.php" method="POST">
                    <!-- Form Input: Name -->
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control custom-red-input" id="floatingName" placeholder="Enter your full name" required>
                        <label for="floatingName" class="custom-label">Aapka Naam</label>
                    </div>
                    
                    <!-- Form Input: Email -->
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control custom-red-input" id="floatingEmail" placeholder="name@example.com" required>
                        <label for="floatingEmail" class="custom-label">Email Address</label>
                    </div>
                    
                    <!-- Form Input: Message -->
                    <div class="form-floating mb-4">
                        <textarea name="message" class="form-control custom-red-input" id="floatingMessage" placeholder="Type your message here..." required style="height: 140px;"></textarea>
                        <label for="floatingMessage" class="custom-label">Aapka Message / Query</label>
                    </div>
                    
                    <!-- Red Matte Interactive Animated Button -->
                    <button type="submit" name="submit_contact" class="btn text-white rounded-pill px-4 py-3 fw-bold w-100 border-0 custom-submit-btn text-uppercase" style="font-size: 0.9rem;">
                        Send Message <i class="fas fa-paper-plane ms-2 small"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- RIGHT COLUMN: RED CORE DETAIL INFORMATION HOVER TILES -->
        <div class="col-lg-5">
            <div class="h-100 d-flex flex-column justify-content-between gap-3 gap-lg-0">
                
                <!-- Card 1: Live Server Status (Soft Premium Dark Crimson Contrast) -->
                <div class="p-4 rounded-4 shadow-sm text-white mb-lg-3" style="background: linear-gradient(135deg, #1f0b0d 0%, #381115 100%); border: 1px solid rgba(114, 47, 55, 0.2);">
                    <div class="d-flex align-items-center mb-2">
                        <span class="spinner-grow text-success spinner-grow-sm me-2" role="status"></span>
                        <h6 class="fw-bold mb-0 text-warning" style="letter-spacing: 1px; font-size: 0.85rem;">AI SERVERS ONLINE</h6>
                    </div>
                    <p class="small text-light opacity-75 mb-0" style="font-size: 0.85rem; line-height: 1.5;">Hamare virtual dressing room system active hain. Response and processing time under 2 minutes hai.</p>
                </div>
                
                <!-- Card 2: Contact Methods Hub (Clickable Details) -->
                <div class="p-4 rounded-4 shadow-sm bg-white border-0" style="border: 1px solid #f5e6e6 !important; box-shadow: 0 10px 30px rgba(114, 47, 37, 0.04) !important;">
                    <h5 class="fw-bold mb-4 text-dark opacity-75" style="font-family: 'Playfair Display', serif; font-size: 1.15rem;">Direct Hub Details</h5>
                    
                    <!-- Official Email (Clickable Mailto Link) -->
                    <a href="mailto:bushracreats@gmail.com" class="d-flex align-items-center mb-3 text-decoration-none contact-card-hover p-2 rounded-3 transition">
                        <div class="p-2.5 rounded-3 me-3 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: #722F37; width: 40px; height: 40px; box-shadow: 0 4px 10px rgba(114, 47, 55, 0.25);"><i class="fas fa-envelope fa-fw"></i></div>
                        <div>
                            <small class="d-block text-muted fw-bold" style="font-size: 0.75rem;">Official Email</small>
                            <span class="text-dark fw-medium small">bushracreats@gmail.com</span>
                        </div>
                    </a>
                    
                    <!-- Helpline / WhatsApp (Clickable) -->
                    <a href="https://wa.me/923101205366" target="_blank" class="d-flex align-items-center mb-3 text-decoration-none contact-card-hover p-2 rounded-3 transition">
                        <div class="p-2.5 rounded-3 me-3 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: #b8323d; width: 40px; height: 40px; box-shadow: 0 4px 10px rgba(184, 50, 61, 0.25);"><i class="fab fa-whatsapp fa-fw"></i></div>
                        <div>
                            <small class="d-block text-muted fw-bold" style="font-size: 0.75rem;">Helpline / WhatsApp</small>
                            <span class="text-dark fw-medium small">0310 1205366</span>
                        </div>
                    </a>

                    <!-- LinkedIn Profile (Clickable) -->
                    <a href="https://www.linkedin.com/in/bushra-creates/" target="_blank" class="d-flex align-items-center mb-3 text-decoration-none contact-card-hover p-2 rounded-3 transition">
                        <div class="p-2.5 rounded-3 me-3 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: #0077b5; width: 40px; height: 40px; box-shadow: 0 4px 10px rgba(0, 119, 181, 0.25);"><i class="fab fa-linkedin-in fa-fw"></i></div>
                        <div>
                            <small class="d-block text-muted fw-bold" style="font-size: 0.75rem;">Professional Profile</small>
                            <span class="text-dark fw-medium small">linkedin.com/in/bushra-creates</span>
                        </div>
                    </a>
                    
                    <!-- Headquarters (Clickable Google Maps link) -->
                    <a href="https://maps.google.com/?q=Gulzar-e-Hijri,+Scheme+33,+Karachi" target="_blank" class="d-flex align-items-center text-decoration-none contact-card-hover p-2 rounded-3 transition">
                        <div class="p-2.5 rounded-3 me-3 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: #222; width: 40px; height: 40px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);"><i class="fas fa-location-dot fa-fw"></i></div>
                        <div>
                            <small class="d-block text-muted fw-bold" style="font-size: 0.75rem;">Headquarters</small>
                            <span class="text-dark fw-medium small">Gulzar-e-Hijri, Scheme 33, Karachi, Pakistan</span>
                        </div>
                    </a>
                </div>

            </div>
        </div>

    </div>

    <!-- CERTIFIED GOOGLE MAP ENGINE FRAME WITH REAL KARACHI COORDINATES DATA -->
    <div class="row mt-5 pt-3">
        <div class="col-12">
            <h4 class="fw-bold mb-4 text-center fs-4" style="font-family: 'Playfair Display', serif; color: #222;">Visit Our Corporate Office</h4>
            <div class="rounded-4 overflow-hidden border shadow-sm" style="height: 350px; border-color: #f0dbdb !important;">
                <iframe 
                    src="https://maps.google.com/maps?q=Gulzar-e-Hijri,%20Scheme%2033,%20Karachi&t=&z=13&ie=UTF8&iwloc=&output=embed" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- TARGETED RED AND WHITE STYLESHEET REGULATORY INJECTS -->
<style>
    .custom-red-input {
        background-color: #ffffff !important;
        border: 1px solid #e2c4c4 !important;
        color: #222222 !important;
        border-radius: 12px !important;
        transition: all 0.3s ease-in-out !important;
    }
    .custom-red-input:focus {
        background-color: #fffdfd !important;
        border-color: #722F37 !important;
        box-shadow: 0 0 10px rgba(114, 47, 55, 0.15) !important;
    }
    .custom-label {
        color: #777777 !important;
    }
    .custom-red-input:focus ~ .custom-label,
    .form-floating > .form-control:not(:placeholder-shown) ~ .custom-label {
        color: #722F37 !important;
    }
    .custom-submit-btn {
        background: linear-gradient(135deg, #722F37 0%, #b8323d 100%);
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .custom-submit-btn:hover {
        opacity: 0.95;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(114, 47, 55, 0.3);
    }
    .contact-card-hover {
        transition: all 0.25s ease-in-out;
    }
    .contact-card-hover:hover {
        background-color: #fff5f5 !important;
        transform: translateX(4px);
    }
</style>
<?php 
include 'footer.php'; 
?>