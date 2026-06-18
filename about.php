<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Astra Service Booking</title>
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

        /* Breadcrumb */
        .breadcrumb {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .breadcrumb a {
            color: #0da6c7;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Hero Section */
        .hero {
            padding: 80px 20px;
            text-align: center;
            background: linear-gradient(135deg, rgba(13, 166, 199, 0.05) 0%, rgba(0, 212, 255, 0.03) 100%);
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #00d4ff 0%, #0da6c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.1rem;
            color: #a8b8c8;
            line-height: 1.6;
        }

        /* Content Section */
        .content-section {
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .section-title {
            font-size: 2.2rem;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #00d4ff 0%, #0da6c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            margin-bottom: 80px;
        }

        .two-column-text h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #00d4ff;
        }

        .two-column-text p {
            color: #a8b8c8;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .two-column-image {
            background: linear-gradient(135deg, #0f1f35 0%, #132a47 100%);
            padding: 40px;
            border-radius: 12px;
            border: 1px solid #1a2a42;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }

        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #0f1f35 0%, #132a47 100%);
            border: 1px solid #1a2a42;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #0da6c7;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(13, 166, 199, 0.15);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0da6c7;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #a8b8c8;
            font-size: 0.95rem;
        }

        /* Values Section */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .value-card {
            background: linear-gradient(135deg, #0f1f35 0%, #132a47 100%);
            border: 1px solid #1a2a42;
            padding: 35px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-8px);
            border-color: #0da6c7;
            box-shadow: 0 20px 40px rgba(13, 166, 199, 0.2);
        }

        .value-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .value-card h3 {
            font-size: 1.2rem;
            color: #00d4ff;
            margin-bottom: 12px;
        }

        .value-card p {
            color: #a8b8c8;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 35px;
            margin: 40px 0;
        }

        .team-member {
            background: linear-gradient(135deg, #0f1f35 0%, #132a47 100%);
            border: 1px solid #1a2a42;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-10px);
            border-color: #0da6c7;
            box-shadow: 0 20px 40px rgba(13, 166, 199, 0.2);
        }

        .member-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, rgba(13, 166, 199, 0.1), rgba(0, 212, 255, 0.05));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            border-bottom: 1px solid #1a2a42;
        }

        .member-info {
            padding: 25px;
        }

        .member-name {
            font-size: 1.2rem;
            color: #00d4ff;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .member-role {
            color: #7a8fa3;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .member-bio {
            color: #a8b8c8;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(13, 166, 199, 0.1) 0%, rgba(0, 212, 255, 0.05) 100%);
            padding: 60px 20px;
            text-align: center;
            margin: 60px 0 0 0;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #00d4ff 0%, #0da6c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta-content p {
            color: #a8b8c8;
            margin-bottom: 30px;
            font-size: 1.05rem;
        }

        .cta-btn {
            padding: 14px 40px;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #0da6c7 0%, #00d4ff 100%);
            color: #0a1628;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(13, 166, 199, 0.5);
        }

        /* Footer */
        footer {
            background: rgba(15, 31, 53, 0.95);
            border-top: 1px solid #1a2a42;
            padding: 40px 20px;
            text-align: center;
            color: #7a8fa3;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        footer p {
            margin: 10px 0;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .two-column {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .team-grid {
                grid-template-columns: 1fr;
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
                <li><a href="welcome.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="services.php">Services</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-login">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="welcome.php">Home</a> / <span style="color: #a8b8c8;">About Us</span>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>About Astra Service</h1>
            <p>Transforming the service industry with innovation, reliability, and customer-first solutions since 2020.</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content-section">
        <!-- Our Story -->
        <div class="two-column">
            <div class="two-column-text">
                <h3>Our Story</h3>
                <p>Astra Service started with a simple vision: to make professional services accessible to everyone. Founded in 2020, we recognized a gap in the market for a reliable, user-friendly platform that connects service seekers with qualified professionals.</p>
                <p>What began as a small startup has grown into a trusted platform serving thousands of customers across the region. We've remained committed to our core mission of delivering quality services at affordable prices.</p>
            </div>
            <div class="two-column-image">📖</div>
        </div>

        <!-- Our Mission & Vision -->
        <div class="two-column" style="direction: rtl;">
            <div class="two-column-image" style="direction: ltr;">🎯</div>
            <div class="two-column-text" style="direction: ltr;">
                <h3>Mission & Vision</h3>
                <p><strong>Mission:</strong> To empower service professionals and customers through a seamless, secure, and innovative booking platform that builds trust and delivers excellence.</p>
                <p><strong>Vision:</strong> To become the most trusted and widely-used service booking platform in the region, setting new standards for customer service and professional excellence.</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">50K+</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">5K+</div>
                <div class="stat-label">Service Providers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">100K+</div>
                <div class="stat-label">Bookings Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">4.8★</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>

        <!-- Our Values -->
        <h2 class="section-title">Our Core Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Trust</h3>
                <p>We build lasting relationships based on transparency, honesty, and reliability. Your trust is our most valuable asset.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">✨</div>
                <h3>Excellence</h3>
                <p>We maintain the highest standards in everything we do, from our platform to our customer service.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🌍</div>
                <h3>Accessibility</h3>
                <p>Making quality services available to everyone, regardless of their location or circumstances.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">💡</div>
                <h3>Innovation</h3>
                <p>Continuously improving our platform with cutting-edge technology and user-centric features.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🛡️</div>
                <h3>Security</h3>
                <p>Protecting your data and privacy with enterprise-grade security measures.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">❤️</div>
                <h3>Customer Care</h3>
                <p>Your satisfaction is our priority. We're here to support you 24/7.</p>
            </div>
        </div>

        <!-- Leadership Team -->
        <h2 class="section-title" style="margin-top: 80px;">Our Leadership Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-image">👨‍💼</div>
                <div class="member-info">
                    <div class="member-name">Rajesh Kumar</div>
                    <div class="member-role">Founder & CEO</div>
                    <div class="member-bio">Visionary leader with 15+ years of experience in service industry innovation.</div>
                </div>
            </div>
            <div class="team-member">
                <div class="member-image">👩‍💻</div>
                <div class="member-info">
                    <div class="member-name">Priya Sharma</div>
                    <div class="member-role">CTO</div>
                    <div class="member-bio">Technical expert specializing in scalable platform architecture and security.</div>
                </div>
            </div>
            <div class="team-member">
                <div class="member-image">👨‍💼</div>
                <div class="member-info">
                    <div class="member-name">Amit Patel</div>
                    <div class="member-role">COO</div>
                    <div class="member-bio">Operations specialist ensuring seamless service delivery and customer satisfaction.</div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us -->
        <h2 class="section-title" style="margin-top: 80px;">Why Choose Astra?</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">⚡</div>
                <h3>Speed & Efficiency</h3>
                <p>Fast booking process with instant confirmations and real-time updates on your service requests.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">💰</div>
                <h3>Best Prices</h3>
                <p>Competitive rates with no hidden charges. See exactly what you'll pay before confirming.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">✅</div>
                <h3>Verified Professionals</h3>
                <p>All service providers are vetted and verified for quality and reliability.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">📱</div>
                <h3>User-Friendly App</h3>
                <p>Intuitive interface designed for maximum convenience and ease of use.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🏆</div>
                <h3>Quality Guarantee</h3>
                <p>We stand behind our services with a satisfaction guarantee.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">📊</div>
                <h3>Transparent Ratings</h3>
                <p>Honest reviews and ratings to help you make informed decisions.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Ready to Experience Astra?</h2>
            <p>Join thousands of satisfied customers who trust us with their service needs.</p>
            <a href="register.php" class="cta-btn">Get Started Today</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <h3 style="color: #00d4ff; margin-bottom: 15px;">Astra Service Booking</h3>
            <p>&copy; 2026 Astra Service. All rights reserved.</p>
            <p>Building trust, one service at a time</p>
        </div>
    </footer>
</body>
</html>
