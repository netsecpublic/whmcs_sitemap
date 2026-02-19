# WHMCS XML Sitemap Generator

A lightweight, robust WHMCS Addon Module that generates a clean, Google-compliant `sitemap.xml` file. 

This module was specifically developed to solve "Invalid URL" errors in Google Search Console caused by WHMCS's default `?rp=` routing parameters. It strictly utilizes **Friendly URLs** to generate clean, SEO-optimized links for your public knowledgebase and core pages.

## Features

* **Google Search Console Ready:** Avoids `&`, `?`, and `?rp=` parameters that cause XML validation errors.
* **Friendly URL Native:** Formats links using the clean `index.php/knowledgebase/...` structure.
* **Smart Knowledgebase Crawling:** Automatically includes public KB articles while strictly ignoring private (client-only) articles and hidden categories.
* **Core Pages Included:** Automatically maps essential WHMCS pages (Homepage, Store, Announcements, Contact, KB Index) with appropriate SEO priorities.
* **High Compatibility:** Written using standard PHP arrays and loops to prevent `ParseError` exceptions on older server environments (PHP 7.2+).
* **Customizable Filename:** Define your output file (default: `sitemap.xml`) directly from the WHMCS Addon settings.

## Requirements

* WHMCS v7.x or v8.x+
* PHP 7.2 or higher
* **Friendly URLs must be enabled** in WHMCS General Settings.

## Installation

1. Download or clone this repository.
2. Upload the `whmcs_sitemap` folder into your WHMCS directory under `/modules/addons/`.
   * *The final path should look like: `your_whmcs_root/modules/addons/whmcs_sitemap/whmcs_sitemap.php`*
3. Log in to your WHMCS Admin Area.
4. Navigate to **System Settings** -> **Addon Modules** (or **Setup** -> **Addon Modules** in older WHMCS versions).
5. Locate **Sitemap Generator** and click **Activate**.
6. Click **Configure** on the module:
   * Select the Admin Roles that should have access to generate the sitemap.
   * (Optional) Change the default output filename from `sitemap.xml` if needed.
   * Click **Save Changes**.

## Crucial Setup Step: Enable Friendly URLs

For this sitemap generator to create valid links that Google will accept, you must ensure your WHMCS installation is configured to use Friendly URLs.

1. In the WHMCS Admin, go to **System Settings** -> **General Settings**.
2. On the **General** tab, scroll down to **Friendly URLs**.
3. Ensure this is set to **Full Friendly URLs** or **Friendly index.php URLs**. 
[Image of WHMCS Friendly URLs settings panel]
4. Click **Save Changes**.

*Note: If you use Full Friendly URLs, ensure your web server (`.htaccess` for
