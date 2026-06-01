<?php
session_start();
require_once '../../db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Blossomly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Inline fallback — guarantees styles apply */
        body {
            font-family: 'Lato', sans-serif;
            background-color: #fdf6f0;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .contact-main {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 0 2rem 4rem;
        }

        .page-hero {
            text-align: center;
            padding: 3rem 2rem 2rem;
            background: linear-gradient(to bottom, #FCE4EC, transparent);
            margin: 0 -2rem 3rem;
        }

        .page-hero h1 {
            font-size: 2.8rem;
            color: #3E2723;
            margin-bottom: 0.8rem;
            font-family: 'Playfair Display', serif;
        }

        .page-hero p {
            color: #8D6E63;
            font-size: 1rem;
            max-width: 480px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .contact-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-areas:
                "info form"
                "map  map";
            gap: 2rem;
            align-items: start;
        }

        .contact-info {
            grid-area: info;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #FFCDD2;
            border-radius: 12px;
            padding: 1.4rem 1.6rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .info-card:hover {
            box-shadow: 0 4px 16px rgba(78,52,46,0.1);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background-color: #FCE4EC;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon svg {
            width: 20px;
            height: 20px;
            fill: #558B2F;
        }

        .info-card h3 {
            font-size: 0.95rem;
            color: #3E2723;
            margin-bottom: 0.3rem;
            font-weight: 700;
        }

        .info-card p {
            font-size: 0.88rem;
            color: #8D6E63;
            line-height: 1.6;
            margin: 0;
        }

        .form-wrapper {
            grid-area: form;
            background: #ffffff;
            border: 1px solid #FFCDD2;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 2px 12px rgba(78,52,46,0.07);
        }

        .form-wrapper h2 {
            font-size: 1.6rem;
            color: #3E2723;
            margin-bottom: 1.8rem;
            font-family: 'Playfair Display', serif;
        }

        .form-group {
            margin-bottom: 1.3rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #4E342E;
            margin-bottom: 0.4rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e8d5d0;
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
            font-size: 0.9rem;
            color: #4E342E;
            background-color: #fdf9f8;
            outline: none;
            resize: none;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #A5D6A7;
            box-shadow: 0 0 0 3px rgba(165,214,167,0.25);
        }

        .submit-btn {
            width: 100%;
            padding: 0.9rem;
            background-color: #A5D6A7;
            color: #3E2723;
            font-family: 'Lato', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        .submit-btn:hover {
            background-color: #81C784;
        }

        .success-msg {
            display: none;
            margin-top: 1rem;
            padding: 0.75rem 1rem;
            background-color: #e8f5e9;
            color: #558B2F;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            text-align: center;
            border: 1px solid #A5D6A7;
        }

        .map-wrapper {
            grid-area: map;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
        }

        .map-wrapper iframe {
            display: block;
            width: 100%;
            height: 450px;
            border: 0;
        }

        .site-footer {
            background-color: #3E2723;
            color: white;
            text-align: center;
            padding: 25px;
            font-size: 0.9rem;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .contact-section {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "info"
                    "form"
                    "map";
            }
        }
    </style>
</head>
<body>
<!-- Sharifah Alyousef -->

    <?php include '../../includes/header.php'; ?>

    <main class="contact-main">

        <section class="page-hero">
            <h1>Get In Touch</h1>
            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </section>

        <section class="contact-section">

            <!-- 1. Info Cards -->
            <div class="contact-info">
                <div class="info-card">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                    </div>
                    <div>
                        <h3>Our Location</h3>
                        <p>Al Khobar, Eastern Province<br>Saudi Arabia</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    </div>
                    <div>
                        <h3>Phone Number</h3>
                        <p>+966 13 000 0000<br>Sun – Thu, 9AM – 6PM</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    </div>
                    <div>
                        <h3>Email Us</h3>
                        <p>hello@blossomly.sa<br>support@blossomly.sa</p>
                    </div>
                </div>
            </div>

            <!-- 2. Form -->
            <div class="form-wrapper">
                <h2>Send a Message</h2>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" placeholder="Your full name">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" placeholder="How can we help?">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" rows="5" placeholder="Write your message here..."></textarea>
                </div>
                <button class="submit-btn" onclick="handleSubmit()">Send Message</button>
                <div class="success-msg" id="successMsg">✓ Your message has been sent successfully!</div>
            </div>

            <!-- 3. Map -->
            <div class="map-wrapper">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3577.4!2d50.2!3d26.28!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sAl+Khobar%2C+Saudi+Arabia!5e0!3m2!1sen!2ssa!4v1"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

        </section>
    </main>

    <?php include '../../includes/footer.html'; ?>

    <script>
        function handleSubmit() {
            const name    = document.getElementById('name').value;
            const email   = document.getElementById('email').value;
            const message = document.getElementById('message').value;

            if (!name || !email || !message) {
                alert('Please fill in all required fields.');
                return;
            }

            document.getElementById('successMsg').style.display = 'block';
            document.getElementById('name').value    = '';
            document.getElementById('email').value   = '';
            document.getElementById('subject').value = '';
            document.getElementById('message').value = '';

            setTimeout(() => {
                document.getElementById('successMsg').style.display = 'none';
            }, 4000);
        }
    </script>

</body>
</html>