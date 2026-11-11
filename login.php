<?php
require_once __DIR__ . '/config/database.php';
session_start();

$pdo = getPDO();
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $username = trim((string)($data['username'] ?? ''));
    $password = (string)($data['password'] ?? '');

    if ($username === '' || $password === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Nom d’utilisateur et mot de passe requis.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            echo json_encode(['success' => true]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Identifiants invalides.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
    }
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | Atelier</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap');
        :root {
            color-scheme: light;
            --ink: #1d2a25;
            --muted: #697972;
            --paper: #f5f6f1;
            --white: #fff;
            --green: #175c4b;
            --green-dark: #174a3e;
            --green-pale: #e7eee7;
            --line: #dde4df;
            --shadow: 0 24px 60px rgba(25, 45, 37, .12);
            --error: #a43d2f;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f2ed 0%, #f8f9f5 100%);
            color: var(--ink);
            font: 15px/1.6 'DM Sans', sans-serif;
        }
        a { color: inherit; }
        .page-shell { min-height: 100vh; display: grid; grid-template-columns: 1.08fr .92fr; }
        .story {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px clamp(30px, 5vw, 88px);
            background: linear-gradient(160deg, var(--green-dark) 0%, #1c5a4d 100%);
            color: white;
        }
        .story::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.18;
            background: repeating-linear-gradient(135deg, transparent 0 28px, rgba(255,255,255,0.18) 29px 30px);
            mask-image: linear-gradient(140deg, black 20%, transparent 85%);
        }
        .brand, .story-copy, .story-foot { position: relative; z-index: 1; }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            width: fit-content;
            font: 800 14px Manrope, sans-serif;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .brand-mark {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 50%;
            color: #d9ead9;
        }
        .story-copy { max-width: 500px; padding: 48px 0 26px; }
        .eyebrow {
            color: #d6e5d8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        h1 {
            margin: 18px 0 16px;
            font: 800 clamp(42px, 4.5vw, 68px)/1.04 Manrope, sans-serif;
            letter-spacing: -.06em;
        }
        .story-copy p {
            max-width: 390px;
            color: rgba(255,255,255,0.8);
            font-size: 17px;
            line-height: 1.7;
        }
        .story-foot {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .form-side {
            display: grid;
            place-items: center;
            padding: 30px 28px;
        }
        .form-wrap {
            width: min(100%, 420px);
            padding: 30px 28px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255,255,255,0.68);
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
            animation: arrive .55s ease both;
        }
        @keyframes arrive { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
        .mobile-brand { display: none; }
        .form-wrap h2 {
            margin: 0 0 12px;
            font: 800 34px Manrope, sans-serif;
            letter-spacing: -.04em;
        }
        .intro {
            margin: 0 0 26px;
            color: var(--muted);
            line-height: 1.6;
        }
        .field { margin-top: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
        }
        input {
            width: 100%;
            height: 50px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--white);
            color: var(--ink);
            font: inherit;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        input:focus {
            outline: none;
            border-color: #8bb0a0;
            box-shadow: 0 0 0 4px rgba(23,92,75,.12);
        }
        .field-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; margin-bottom: 8px; }
        .remember { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-size: 12px; }
        .remember input { width: auto; height: auto; margin: 0; }
        .link-mini {
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }
        .link-mini:hover { color: var(--green-dark); }
        .submit-btn {
            width: 100%;
            min-height: 52px;
            margin-top: 24px;
            border: 0;
            border-radius: 6px;
            background: linear-gradient(180deg, var(--green) 0%, var(--green-dark) 100%);
            color: #fff;
            font: 700 14px 'DM Sans', sans-serif;
            letter-spacing: .02em;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
            box-shadow: 0 12px 22px rgba(23,92,75,.18);
        }
        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(23,92,75,.22);
        }
        .message {
            min-height: 22px;
            margin-top: 14px;
            color: var(--error);
            font-size: 13px;
            line-height: 1.4;
        }
        .helper-text {
            margin-top: 22px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }
        .helper-text a { color: var(--green); font-weight: 700; text-decoration: none; }
        @media (max-width: 820px) {
            .page-shell { grid-template-columns: 1fr; }
            .story { min-height: 280px; padding-bottom: 26px; }
            .story-copy { padding-top: 30px; }
            .story-copy p { max-width: 500px; }
            .story-foot { display: none; }
        }
        @media (max-width: 520px) {
            .form-side { padding: 22px 18px 28px; }
            .form-wrap { padding: 24px 18px; }
            .mobile-brand {
                display: block;
                margin-bottom: 12px;
                color: var(--green);
                font: 800 15px Manrope, sans-serif;
                letter-spacing: .06em;
                text-transform: uppercase;
            }
            .brand { display: none; }
            .story-copy { padding-top: 18px; }
            h1 { font-size: clamp(36px, 11vw, 52px); }
            .story-copy p { font-size: 15px; }
            .field-row { flex-direction: column; align-items: flex-start; }
            .link-mini { align-self: flex-end; }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <aside class="story">
            <div class="brand"><span class="brand-mark">G</span> Garage / Atelier</div>
            <div class="story-copy">
                <div class="eyebrow">Gestion d’atelier</div>
                <h1>Tout l’atelier.<br>Au même endroit.</h1>
                <p>Clients, véhicules, mécaniciens et interventions réunis dans un espace de travail clair et sécurisé.</p>
            </div>
            <div class="story-foot">Espace de gestion sécurisé</div>
        </aside>
        <main class="form-side">
            <div class="form-wrap">
                <div class="mobile-brand">Garage / Atelier</div>
                <h2>Bon retour</h2>
                <p class="intro">Connectez-vous pour retrouver votre espace de gestion.</p>
                <form id="login-form">
                    <div class="field">
                        <label for="username">Nom d’utilisateur</label>
                        <input id="username" name="username" autocomplete="username" required maxlength="80" placeholder="Entrez votre nom d’utilisateur">
                    </div>
                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Entrez votre mot de passe">
                    </div>
                    <div class="field-row">
                        <label class="remember"><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
                        <a class="link-mini" href="#">Mot de passe oublié ?</a>
                    </div>
                    <button class="submit-btn" type="submit">Se connecter</button>
                    <div class="message" id="message" role="alert"></div>
                </form>
                <div class="helper-text">Pas encore accès ? <a href="index.php">Retour à l’accueil</a></div>
            </div>
        </main>
    </div>
    <script>
        const form = document.querySelector('#login-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = document.querySelector('#message');
            const payload = Object.fromEntries(new FormData(form));
            try {
                const response = await fetch('login.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Connexion impossible.');
                location.href = 'dasboard.php';
            } catch (error) {
                message.textContent = error.message;
            }
        });
    </script>
</body>
</html>