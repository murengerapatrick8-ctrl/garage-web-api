<?php
session_start();
// Si l'utilisateur est déjà connecté, on peut le rediriger vers le dashboard (optionnel)
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f6f1">
    <meta name="description" content="Gérez votre garage plus simplement : clients, véhicules, mécaniciens et interventions réunis dans un seul espace.">
    <title>Garage Atelier — La gestion de votre garage, simplement</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap');
        :root {
            color-scheme: light;
            --ink: #1c2925;
            --muted: #68766f;
            --paper: #f5f6f1;
            --white: #fff;
            --green: #175c4b;
            --green-dark: #174a3e;
            --green-pale: #e7eee7;
            --line: #e1e6df;
            --coral: #c9654a;
            --sand: #f3e1cf;
            --shadow: 0 24px 70px rgba(27, 55, 43, .12);
            --shadow-soft: 0 18px 36px rgba(26, 52, 41, .08);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: radial-gradient(circle at top left, rgba(94, 151, 122, .18), transparent 24%), linear-gradient(180deg, #f5f6f1 0%, #edf3ee 46%, #f8f9f7 100%);
            color: var(--ink);
            font: 15px/1.6 'DM Sans', sans-serif;
        }
        a { color: inherit; }
        .container { width: min(1120px, calc(100% - 48px)); margin-inline: auto; }
        
        /* HEADER */
        .site-header { display: flex; align-items: center; justify-content: space-between; min-height: 82px; position: sticky; top: 0; z-index: 12; backdrop-filter: blur(12px); background: rgba(245, 246, 241, .85); border-bottom: 1px solid var(--line); }
        .brand { display: inline-flex; align-items: center; gap: 11px; color: var(--green-dark); font: 800 14px Manrope, sans-serif; letter-spacing: .06em; text-decoration: none; }
        .brand-mark { display: grid; width: 36px; height: 36px; place-items: center; border: 1px solid #9db9a4; border-radius: 50%; background: linear-gradient(135deg, #f0f6ef, #dfece1); color: var(--green); font-size: 15px; box-shadow: inset 0 2px 4px rgba(255,255,255,.8); }
        .header-right { display: flex; align-items: center; gap: 24px; }
        .header-note { color: var(--muted); font-size: 13px; }
        .text-link { color: var(--green); font-size: 14px; font-weight: 700; text-decoration: none; transition: 0.2s; }
        .text-link:hover { color: var(--green-dark); }
        
        /* HERO */
        .hero { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(400px, .95fr); align-items: center; gap: clamp(40px, 7vw, 88px); min-height: 620px; padding-block: 48px 78px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 9px; color: var(--green); font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .eyebrow::before { width: 24px; height: 1px; background: var(--coral); content: ''; }
        h1, h2, h3, p { margin-top: 0; }
        h1 { max-width: 580px; margin: 20px 0 18px; font: 800 clamp(42px, 4.5vw, 60px)/1.07 Manrope, sans-serif; letter-spacing: -.055em; }
        h1 span { color: var(--green); }
        .hero-copy > p { max-width: 490px; margin-bottom: 28px; color: var(--muted); font-size: 17px; line-height: 1.75; }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
        .button { display: inline-flex; min-height: 50px; align-items: center; justify-content: center; gap: 11px; padding: 0 21px; border: 1px solid transparent; border-radius: 5px; background: linear-gradient(180deg, #1a6957, #175c4b); box-shadow: 0 12px 22px rgba(23, 92, 75, .18); color: white; font-size: 14px; font-weight: 700; text-decoration: none; transition: background .2s ease, transform .2s ease, box-shadow .2s ease; }
        .button:hover { transform: translateY(-2px); background: linear-gradient(180deg, #1b725f, #174a3e); box-shadow: 0 16px 28px rgba(23, 92, 75, .24); }
        .button svg { width: 16px; height: 16px; }
        .subtle-link { color: var(--muted); font-size: 13px; text-decoration: none; }
        .subtle-link:hover { color: var(--green); }
        .trust-note { display: flex; align-items: center; gap: 9px; margin-top: 23px; color: var(--muted); font-size: 12px; }
        .trust-note svg { width: 16px; height: 16px; color: var(--green); }
        .highlight-strip { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 28px; }
        .highlight-pill { display: inline-flex; align-items: center; gap: 8px; padding: 11px 14px; border: 1px solid rgba(33, 72, 58, .08); border-radius: 999px; background: rgba(255, 255, 255, .55); color: var(--ink); font-size: 12px; font-weight: 700; box-shadow: var(--shadow-soft); }
        .highlight-pill::before { content: ''; width: 9px; height: 9px; border-radius: 50%; background: linear-gradient(180deg, #4c9d78, #1d6d54); box-shadow: 0 0 0 4px rgba(92, 170, 126, .18); }
        
        /* APERÇU */
        .preview-wrap { position: relative; padding: 18px 0 22px 18px; }
        .preview-wrap::before { position: absolute; z-index: 0; top: 0; right: 0; bottom: 0; left: 0; border-radius: 14px; background: linear-gradient(135deg, rgba(167, 199, 175, .5), rgba(240, 218, 191, .5)); content: ''; transform: rotate(3deg); }
        .preview { position: relative; z-index: 1; overflow: hidden; border: 1px solid #e8ece6; border-radius: 14px; background: var(--white); box-shadow: var(--shadow); transition: transform 0.3s; cursor: pointer; }
        .preview:hover { transform: scale(1.02); }
        .preview-top { display: flex; align-items: center; justify-content: space-between; min-height: 56px; padding: 0 19px; border-bottom: 1px solid #edf0ec; }
        .preview-brand { display: inline-flex; align-items: center; gap: 8px; color: var(--green-dark); font: 800 10px Manrope, sans-serif; letter-spacing: .07em; }
        .preview-mark { display: grid; width: 23px; height: 23px; place-items: center; border: 1px solid #b5caba; border-radius: 50%; background: linear-gradient(135deg, #ecf5ef, #ddeee2); color: var(--green); font-size: 10px; }
        .preview-date { color: #8a958e; font-size: 10px; }
        .preview-body { padding: 20px; }
        .preview-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 17px; }
        .preview-heading h2 { margin: 0; font: 700 17px Manrope, sans-serif; letter-spacing: -.03em; }
        .preview-heading p { margin: 3px 0 0; color: #88938d; font-size: 10px; }
        .live { display: inline-flex; align-items: center; gap: 6px; padding: 5px 8px; border-radius: 3px; background: #eef5ef; color: #367353; font-size: 9px; font-weight: 700; white-space: nowrap; }
        .live::before { width: 6px; height: 6px; border-radius: 50%; background: #4a9a64; content: ''; }
        .preview-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 9px; margin-bottom: 17px; }
        .preview-stat { min-height: 77px; padding: 11px 12px; border: 1px solid #e9ede8; border-radius: 8px; background: linear-gradient(180deg, #fcfdfb 0%, #f3f7f2 100%); }
        .preview-stat span { display: block; color: #7d8981; font-size: 9px; }
        .preview-stat strong { display: block; margin-top: 4px; color: var(--ink); font: 700 20px Manrope, sans-serif; }
        .preview-stat small { color: #41845a; font-size: 9px; }
        .work-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; font-size: 11px; font-weight: 700; }
        .work-title a { color: var(--green); font-size: 9px; text-decoration: none; }
        .work-row { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 10px; padding: 10px 0; border-top: 1px solid #eff1ed; }
        .work-name { font-size: 10px; font-weight: 700; }
        .work-meta { display: block; margin-top: 2px; color: #87918a; font-size: 9px; font-weight: 400; }
        .status { padding: 4px 7px; border-radius: 3px; background: #f4f0e2; color: #88702a; font-size: 8px; white-space: nowrap; }
        .status.done { background: #eaf3eb; color: #34704a; }
        
        /* FONCTIONNALITÉS */
        .features { padding: 60px 0 78px; border-top: 1px solid var(--line); }
        .section-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin-bottom: 25px; }
        .section-heading h2 { margin: 8px 0 0; font: 700 clamp(24px, 3vw, 32px)/1.2 Manrope, sans-serif; letter-spacing: -.04em; }
        .section-heading > p { max-width: 340px; margin: 0; color: var(--muted); font-size: 13px; }
        .feature-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 15px; }
        .feature-card { min-height: 184px; padding: 22px; border: 1px solid var(--line); border-radius: 14px; background: rgba(255,255,255,.72); box-shadow: 0 8px 18px rgba(28, 41, 37, .02); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .feature-card:hover { transform: translateY(-5px); border-color: #c7d4ca; box-shadow: 0 14px 24px rgba(27,55,43,.06); }
        .feature-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 10px; background: linear-gradient(135deg, #edf5ef, #e2efe5); color: var(--green); }
        .feature-icon svg { width: 19px; height: 19px; }
        .feature-card h3 { margin: 17px 0 5px; font: 700 15px Manrope, sans-serif; }
        .feature-card p { max-width: 290px; margin: 0; color: var(--muted); font-size: 12px; line-height: 1.65; }
        
        /* FOOTER */
        .site-footer { display: flex; align-items: center; justify-content: space-between; gap: 20px; min-height: 68px; border-top: 1px solid var(--line); color: #87918a; font-size: 11px; }
        .site-footer a { color: var(--green); font-weight: 700; text-decoration: none; }
        a:focus-visible { outline: 3px solid #7ba58d; outline-offset: 4px; }
        
        /* RESPONSIVE */
        @media (max-width: 800px) {
            .hero { grid-template-columns: 1fr; gap: 32px; min-height: auto; padding-block: 54px 60px; }
            .hero-copy { max-width: 630px; }
            .highlight-strip { grid-template-columns: 1fr; }
            .preview-wrap { width: min(100%, 560px); margin-inline: auto; }
        }
        @media (max-width: 560px) {
            .container { width: min(100% - 36px, 480px); }
            .site-header { min-height: 70px; }
            .header-right { gap: 0; }
            .header-note { display: none; }
            .hero { gap: 26px; padding-block: 39px 48px; }
            h1 { font-size: clamp(39px, 11vw, 54px); }
            .hero-copy > p { font-size: 15px; }
            .actions { align-items: flex-start; flex-direction: column; gap: 13px; }
            .button { width: 100%; }
            .highlight-strip { gap: 10px; margin-top: 18px; }
            .preview-wrap { padding: 11px 0 14px 10px; }
            .preview-body { padding: 15px; }
            .preview-top { min-height: 49px; padding-inline: 14px; }
            .preview-stats { gap: 6px; }
            .preview-stat { min-height: 69px; padding: 9px 8px; }
            .preview-stat strong { font-size: 17px; }
            .features { padding-block: 32px 48px; }
            .section-heading { align-items: flex-start; flex-direction: column; gap: 8px; margin-bottom: 18px; }
            .feature-grid { grid-template-columns: 1fr; gap: 10px; }
            .feature-card { min-height: auto; padding: 18px; }
            .feature-card h3 { margin-top: 12px; }
            .site-footer { align-items: flex-start; flex-direction: column; justify-content: center; gap: 3px; padding-block: 15px; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="site-header container">
        <a class="brand" href="index.php" aria-label="Garage Atelier, accueil">
            <span class="brand-mark" aria-hidden="true">G</span>
            <span>GARAGE / ATELIER</span>
        </a>
        <div class="header-right">
            <span class="header-note">Votre atelier, mieux organisé.</span>
            <a class="text-link" href="login.php">Se connecter <span aria-hidden="true">→</span></a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <main>
        <section class="hero container" aria-labelledby="hero-title">
            <div class="hero-copy">
                <div class="eyebrow">La gestion d’atelier, simplement</div>
                <h1 id="hero-title">Tout votre garage.<br><span>Au même endroit.</span></h1>
                <p>Clients, véhicules, équipe et réparations : gardez le fil de votre activité avec un espace de gestion clair, pensé pour votre atelier.</p>
                <div class="actions">
                    <a class="button" href="login.php">
                        Accéder à mon atelier
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a class="subtle-link" href="#fonctionnalites">Découvrir les fonctionnalités <span aria-hidden="true">↓</span></a>
                </div>
                <div class="trust-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Un espace de travail dédié à votre équipe
                </div>
                <div class="highlight-strip" aria-label="Points forts de l’atelier">
                    <span class="highlight-pill">+94% de suivi</span>
                    <span class="highlight-pill">4,9/5 satisfaction</span>
                    <span class="highlight-pill">Mieux organisé</span>
                </div>
            </div>

            <!-- APERÇU INTERACTIF DU DASHBOARD -->
            <div class="preview-wrap" aria-label="Aperçu du tableau de bord">
                <a href="login.php" style="text-decoration: none;">
                    <div class="preview">
                        <div class="preview-top">
                            <div class="preview-brand"><span class="preview-mark" aria-hidden="true">G</span> GARAGE / ATELIER</div>
                            <span class="preview-date">VUE D’ENSEMBLE</span>
                        </div>
                        <div class="preview-body">
                            <div class="preview-heading">
                                <div><h2>Bonjour, l’équipe</h2><p>Voici le suivi de votre atelier.</p></div>
                                <span class="live">À JOUR</span>
                            </div>
                            <div class="preview-stats" aria-label="Indicateurs illustratifs">
                                <div class="preview-stat"><span>Interventions</span><strong>12</strong><small>En cours</small></div>
                                <div class="preview-stat"><span>Véhicules</span><strong>08</strong><small>À l’atelier</small></div>
                                <div class="preview-stat"><span>Équipe</span><strong>04</strong><small>Mécaniciens</small></div>
                            </div>
                            <div class="work-title"><span>Suivi des interventions</span><span style="color:var(--green); font-size:9px; font-weight:700;">Cliquez pour voir →</span></div>
                            <div class="work-row">
                                <span class="work-name">Révision périodique<span class="work-meta">Peugeot 308 · M. Laurent</span></span>
                                <span class="status">En cours</span>
                            </div>
                            <div class="work-row">
                                <span class="work-name">Diagnostic moteur<span class="work-meta">Renault Clio · Mme Martin</span></span>
                                <span class="status">À planifier</span>
                            </div>
                            <div class="work-row">
                                <span class="work-name">Remplacement des freins<span class="work-meta">Citroën C3 · M. Bernard</span></span>
                                <span class="status done">Terminée</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- FONCTIONNALITÉS -->
        <section class="features container" id="fonctionnalites" aria-labelledby="features-title">
            <div class="section-heading">
                <div><div class="eyebrow">Tout au même endroit</div><h2 id="features-title">Moins de dispersion.<br>Plus de visibilité.</h2></div>
                <p>Les outils essentiels pour suivre votre activité et retrouver rapidement les bonnes informations.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <span class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3 3 0 0 1 0 5.6M18 14a5 5 0 0 1 3 4.6V20"/></svg></span>
                    <h3>Clients &amp; véhicules</h3>
                    <p>Retrouvez les coordonnées, l’historique et les informations des véhicules au même endroit.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M14.7 6.3a5 5 0 0 0-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 0 0 6.4-6.4L14 13l-3-3 3.7-3.7Z"/></svg></span>
                    <h3>Interventions suivies</h3>
                    <p>Consultez les réparations en cours, leur statut et les mécaniciens qui en ont la charge.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 3v18h18M7 14l4-4 3 3 6-7"/><path d="M15 6h5v5"/></svg></span>
                    <h3>Vue d’ensemble</h3>
                    <p>Accédez rapidement aux indicateurs utiles pour garder une vision claire de l’activité.</p>
                </article>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="site-footer container">
        <span>© <?= date('Y') ?> Garage / Atelier</span>
        <a href="login.php">Accéder à mon espace <span aria-hidden="true">→</span></a>
    </footer>
</body>
</html>