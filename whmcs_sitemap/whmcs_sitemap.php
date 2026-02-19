<?php
/**
 * WHMCS XML Sitemap Generator
 * Generates a Google-Friendly sitemap.xml for WHMCS
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

function whmcs_sitemap_config() {
    return array(
        'name' => 'Sitemap Generator',
        'description' => 'Generates a Google-friendly sitemap.xml using Friendly index.php URLs.',
        'author' => 'System Admin',
        'language' => 'english',
        'version' => '1.7',
        'fields' => array(
            'sitemap_filename' => array(
                'FriendlyName' => 'Sitemap Filename',
                'Type' => 'text',
                'Size' => '25',
                'Default' => 'sitemap.xml',
                'Description' => 'The name of the file to be generated in your WHMCS root.',
            )
        )
    );
}

function whmcs_sitemap_activate() {
    return array('status' => 'success', 'description' => 'Activated successfully.');
}

function whmcs_sitemap_deactivate() {
    return array('status' => 'success', 'description' => 'Deactivated successfully.');
}

function whmcs_sitemap_output($vars) {
    $modulelink = $vars['modulelink'];
    $filename = empty($vars['sitemap_filename']) ? 'sitemap.xml' : $vars['sitemap_filename'];
    
    if (isset($_POST['generate_sitemap'])) {
        $result = whmcs_sitemap_build_xml($filename);
        if ($result['status'] === 'success') {
            echo '<div class="alert alert-success"><strong>Success!</strong> Sitemap generated: <a href="' . $result['url'] . '" target="_blank">' . $result['url'] . '</a></div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error:</strong> ' . $result['message'] . '</div>';
        }
    }

    echo '<h2>XML Sitemap Generator</h2>';
    echo '<form method="post" action="' . $modulelink . '">';
    echo '<input type="hidden" name="generate_sitemap" value="1">';
    echo '<button type="submit" class="btn btn-primary">Generate Sitemap</button>';
    echo '</form>';
}

function whmcs_sitemap_build_xml($filename) {
    try {
        $systemUrl = \WHMCS\Config\Setting::getValue('SystemURL');
        if (empty($systemUrl)) {
            throw new Exception("System URL is not configured in WHMCS General Settings.");
        }
        $systemUrl = rtrim(trim($systemUrl), '/') . '/';

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        $urlset = $xml->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($urlset);

        $date = date('Y-m-d');

        $staticPages = array(
            array('path' => '', 'priority' => '1.0'),
            array('path' => 'knowledgebase.php', 'priority' => '0.8'),
            array('path' => 'announcements.php', 'priority' => '0.8'),
            array('path' => 'contact.php', 'priority' => '0.6'),
            array('path' => 'store', 'priority' => '0.8')
        );

        foreach ($staticPages as $page) {
            $url = $xml->createElement('url');
            $loc = $xml->createElement('loc');
            $locUrl = $systemUrl . $page['path'];
            $loc->appendChild($xml->createTextNode($locUrl));
            $url->appendChild($loc);
            $url->appendChild($xml->createElement('lastmod', $date));
            $url->appendChild($xml->createElement('changefreq', 'weekly'));
            $url->appendChild($xml->createElement('priority', $page['priority']));
            $urlset->appendChild($url);
        }

        $kbArticles = Capsule::table('tblknowledgebase')
            ->join('tblknowledgebaselinks', 'tblknowledgebase.id', '=', 'tblknowledgebaselinks.articleid')
            ->join('tblknowledgebasecats', 'tblknowledgebaselinks.categoryid', '=', 'tblknowledgebasecats.id')
            ->where('tblknowledgebasecats.hidden', '!=', 'on')
            ->select('tblknowledgebase.id', 'tblknowledgebase.title', 'tblknowledgebase.private')
            ->distinct()
            ->get();

        foreach ($kbArticles as $article) {
            // Skip private (client-only) articles
            if ($article->private == 'on') {
                continue;
            }

            $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', trim($article->title));
            $slug = trim($slug, '-');
            
            // Format for WHMCS "Friendly index.php" mode (No ?rp= or & characters)
            $articleUrl = $systemUrl . "index.php/knowledgebase/" . $article->id . "/" . $slug . ".html";

            $url = $xml->createElement('url');
            $loc = $xml->createElement('loc');
            $loc->appendChild($xml->createTextNode($articleUrl));
            $url->appendChild($loc);
            $url->appendChild($xml->createElement('changefreq', 'monthly'));
            $url->appendChild($xml->createElement('priority', '0.7'));
            $urlset->appendChild($url);
        }

        $filePath = ROOTDIR . DIRECTORY_SEPARATOR . $filename;
        
        if (file_put_contents($filePath, $xml->saveXML()) !== false) {
            return array('status' => 'success', 'url' => $systemUrl . $filename);
        } else {
            return array('status' => 'error', 'message' => 'Failed to write file to: ' . $filePath);
        }

    } catch (\Exception $e) {
        return array('status' => 'error', 'message' => $e->getMessage());
    }
}
