<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Astra Service Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a1628 0%, #0f1f35 100%);
            color: #e8f1f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        nav {
            background: rgba(15, 31, 53, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #1a2a42;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        nav .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0da6c7 0%, #00d4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            color: #a8b8c8;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: #0da6c7;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login {
            background: transparent;
            border: 1px solid #0da6c7;
            color: #0da6c7;
        }

        .btn-login:hover {
            background: #0da6c7;
            color: #0a1628;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0da6c7 0%, #00d4ff 100%);
            color: #0a1628;
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 166, 199, 0.4);
        }

        /* Hero Section */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
            background: linear-gradient(135deg, rgba(13, 166, 199, 0.05) 0%, rgba(0, 212, 255, 0.03) 100%);
        }

        .hero-content {
            max-width: 700px;
            animation: fadeInUp 0.8s ease;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #00d4ff 0%, #0da6c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            color: #a8b8c8;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 14px 35px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-primary-large {
            background: linear-gradient(135deg, #0da6c7 0%, #00d4ff 100%);
            color: #0a1628;
            border: none;
        }

        .btn-primary-large:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(13, 166, 199, 0.5);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid #0da6c7;
            color: #0da6c7;
        }

        .btn-secondary:hover {
            background: rgba(13, 166, 199, 0.1);
            transform: translateY(-3px);
        }

        /* Features Section */
        .features {
            padding: 80px 20px;
            background: linear-gradient(180deg, transparent 0%, rgba(13, 166, 199, 0.03) 100%);
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 60px;
            background: linear-gradient(135deg, #00d4ff 0%, #0da6c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: linear-gradient(135deg, #0f1f35 0%, #132a47 100%);
            border: 1px solid #1a2a42;
            padding: 40px 30px;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #0da6c7;
            box-shadow: 0 20px 40px rgba(13, 166, 199, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #00d4ff;
        }

        .feature-card p {
            color: #a8b8c8;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background: rgba(15, 31, 53, 0.95);
            border-top: 1px solid #1a2a42;
            padding: 40px 20px;
            text-align: center;
            color: #7a8fa3;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        footer p {
            margin: 10px 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn-large {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">✨ Astra Service</div>
            <ul class="nav-links">
                <li><a href="#features">Services</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-login">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Astra Service</h1>
            <p>Your one-stop solution for booking professional services anytime, anywhere. Book with confidence, serve with excellence.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-large btn-primary-large">Get Started</a>
                <a href="services.php" class="btn btn-large btn-secondary">Explore Services</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <h2 class="section-title">Why Choose Astra?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Quick Booking</h3>
                    <p>Book services in just a few clicks. Fast, simple, and hassle-free booking experience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Professional Service</h3>
                    <p>Connect with verified professionals who are ready to deliver quality services.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure & Safe</h3>
                    <p>Your data is protected with enterprise-grade security and privacy measures.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Affordable Prices</h3>
                    <p>Competitive pricing with transparent costs, no hidden charges.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📞</div>
                    <h3>24/7 Support</h3>
                    <p>Round-the-clock customer support to assist you whenever you need help.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h3>Ratings & Reviews</h3>
                    <p>Make informed decisions with detailed ratings and reviews from other customers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <h3 style="color: #00d4ff; margin-bottom: 15px;">Astra Service Booking</h3>
            <p>&copy; 2026 Astra Service. All rights reserved.</p>
            <p>Providing quality services at your fingertips</p>
        </div>
    </footer>
</body>
</html>
