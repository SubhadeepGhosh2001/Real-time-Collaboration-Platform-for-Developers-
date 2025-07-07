<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Sync - Real-time Collaboration Platform</title>
    <link rel="icon" type="image/png" href="favicon.ico">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: white;
            overflow-x: hidden;
            position: relative;
        }

        /* Enhanced Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(34, 197, 94, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(34, 197, 94, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(34, 197, 94, 0.1) 0%, transparent 50%);
            animation: floatingOrbs 6s ease-in-out infinite;
        }

        .animated-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, transparent 49%, rgba(34, 197, 94, 0.03) 50%, transparent 51%),
                linear-gradient(-45deg, transparent 49%, rgba(34, 197, 94, 0.03) 50%, transparent 51%);
            background-size: 30px 30px;
            animation: meshMove 4s linear infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes floatingOrbs {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes meshMove {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(30px) translateY(30px); }
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(34, 197, 94, 0.2);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #22c55e;
            text-decoration: none;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #cbd5e1;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem;
            border-radius: 25px;
        }

        .nav-links a:hover {
            color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
            transform: translateY(-2px);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            transition: all 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
            left: 0;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white !important;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
            background: linear-gradient(135deg, #ee5a52, #ff6b6b);
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
        }

        .hero {
            text-align: center;
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            animation: titleGlow 2s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            0% { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); }
            100% { text-shadow: 2px 2px 20px rgba(255, 255, 255, 0.5); }
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(34, 197, 94, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
        }

        /* Features Section */
        .features {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: white;
        }

        .feature-card p {
            color: #cbd5e1;
            line-height: 1.6;
        }

        /* Tech Stack Section */
        .techstack {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .techstack h2 {
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .tech-item {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(34, 197, 94, 0.2);
            transition: all 0.3s ease;
        }

        .tech-item:hover {
            transform: translateY(-5px);
            background: rgba(30, 41, 59, 0.7);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .tech-item h3 {
            color: #22c55e;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .tech-item p {
            color: #cbd5e1;
            font-size: 0.9rem;
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 3rem 2rem 1rem;
            color: white;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: #22c55e;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .footer-section p,
        .footer-section a {
            color: #cbd5e1;
            line-height: 1.6;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #22c55e;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(34, 197, 94, 0.2);
            color: #cbd5e1;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .social-links a {
            color: #cbd5e1;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.1);
        }

        .social-links a:hover {
            color: #22c55e;
            background: rgba(34, 197, 94, 0.2);
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .tech-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Typewriter cursor animation */
        .typewriter-cursor {
            display: inline-block;
            color: #22c55e;
            font-weight: bold;
            font-size: 3.5rem;
            animation: blink-cursor 0.8s steps(1) infinite;
            vertical-align: bottom;
        }
        @keyframes blink-cursor {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Fade-in animation for paragraph */
        .fade-in-text {
            opacity: 0;
            animation: fadeInText 2s ease 0.5s forwards;
        }
        @keyframes fadeInText {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>
    
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">Code Sync</a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#techstack">Tech Stack</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <section id="home" class="hero">
            <h1>
                <span class="typewriter-text">Real-time Collaboration Platform</span>
                <span class="typewriter-cursor">|</span>
            </h1>
            <p class="fade-in-text">Create, collaborate, and code together in real-time. Experience seamless development with integrated chat, version control, and live code editing.</p>
            <button class="cta-button" onclick="startCollaborating()">Start Collaborating</button>
        </section>

        <section id="features" class="features">
            <h2>Powerful Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Real-time Code Editing</h3>
                    <p>Edit code simultaneously with your team members and see changes instantly across all connected devices.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Integrated Chat</h3>
                    <p>Communicate seamlessly while coding with built-in chat functionality and instant notifications.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔄</div>
                    <h3>Version Control</h3>
                    <p>Full Git integration with branching, merging, and commit history tracking for better project management.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3>Code Review</h3>
                    <p>Comprehensive code review tools with commenting, suggestions, and approval workflows.</p>
                </div>
            </div>
        </section>

        <section id="techstack" class="techstack">
            <h2>Technology Stack</h2>
            <div class="tech-grid">
                <div class="tech-item">
                    <h3>Backend Services</h3>
                    <p>PHP for robust server-side processing, API development, and database management</p>
                </div>
                <div class="tech-item">
                    <h3>Real-time Communication</h3>
                    <p>WebSocket technology for instant, bidirectional communication between clients</p>
                </div>
                <div class="tech-item">
                    <h3>Frontend Interactivity</h3>
                    <p>JavaScript for dynamic UI enhancements and seamless user experience</p>
                </div>
                <div class="tech-item">
                    <h3>Version Control</h3>
                    <p>Git integration for comprehensive project versioning and collaboration</p>
                </div>
                <div class="tech-item">
                    <h3>Database Management</h3>
                    <p>MySQL/PostgreSQL for efficient data storage and retrieval</p>
                </div>
                <div class="tech-item">
                    <h3>Security & Authentication</h3>
                    <p>JWT tokens and OAuth for secure user authentication and authorization</p>
                </div>
            </div>
        </section>
    </div>

    <footer id="contact" class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Code Sync</h3>
                <p>Empowering developers worldwide with real-time collaboration tools. Build better software together.</p>
                <div class="social-links">
                    <a href="#" title="GitHub">🐙</a>
                    <a href="#" title="LinkedIn">💼</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="Discord">💬</a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Features</h3>
                <ul>
                    <li><a href="#">Real-time Editing</a></li>
                    <li><a href="#">Team Chat</a></li>
                    <li><a href="#">Version Control</a></li>
                    <li><a href="#">Code Review</a></li>
                    <li><a href="#">Project Management</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Resources</h3>
                <ul>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">API Reference</a></li>
                    <li><a href="#">Tutorials</a></li>
                    <li><a href="#">Community</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>📧 subhadeepghosh1270@gmail.com</p>
                <p>📞 +91 9564469300</p>
                <p>📍 Nabadiganta Abasan<br>Kolkata, WB 713166</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Code Sync. All rights reserved. | Privacy Policy | Terms of Service</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add active class to current section in navbar
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-links a');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });

        // CTA button functionality
        function startCollaborating() {
            if (window.confirm('Welcome to Code Sync! You are about to start a new collaboration session. Continue to Code Room?')) {
                window.location.href = 'http://localhost:5173';
            }
        }

        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.feature-card, .tech-item');
            
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                    }
                });
            });

            cards.forEach(card => {
                observer.observe(card);
            });
        });

        // Add CSS for fade in animation
        const style = document.createElement('style');
        style.textContent = `
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
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>