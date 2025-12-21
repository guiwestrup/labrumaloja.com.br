<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- SEO Meta Tags -->
    <title>La Bruma - Biquínis, Moda Praia e Lingerie de Alta Qualidade | Em Breve</title>
    <meta name="description" content="La Bruma - Lingerie & Alma do Mar. Em breve, nossa loja online com biquínis, moda praia e lingerie de alta qualidade. Siga-nos no Instagram e entre em contato pelo WhatsApp.">
    <meta name="keywords" content="biquínis, moda praia, lingerie, alta qualidade, La Bruma, biquíni, maiô, moda feminina, praia, verão, roupa íntima">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="La Bruma - Biquínis, Moda Praia e Lingerie de Alta Qualidade">
    <meta property="og:description" content="Lingerie & Alma do Mar. Em breve, nossa loja online com biquínis, moda praia e lingerie de alta qualidade.">
    <meta property="og:image" content="{{ url('/loja-frente.jpeg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="La Bruma - Loja Física - Lingerie & Alma do Mar">
    <meta property="og:site_name" content="La Bruma">
    <meta property="og:locale" content="pt_BR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="La Bruma - Biquínis, Moda Praia e Lingerie de Alta Qualidade">
    <meta name="twitter:description" content="Lingerie & Alma do Mar. Em breve, nossa loja online com biquínis, moda praia e lingerie de alta qualidade.">
    <meta name="twitter:image" content="{{ url('/loja-frente.jpeg') }}">
    <meta name="twitter:image:alt" content="La Bruma - Loja Física - Lingerie & Alma do Mar">
    
    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#f5f1e8">
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('/labruma-sereia-white-removebg-preview.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/labruma-sereia-white-removebg-preview.ico') }}">
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YEZ7E5VTHN"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-YEZ7E5VTHN');
    </script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f1e8 0%, #e8e3d6 50%, #d4cbb8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Efeito de ondas de fundo suaves */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.2) 0%, transparent 50%);
            pointer-events: none;
        }

        .container {
            text-align: center;
            z-index: 1;
            max-width: 600px;
            width: 100%;
        }

        .logo-container {
            margin-bottom: 40px;
            animation: fadeInUp 1s ease-out;
        }

        .logo {
            max-width: 300px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.1));
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 300;
            color: #8b7355;
            margin-bottom: 20px;
            letter-spacing: 2px;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .subtitle {
            font-size: 1.2rem;
            color: #6b5d4a;
            margin-bottom: 50px;
            font-weight: 300;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 60px;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .social-link {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.95);
        }

        .social-link svg {
            width: 28px;
            height: 28px;
            fill: #8b7355;
            transition: fill 0.3s ease;
        }

        .social-link:hover svg {
            fill: #6b5d4a;
        }

        /* Instagram icon */
        .social-link.instagram:hover svg {
            fill: #E4405F;
        }

        /* WhatsApp icon */
        .social-link.whatsapp:hover svg {
            fill: #25D366;
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
            h1 {
                font-size: 2rem;
            }

            .subtitle {
                font-size: 1rem;
                margin-bottom: 40px;
            }

            .logo {
                max-width: 250px;
            }

            .social-links {
                gap: 20px;
                margin-top: 50px;
            }

            .social-link {
                width: 50px;
                height: 50px;
            }

            .social-link svg {
                width: 24px;
                height: 24px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
                letter-spacing: 1px;
            }

            .subtitle {
                font-size: 0.9rem;
            }

            .logo {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img 
                src="https://cdn.wesdev.com.br/labruma/assets/labruma-sereia-white-removebg-preview.png" 
                alt="La Bruma Logo" 
                class="logo"
            >
        </div>

        <h1>LA BRUMA</h1>
        <p class="subtitle">LINGERIE & ALMA DO MAR</p>
        <p class="subtitle" style="margin-top: 10px; font-size: 1rem;">Em breve, nossa loja estará no ar</p>

        <div class="social-links">
            <a 
                href="https://instagram.com/labruma.laguna" 
                target="_blank" 
                rel="noopener noreferrer"
                class="social-link instagram"
                aria-label="Siga-nos no Instagram"
            >
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </a>

            <a 
                href="https://api.whatsapp.com/send/?phone=48996242686&text&type=phone_number&app_absent=0" 
                target="_blank" 
                rel="noopener noreferrer"
                class="social-link whatsapp"
                aria-label="Fale conosco no WhatsApp"
            >
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </a>
        </div>
    </div>
</body>
</html>
