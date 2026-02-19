<?php
/**
 * WHMCS XML Sitemap Generator
 * Generates a Google-Friendly sitemap.xml for WHMCS Basic URLs
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

function whmcs_sitemap_config() {
    return [
        'name' => 'Sitemap Generator',
        'description' => 'Generates a Google-friendly sitemap.xml including public Knowledgebase articles with Basic routing.',
        'author' => 'System Admin',
        'language' => 'english',
        'version' => '1.4',
        'fields' => [
            'sitemap_filename' => [
                'FriendlyName' => 'Sitemap Filename',
                'Type' => 'text',
                'Size' => '25',
                'Default' => 'sitemap.xml',
                'Description' => 'The name of the file to be generated in your WHMCS root (e.g., sitemap.xml).',
            ]
        ]
    ];
}

function whmcs_sitemap_activate() {
    return [
        'status' => 'success',
        'description' => 'Sitemap generator activated. Ensure your WHMCS root directory is writable for the XML file.'
    ];
}

function whmcs_sitemap_deactivate() {
    return ['status' => 'success', 'description' => 'Module deactivated successfully.'];
}

function whmcs_sitemap_output($vars) {
    $modulelink = $vars['modulelink'];
    $filename = !empty($vars['sitemap_filename']) ? $vars['sitemap_filename'] : 'sitemap.xml';
    
    // Handle Generation Request
    if (isset($_POST['generate_sitemap'])) {
        $result = whmcs_sitemap_build_xml($filename);
        if ($result['status'] === 'success') {
            echo '<div class="alert alert-success"><strong>Success!</strong> Sitemap generated successfully. You can view it here: <a href="' . $result['url'] . '" target="_blank">' . $result['url'] . '</a></div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error:</strong> ' . $result['message'] . '</div>';
        }
    }

    // Admin UI
    echo '<h2>XML Sitemap Generator</h2>';
    echo '<p>This tool will query your public pages and knowledgebase to generate an up-to-date, Google-friendly XML sitemap.</p>';
    echo '<form method="post" action="' . $modulelink . '">';
    echo '<input type="hidden" name="generate_sitemap" value="1">';
    echo '<button type="submit" class="btn btn-primary">Generate ' . htmlspecialchars($filename) . '</button>';
    echo '</form>';
}

function whmcs_sitemap_build_xml($filename) {
    try {
        // Fetch base URL securely using modern WHMCS Config class
        $systemUrl = \WHMCS\Config\Setting::getValue('SystemURL');
        if (empty($systemUrl)) {
            throw new Exception("System URL is not configured in WHMCS General Settings.");
        }
        $systemUrl = rtrim($systemUrl, '/') . '/';

        // Initialize DOMDocument for proper XML structuring
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        $urlset = $xml->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($urlset);

        $date = date('Y-m-d');

        // 1. Core WHMCS Static Pages
        $staticPages = [
            '' => '1.0',                     // Homepage
            'knowledgebase.php' => '0.8',    // KB Index
            'announcements.php' => '0.8',    // Announcements
            'contact.php' => '0.6',          // Contact
            'store' => '0.8'                 // Store/Products
        ];

        foreach ($staticPages as $page => $priority) {
            $url = $xml->createElement('url');
            
            $loc = $xml->createElement('loc');
            $loc->appendChild($xml->createTextNode($systemUrl . $page));
            $url->appendChild($loc);
            
            $url->appendChild($xml->createElement('lastmod', $date));
            $url->appendChild($xml->createElement('changefreq', 'weekly'));
            $url->appendChild($xml->createElement('priority', $priority));
            
            $urlset->appendChild($url);
        }

        // 2. Fetch Active, Public Knowledgebase Articles
        $kbArticles = Capsule::table('tblknowledgebase')
            ->join('tblknowledgebaselinks', 'tblknowledgebase.id', '=', 'tblknowledgebaselinks.articleid')
            ->join('tblknowledgebasecats', 'tblknowledgebaselinks.categoryid', '=', 'tblknowledgebasecats.id')
            ->where(function($query) {
                // Ensure the article is not marked as private (client-only)
                $query->where('tblknowledgebase.private', '!=', 'on')
                      ->orWhereNull('tblknowledgebase.private');
            })
            // Filter out articles in hidden categories
            ->where('tblknowledgebasecats.hidden', '!=', 'on')
            ->select('tblknowledgebase.id', 'tblknowledgebase.title')
            ->distinct()
            ->get();

        foreach ($kbArticles as $article) {
            // Generate Google-Friendly SEO Slug
            // We retain the original case but replace spaces/special chars with hyphens
            $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', trim($article->title));
            $slug = trim($slug, '-');
            
            // Format specifically for WHMCS "Basic URLs" routing mode
            $articleUrl = $systemUrl . "index.php?rp=/knowledgebase/{$article->id}/{$slug}.html";

            $url = $xml->createElement('url');
            
            $loc = $xml->createElement('loc');
            $loc->appendChild($xml->createTextNode($articleUrl));
            $url->appendChild($loc);
            
            $url->appendChild($xml->createElement('changefreq', 'monthly'));
            $url->appendChild($xml->createElement('priority', '0.7'));
            
            $urlset->appendChild($url);
        }

        // 3. Save the File
        $filePath = ROOTDIR . DIRECTORY_SEPARATOR . $filename;
        
        if (file_put_contents($filePath, $xml->saveXML()) !== false) {
            return ['status' => 'success', 'url' => $systemUrl . $filename];
        } else {
            return [
                'status' => 'error', 
                'message' => 'Failed to write file. Ensure your web server has write permissions for: ' . $filePath
            ];
        }

    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}