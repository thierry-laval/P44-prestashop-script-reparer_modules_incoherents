<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html lang='fr'><head>
<meta charset='UTF-8'>
<title>Comparaison & Correction des modules PrestaShop</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
table { border-collapse: collapse; width: 100%; background: #fff; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #eee; }
button { margin: 10px 5px; padding: 6px 12px; cursor: pointer; }
tr.hidden { display: none; }
tr.ok { background: #d4edda; }
tr.diff { background: #fff3cd; }
tr.missing { background: #f8d7da; }
.message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
.success { background: #d4edda; color: #155724; }
.error { background: #f8d7da; color: #721c24; }
</style>
</head><body>";

echo "<h1>🛠 Comparaison & Correction des modules PrestaShop</h1>";

// 🔧 Connexion base de données - Mettez vos données
$dbHost = 'localhost';
$dbName = 'NOM_BASE_DE_DONNEES';
$dbUser = 'NOM_UTILISATEUR';
$dbPass = 'VOTRE MOT DE PASSE';
$dbPrefix = 'ps_';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p>✅ Connexion à la base réussie</p>";
} catch (PDOException $e) {
    die("<div class='message error'><strong>❌ Erreur de connexion :</strong> " . htmlspecialchars($e->getMessage()) . "</div></body></html>");
}

// Récupération modules en base avec version et actif
$dbModules = [];
$stmt = $pdo->query("SELECT id_module, name, version, active FROM {$dbPrefix}module ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dbModules[$row['name']] = [
        'id_module' => $row['id_module'],
        'version' => $row['version'],
        'active' => (bool)$row['active']
    ];
}
echo "<p>✅ Modules récupérés depuis la base : " . count($dbModules) . "</p>";

// Récupération modules sur FTP
$modulesDir = __DIR__ . '/modules';
$ftpVersions = [];

if (!is_dir($modulesDir)) {
    die("<div class='message error'>❌ Dossier /modules introuvable</div></body></html>");
}

$ftpModules = scandir($modulesDir);
foreach ($ftpModules as $module) {
    if ($module === '.' || $module === '..' || $module[0] === '.' || !is_dir("$modulesDir/$module")) continue;

    $configXmlPath = "$modulesDir/$module/config.xml";
    if (file_exists($configXmlPath)) {
        $xml = simplexml_load_file($configXmlPath);
        if ($xml && isset($xml->version)) {
            $ftpVersions[$module] = (string)$xml->version;
        } else {
            $ftpVersions[$module] = '⚠️ Version introuvable';
        }
    } else {
        $ftpVersions[$module] = '❌ config.xml manquant';
    }
}
echo "<p>📦 Modules détectés dans /modules : " . count($ftpVersions) . "</p>";

// Traitement de la correction automatique si demandé
$correctionMessage = '';
if (isset($_POST['correct_versions'])) {
    $updatedCount = 0;
    $errors = [];

    foreach ($dbModules as $moduleName => $data) {
        if (!isset($ftpVersions[$moduleName])) continue; // pas sur FTP, skip

        $ftpVer = $ftpVersions[$moduleName];
        if ($ftpVer === '⚠️ Version introuvable' || $ftpVer === '❌ config.xml manquant') {
            $errors[] = "Impossible de corriger $moduleName : version FTP introuvable.";
            continue;
        }

        if ($data['version'] !== $ftpVer) {
            // Mise à jour en base
            try {
                $stmt = $pdo->prepare("UPDATE {$dbPrefix}module SET version = :version WHERE id_module = :id");
                $stmt->execute(['version' => $ftpVer, 'id' => $data['id_module']]);
                $updatedCount++;
            } catch (Exception $e) {
                $errors[] = "Erreur lors de la mise à jour de $moduleName : " . $e->getMessage();
            }
        }
    }

    if ($updatedCount) {
        $correctionMessage = "<div class='message success'>✅ Correction effectuée : $updatedCount module(s) mis à jour.</div>";
    } elseif (empty($errors)) {
        $correctionMessage = "<div class='message success'>ℹ️ Aucune différence détectée, rien à corriger.</div>";
    }

    if ($errors) {
        $correctionMessage .= "<div class='message error'><strong>Erreurs :</strong><ul><li>" . implode("</li><li>", $errors) . "</li></ul></div>";
    }
}

echo $correctionMessage;

// Préparation liste complète des modules à comparer
$allModules = array_unique(array_merge(array_keys($dbModules), array_keys($ftpVersions)));

echo <<<HTML
<h2>📊 Comparaison</h2>
<form method="post" onsubmit="return confirm('Confirmez-vous la correction des versions en base selon le FTP ?');">
<button type="submit" name="correct_versions">🛠 Corriger les versions en base</button>
</form>
<button onclick="filterRows('all')">🔄 Tous</button>
<button onclick="filterRows('diff')">⚠️ Différents</button>
<button onclick="filterRows('missing')">❌ Manquants</button>
<button onclick="exportCSV()">📄 Export CSV</button>
<table id="modulesTable">
<thead>
<tr>
<th>Module</th>
<th>Version BD (Actif)</th>
<th>Version FTP</th>
<th>État</th>
</tr>
</thead>
<tbody>
HTML;

foreach ($allModules as $module) {
    $verDB = $dbModules[$module]['version'] ?? '-';
    $activeDB = $dbModules[$module]['active'] ?? false;
    $verFTP = $ftpVersions[$module] ?? '-';

    if ($verDB === '-' && $verFTP !== '-') {
        $status = '🆕 Présent sur FTP uniquement';
        $class = 'missing';
    } elseif ($verFTP === '-' && $verDB !== '-') {
        $status = '❌ Présent en base uniquement';
        $class = 'missing';
    } elseif ($verDB === $verFTP) {
        $status = '✅ Identique';
        $class = 'ok';
    } else {
        $status = '⚠️ Différent';
        $class = 'diff';
    }

    $activeText = $activeDB ? 'Oui' : 'Non';

    echo "<tr class='$class'>
            <td>" . htmlspecialchars($module) . "</td>
            <td>" . htmlspecialchars($verDB) . " (Actif: $activeText)</td>
            <td>" . htmlspecialchars($verFTP) . "</td>
            <td>$status</td>
          </tr>";
}

echo "</tbody></table>";

echo <<<JS
<script>
function filterRows(type) {
    const rows = document.querySelectorAll("#modulesTable tbody tr");
    rows.forEach(row => {
        row.classList.remove("hidden");
        if(type === 'diff' && !row.classList.contains('diff')) row.classList.add('hidden');
        if(type === 'missing' && !row.classList.contains('missing')) row.classList.add('hidden');
    });
}
function exportCSV() {
    let csv = "Module,Version BD (Actif),Version FTP,État\\n";
    document.querySelectorAll("#modulesTable tbody tr").forEach(row => {
        const cols = row.querySelectorAll("td");
        csv += Array.from(cols).map(td => td.textContent.trim().replace(/\\n/g, '')).join(",") + "\\n";
    });
    const blob = new Blob([csv], {type: "text/csv;charset=utf-8;"});
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "modules_comparaison.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
JS;

echo "</body></html>";
