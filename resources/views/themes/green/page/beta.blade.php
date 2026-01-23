<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ApnaCrowdfunding Beta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            overflow: hidden;
        }

        /* MAIN BACKGROUND - Added a radial gradient to mimic the light effect */
        .beta-wrapper {
            height: 100vh;
            background:url('{{ asset('betatest/Background.png') }}');
            /* If you have the specific image: background: url('{{ asset('betatest/Background.png') }}') no-repeat center center/cover; */
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-size: cover;
        }

        /* CENTER CONTENT */
        .beta-content {
            text-align: center;
            padding: 0 20px;
            z-index: 1;
        }

        /* LOGO */
        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            height: 80px; /* Adjusted to match scale */
            object-fit: contain;
        }

        /* MAIN HEADING */
        .beta-content h1 {
            font-size: 72px; /* Large, bold heading like the image */
            font-weight: 700;
            color: #1b7f46;
            line-height: 1.1;
            margin-bottom: 30px;
        }

        /* SUBTEXT */
        .beta-content p {
            font-size: 22px;
            color: #1b7f46;
            max-width: 800px;
            margin: 0 auto 40px auto;
            line-height: 1.4;
            font-style: italic; /* The reference has a slight slant/softness */
        }

        /* BUTTONS */
        .btn-container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .btn {
            background: #1b7f46;
            color: #fff;
            text-decoration: none;
            padding: 15px 35px;
            font-size: 24px;
            font-weight: 600;
            border-radius: 12px;
            transition: transform 0.2s, background 0.3s;
        }

        .btn:hover {
            background: #146437;
            transform: translateY(-2px);
        }

        /* BETA RIBBON - Precision positioning for the corner */
        .beta-ribbon {
            position: absolute;
            top: 40px;
            right: -60px;
            background: #ff0000;
            color: #fff;
            font-weight: 800;
            padding: 10px 100px;
            transform: rotate(45deg);
            text-align: center;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-transform: uppercase;
            line-height: 1.1;
        }

        /* RESPONSIVE */
        @media(max-width: 768px){
            .beta-content h1 { font-size: 40px; }
            .beta-content p { font-size: 18px; }
            .btn { font-size: 18px; padding: 12px 24px; }
            .beta-ribbon { font-size: 14px; right: -80px; }
        }
    </style>
</head>
<body>

<div class="beta-wrapper">

    <div class="beta-ribbon">
        BETA<br>VERSION
    </div>

    <div class="beta-content">

        <div class="logo">
            <img src="{{ asset('betatest/Logo.png') }}" alt="ApnaCrowdfunding Logo">
        </div>

        <h1>Help Us Shape the Future<br> of Crowdfunding</h1>

        <p>ApnaCrowdfunding is currently in Beta. Your feedback helps us build a better platform for creators and contributors</p>

        <div class="btn-container">
            <form method="POST" action="{{ route('beta.accept') }}">
                @csrf
                <button type="submit" class="btn">Start Using</button>
            </form>
            <a
                href="https://wa.me/14084122162?text=Hi%20ApnaCrowdfunding%2C%20I%20want%20to%20share%20beta%20feedback."
                target="_blank"
                rel="noopener"
                class="btn"
            >
                Share Feedback
            </a>
        </div>

    </div>

</div>

</body>
</html>