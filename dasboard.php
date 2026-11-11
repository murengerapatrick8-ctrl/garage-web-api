<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord | Garage</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap');
        :root { color-scheme: light; --ink: #1c2925; --muted: #718078; --paper: #f3f5f0; --white: #fff; --green: #175c4b; --green-pale: #e4eee7; --line: #dfe5de; --coral: #c9654a; --shadow: 0 12px 30px #193b2e0b; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font: 14px 'DM Sans', sans-serif; }
        button, input { font: inherit; }
        button { cursor: pointer; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 244px minmax(0, 1fr); }
        aside { display: flex; flex-direction: column; padding: 26px 18px; background: #174a3e; color: white; }
        .brand { display: flex; align-items: center; gap: 11px; padding: 4px 9px 34px; font: 800 15px Manrope, sans-serif; }
        .brand-mark { display: grid; place-items: center; width: 34px; height: 34px; border: 1px solid #8fb39a; border-radius: 50%; color: #c5dcbb; }
        .nav-label { padding: 0 10px 10px; color: #a9c5b3; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        nav { display: grid; gap: 5px; }
        .nav-item { display: flex; align-items: center; gap: 11px; width: 100%; min-height: 42px; padding: 0 11px; border: 0; border-radius: 4px; background: transparent; color: #d4e2d9; text-align: left; }
        .nav-item:hover, .nav-item.active { background: #ffffff19; color: white; }
        .nav-icon { width: 22px; color: #b7d3bf; font-size: 15px; text-align: center; }
        .side-bottom { margin-top: auto; border-top: 1px solid #ffffff25; padding: 16px 9px 0; }
        .user-label { margin-bottom: 12px; color: #e0ebe2; font-weight: 600; }
        .logout { color: #c5d7ca; text-decoration: none; font-size: 13px; }
        .logout:hover { color: white; }
        main { min-width: 0; padding: 34px clamp(20px, 4vw, 58px) 50px; }
        .topline { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
        .crumb { margin-bottom: 7px; color: var(--muted); font-size: 12px; }
        h1, h2 { margin: 0; font-family: Manrope, sans-serif; }
        h1 { font-size: 28px; }
        .primary { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 16px; border: 0; border-radius: 4px; background: var(--green); color: white; font-weight: 700; }
        .primary[hidden] { display: none; }
        .stats { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 13px; margin-bottom: 22px; }
        .stat { min-height: 104px; padding: 17px 18px; border: 1px solid var(--line); border-radius: 5px; background: var(--white); box-shadow: var(--shadow); }
        .stat-name { color: var(--muted); font-size: 12px; }
        .stat-value { margin-top: 9px; font: 800 25px Manrope, sans-serif; }
        .notice { margin-bottom: 20px; padding: 12px 15px; border: 1px solid #e8c9bf; border-radius: 5px; background: #fff5f1; color: #8d392c; }
        .notice[hidden], .stats-detail[hidden], #records-section[hidden] { display: none; }
        .stats-detail { margin-bottom: 30px; padding: 20px; border: 1px solid var(--line); border-radius: 5px; background: var(--white); box-shadow: var(--shadow); }
        .stats-detail h2 { margin-bottom: 16px; font-size: 17px; }
        .status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .status-card { padding: 14px; border: 1px solid var(--line); border-radius: 4px; background: #fbfcfa; }
        .status-card span { display: block; color: var(--muted); font-size: 12px; }
        .status-card strong { display: block; margin-top: 5px; font: 700 20px Manrope, sans-serif; }
        .section-head { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 12px; }
        .search { width: min(260px, 100%); height: 39px; padding: 0 12px; border: 1px solid var(--line); border-radius: 4px; }
        .table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 5px; background: white; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; background: #f8faf7; color: #67766e; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        td { padding: 13px 15px; border-top: 1px solid #edf0eb; color: #32413b; }
        .empty-state { padding: 22px; color: var(--muted); text-align: center; }
        .actions { display: flex; gap: 7px; }
        .icon-button { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 4px; background: white; color: var(--green); font-weight: 700; }
        .icon-button.delete { color: var(--coral); }
        dialog { width: min(520px, calc(100% - 28px)); padding: 0; border: 1px solid var(--line); border-radius: 6px; }
        .dialog-head { display: flex; justify-content: space-between; padding: 19px 22px; border-bottom: 1px solid var(--line); }
        .dialog-form { display: grid; gap: 13px; padding: 19px 22px 22px; }
        #fields { display: grid; gap: 13px; }
        .field label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; }
        .field input, .field select, .field textarea { width: 100%; min-height: 41px; padding: 9px 11px; border: 1px solid var(--line); border-radius: 4px; font: inherit; }
        .field textarea { min-height: 90px; resize: vertical; }
        .field input:focus, .field select:focus, .field textarea:focus, .search:focus { outline: 2px solid #84ad9b; outline-offset: 1px; }
        .dialog-actions { display: flex; justify-content: flex-end; gap: 9px; }
        .secondary { min-height: 40px; padding: 0 14px; border: 1px solid var(--line); border-radius: 4px; background: white; }
        .close { border: 0; background: transparent; color: var(--muted); font-size: 22px; cursor: pointer; }
        @media (max-width: 900px) { .shell { grid-template-columns: 200px minmax(0, 1fr); } .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 620px) { .shell { grid-template-columns: 1fr; } aside { padding: 16px; } .brand { padding-bottom: 18px; } nav { grid-template-columns: repeat(2, minmax(0, 1fr)); } .side-bottom { margin-top: 18px; } main { padding: 24px 16px 36px; } .topline { align-items: center; } h1 { font-size: 23px; } .primary { padding-inline: 12px; } .section-head { align-items: flex-start; flex-direction: column; } .search { width: 100%; } .stats { gap: 9px; } .stat { min-height: 90px; padding: 13px; } .stat-value { font-size: 21px; } .table-wrap { font-size: 12px; } th, td { padding: 10px; } .dialog-form { padding: 16px; } }
    </style>
</head>
<body>
<div class="shell">
    <aside>
        <div class="brand"><span class="brand-mark">G</span> GARAGE / ATELIER</div>
        <div class="nav-label">Gestion</div>
        <nav id="navigation"></nav>
        <div class="side-bottom">
            <div class="user-label"><?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur', ENT_QUOTES, 'UTF-8') ?></div>
            <a class="logout" href="login.php?logout=1">Se déconnecter</a>
        </div>
    </aside>
    <main>
        <header class="topline">
            <div><div class="crumb">Atelier / <span id="breadcrumb">Vue d’ensemble</span></div><h1 id="page-title">Vue d’ensemble</h1></div>
            <button class="primary" id="add-button" type="button">＋ Nouveau</button>
        </header>
        <div class="notice" id="notice" role="alert" hidden></div>
        <section class="stats" id="stats"></section>
        <section class="stats-detail" id="statistics-detail" hidden>
            <h2>Activité des interventions</h2>
            <div class="status-grid" id="statistics-breakdown"></div>
        </section>
        <section id="records-section">
            <div class="section-head"><h2 id="table-title">Enregistrements</h2><input id="search" class="search" type="search" placeholder="Rechercher…"></div>
            <div class="table-wrap"><table><thead><tr id="table-head"></tr></thead><tbody id="table-body"></tbody></table></div>
        </section>
    </main>
</div>
<dialog id="record-dialog">
    <div class="dialog-head"><h2 id="dialog-title">Nouvel enregistrement</h2><button class="close" id="close-dialog" type="button" aria-label="Fermer">×</button></div>
    <form class="dialog-form" id="record-form"><div id="fields"></div><div class="dialog-actions"><button class="secondary" id="cancel-dialog" type="button">Annuler</button><button class="primary" type="submit">Enregistrer</button></div></form>
</dialog>
<script>
const entities = {
    clients: { label: 'Clients', icon: 'C', endpoint: 'clients.php', columns: [['prenom', 'Prénom'], ['nom', 'Nom'], ['telephone', 'Téléphone'], ['email', 'E-mail']], fields: [['prenom', 'Prénom', 'text', true], ['nom', 'Nom', 'text', true], ['telephone', 'Téléphone'], ['email', 'E-mail', 'email']] },
    vehicules: { label: 'Véhicules', icon: 'V', endpoint: 'vehicules.php', columns: [['immatriculation', 'Immatriculation'], ['marque', 'Marque'], ['modele', 'Modèle'], ['annee', 'Année'], ['client', 'Client']], fields: [['immatriculation', 'Immatriculation', 'text', true], ['marque', 'Marque', 'text', true], ['modele', 'Modèle', 'text', true], ['annee', 'Année', 'number'], ['kilometrage', 'Kilométrage', 'number'], ['client_id', 'Client', 'select', true, 'clients']] },
    mecaniciens: { label: 'Mécaniciens', icon: 'M', endpoint: 'mecaniciens.php', columns: [['prenom', 'Prénom'], ['nom', 'Nom'], ['telephone', 'Téléphone'], ['specialite', 'Spécialité'], ['date_embauche', 'Embauché le']], fields: [['prenom', 'Prénom', 'text', true], ['nom', 'Nom', 'text', true], ['telephone', 'Téléphone'], ['specialite', 'Spécialité'], ['date_embauche', 'Date d’embauche', 'date']] },
    interventions: { label: 'Interventions', icon: 'I', endpoint: 'interventions.php', columns: [['immatriculation', 'Véhicule'], ['mecanicien', 'Mécanicien'], ['date_intervention', 'Date'], ['description', 'Description'], ['cout', 'Coût'], ['statut', 'Statut']], fields: [['vehicule_id', 'Véhicule', 'select', true, 'vehicules'], ['mecanicien_id', 'Mécanicien', 'select', true, 'mecaniciens'], ['date_intervention', 'Date', 'date', true], ['description', 'Description', 'textarea', true], ['cout', 'Coût', 'number'], ['statut', 'Statut', 'options', true, [['en_attente', 'En attente'], ['en_cours', 'En cours'], ['terminee', 'Terminée']]]] },
    statistiques: { label: 'Statistiques', icon: '↗', endpoint: 'statistiques.php', columns: [], fields: [] }
};
const state = { current: 'statistiques', rows: [], editing: null, cache: {}, statistics: null, requestId: 0 };
const navigation = document.querySelector('#navigation');
for (const [key, entity] of Object.entries(entities)) {
    const button = document.createElement('button');
    button.className = 'nav-item';
    button.type = 'button';
    button.dataset.entity = key;
    const icon = document.createElement('span');
    icon.className = 'nav-icon';
    icon.textContent = entity.icon;
    const label = document.createElement('span');
    label.textContent = entity.label;
    button.append(icon, label);
    navigation.append(button);
}

function showNotice(message) {
    const notice = document.querySelector('#notice');
    notice.textContent = message;
    notice.hidden = !message;
}

async function api(url, options = {}) {
    const response = await fetch(`api/${url}`, {
        credentials: 'same-origin',
        ...options,
        headers: { 'Content-Type': 'application/json', ...(options.headers || {}) }
    });
    let result;
    try {
        result = await response.json();
    } catch (error) {
        throw new Error('Le serveur a renvoyé une réponse invalide.');
    }
    if (response.status === 401) {
        location.href = 'login.php';
        throw new Error('Session expirée. Veuillez vous reconnecter.');
    }
    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Une erreur est survenue.');
    }
    return result.data;
}

function fullName(row) { return `${row.prenom || ''} ${row.nom || ''}`.trim(); }
function displayValue(row, key) {
    const value = row[key] ?? '';
    if (key === 'statut') {
        return ({ en_attente: 'En attente', en_cours: 'En cours', terminee: 'Terminée' })[value] || value;
    }
    if (key === 'cout' && value !== '') {
        return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 2 }).format(Number(value));
    }
    return value;
}

function renderRows() {
    const entity = entities[state.current];
    const query = document.querySelector('#search').value.trim().toLocaleLowerCase('fr');
    const rows = state.rows.filter(row => entity.columns.some(([key]) =>
        String(displayValue(row, key)).toLocaleLowerCase('fr').includes(query)
    ));
    const body = document.querySelector('#table-body');
    body.replaceChildren();
    if (!rows.length) {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = entity.columns.length + 1;
        td.className = 'empty-state';
        td.textContent = query ? 'Aucun résultat pour cette recherche.' : 'Aucun enregistrement pour le moment.';
        tr.append(td);
        body.append(tr);
        return;
    }
    for (const row of rows) {
        const tr = document.createElement('tr');
        for (const [key] of entity.columns) {
            const td = document.createElement('td');
            td.textContent = displayValue(row, key);
            tr.append(td);
        }
        const actions = document.createElement('td');
        actions.className = 'actions';
        const edit = document.createElement('button');
        edit.className = 'icon-button';
        edit.type = 'button';
        edit.textContent = '✎';
        edit.title = 'Modifier';
        edit.setAttribute('aria-label', 'Modifier cet enregistrement');
        edit.addEventListener('click', () => openDialog(row));
        const remove = document.createElement('button');
        remove.className = 'icon-button delete';
        remove.type = 'button';
        remove.textContent = '×';
        remove.title = 'Supprimer';
        remove.setAttribute('aria-label', 'Supprimer cet enregistrement');
        remove.addEventListener('click', () => deleteRow(row));
        actions.append(edit, remove);
        tr.append(actions);
        body.append(tr);
    }
}

async function loadEntity(key) {
    const entity = entities[key];
    if (!entity) return;
    const requestId = ++state.requestId;
    state.current = key;
    showNotice('');
    document.querySelectorAll('.nav-item').forEach(button => {
        button.classList.toggle('active', button.dataset.entity === key);
    });
    document.querySelector('#breadcrumb').textContent = entity.label;
    document.querySelector('#page-title').textContent = entity.label;
    const isStatistics = key === 'statistiques';
    document.querySelector('#add-button').hidden = isStatistics;
    document.querySelector('#statistics-detail').hidden = !isStatistics;
    document.querySelector('#records-section').hidden = isStatistics;
    if (isStatistics) {
        try {
            const data = await loadStats();
            if (requestId !== state.requestId) return;
            renderStatistics(data);
        } catch (error) {
            if (requestId === state.requestId) showNotice(error.message);
        }
        return;
    }
    document.querySelector('#table-title').textContent = `Liste des ${entity.label.toLowerCase()}`;
    const head = document.querySelector('#table-head');
    head.replaceChildren();
    for (const [, label] of entity.columns) {
        const th = document.createElement('th');
        th.textContent = label;
        head.append(th);
    }
    const actionsHeading = document.createElement('th');
    actionsHeading.textContent = 'Actions';
    head.append(actionsHeading);
    state.rows = [];
    const body = document.querySelector('#table-body');
    body.replaceChildren();
    const loadingRow = document.createElement('tr');
    const loadingCell = document.createElement('td');
    loadingCell.colSpan = entity.columns.length + 1;
    loadingCell.className = 'empty-state';
    loadingCell.textContent = 'Chargement…';
    loadingRow.append(loadingCell);
    body.append(loadingRow);
    try {
        const rows = await api(entity.endpoint);
        if (requestId !== state.requestId) return;
        state.rows = rows;
        state.cache[key] = state.rows;
        renderRows();
        await loadStats();
    } catch (error) {
        if (requestId !== state.requestId) return;
        state.rows = [];
        renderRows();
        showNotice(error.message);
    }
}

async function loadStats() {
    const data = await api('statistiques.php');
    state.statistics = data;
    const totals = data.totaux;
    const cards = [
        ['Clients', totals.clients],
        ['Véhicules', totals.vehicules],
        ['Mécaniciens', totals.mecaniciens],
        ['Interventions', totals.interventions],
        ['Chiffre d’affaires', new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(totals.chiffre_affaires)]
    ];
    const container = document.querySelector('#stats');
    container.replaceChildren();
    for (const [label, value] of cards) {
        const card = document.createElement('div');
        card.className = 'stat';
        const name = document.createElement('div');
        name.className = 'stat-name';
        name.textContent = label;
        const amount = document.createElement('div');
        amount.className = 'stat-value';
        amount.textContent = value;
        card.append(name, amount);
        container.append(card);
    }
    return data;
}

function renderStatistics(data) {
    const statuses = [
        ['En attente', data.statuts.en_attente],
        ['En cours', data.statuts.en_cours],
        ['Terminées', data.statuts.terminee],
        ['Chiffre d’affaires terminé', new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(data.totaux.chiffre_affaires)]
    ];
    const container = document.querySelector('#statistics-breakdown');
    container.replaceChildren();
    for (const [label, value] of statuses) {
        const card = document.createElement('div');
        card.className = 'status-card';
        const name = document.createElement('span');
        name.textContent = label;
        const amount = document.createElement('strong');
        amount.textContent = value;
        card.append(name, amount);
        container.append(card);
    }
}

function optionLabel(key, row) {
    if (key === 'clients' || key === 'mecaniciens') return fullName(row);
    return `${row.immatriculation} — ${row.marque} ${row.modele}`.trim();
}

async function openDialog(row = null) {
    const entity = entities[state.current];
    const fields = document.querySelector('#fields');
    fields.replaceChildren();
    state.editing = row ? Number(row.id) : null;
    document.querySelector('#dialog-title').textContent = row ? `Modifier : ${entity.label}` : `Ajouter : ${entity.label}`;
    try {
        for (const [name, label, type, required, extra] of entity.fields) {
            const wrapper = document.createElement('div');
            wrapper.className = 'field';
            const inputId = `field-${name}`;
            const fieldLabel = document.createElement('label');
            fieldLabel.htmlFor = inputId;
            fieldLabel.textContent = label;
            let input;
            if (type === 'select' || type === 'options') {
                input = document.createElement('select');
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Sélectionner…';
                input.append(placeholder);
                let options = extra;
                if (type === 'select') {
                    if (!state.cache[extra]) state.cache[extra] = await api(entities[extra].endpoint);
                    options = state.cache[extra].map(option => [option.id, optionLabel(extra, option)]);
                }
                for (const [value, text] of options) {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = text;
                    input.append(option);
                }
            } else if (type === 'textarea') {
                input = document.createElement('textarea');
            } else {
                input = document.createElement('input');
                input.type = type;
                if (type === 'number') {
                    input.min = '0';
                    input.step = name === 'cout' ? '0.01' : '1';
                }
                if (type === 'date' && name === 'date_intervention' && !row) {
                    const now = new Date();
                    input.value = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
                }
            }
            input.id = inputId;
            input.name = name;
            input.required = Boolean(required);
            if (row && row[name] !== null && row[name] !== undefined) input.value = row[name];
            wrapper.append(fieldLabel, input);
            fields.append(wrapper);
        }
        document.querySelector('#record-dialog').showModal();
    } catch (error) {
        showNotice(error.message);
    }
}

async function deleteRow(row) {
    const entity = entities[state.current];
    if (!confirm(`Supprimer cet enregistrement de ${entity.label.toLowerCase()} ? Cette action est définitive.`)) return;
    try {
        await api(`${entity.endpoint}?id=${encodeURIComponent(row.id)}`, { method: 'DELETE' });
        await loadEntity(state.current);
    } catch (error) {
        showNotice(error.message);
    }
}

navigation.addEventListener('click', event => {
    const button = event.target.closest('.nav-item');
    if (!button) return;
    document.querySelector('#search').value = '';
    loadEntity(button.dataset.entity);
});
document.querySelector('#search').addEventListener('input', renderRows);
document.querySelector('#add-button').addEventListener('click', () => openDialog());
document.querySelector('#close-dialog').addEventListener('click', () => document.querySelector('#record-dialog').close());
document.querySelector('#cancel-dialog').addEventListener('click', () => document.querySelector('#record-dialog').close());
document.querySelector('#record-form').addEventListener('submit', async event => {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData);
    for (const [name, , type] of entities[state.current].fields) {
        if (type === 'number') {
            payload[name] = payload[name] === '' ? (name === 'cout' || name === 'kilometrage' ? 0 : null) : Number(payload[name]);
        }
        if (type === 'date' && payload[name] === '') payload[name] = null;
    }
    const editing = state.editing !== null;
    try {
        const url = editing
            ? `${entities[state.current].endpoint}?id=${encodeURIComponent(state.editing)}`
            : entities[state.current].endpoint;
        await api(url, { method: editing ? 'PUT' : 'POST', body: JSON.stringify(payload) });
        document.querySelector('#record-dialog').close();
        form.reset();
        await loadEntity(state.current);
    } catch (error) {
        showNotice(error.message);
    }
});

loadEntity('statistiques');
</script>
</body>
</html>