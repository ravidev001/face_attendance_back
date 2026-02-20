<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketApp | Welcome</title>
    <style>
        /* Modern Reset & Google Font */
        @import url('https://fonts.googleapis.com');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
        }

        .container {
            max-width: 800px;
            animation: fadeIn 1.2s ease-out;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: white;
            color: #6366f1;
        }

        .btn-primary:hover {
            background-color: #f1f5f9;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
        }

        .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Simple Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 600px) {
            h1 { font-size: 2.5rem; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>

    <section class="hero">
        <div class="container">
            <h1>FaceApp</h1>
            <p>The smartest way to manage your marketplace. Beautifully designed, built for performance, and ready for your next big project.</p>
            
            <div class="btn-group">
                <a href="#" class="btn btn-primary">Get Started</a>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

</body>
</html>
