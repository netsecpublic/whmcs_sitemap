# WHMCS Basic Routing XML Sitemap Generator

Boost your WHMCS SEO with this Google-friendly XML sitemap generator, precision-coded for the **"Basic URLs"** (`index.php?rp=`) routing setup. 

Getting your WHMCS knowledgebase properly indexed by Google is critical for reducing support tickets and driving organic traffic. Standard sitemap generators often fail to account for specific WHMCS routing configurations, resulting in 404 errors, redirect chains, or the accidental exposure of private client-only articles. This lightweight, drop-in addon module is built specifically to solve these exact issues.

## ✨ Features

* **Native Basic URL Routing:** Eliminates 404 errors in Google Search Console by natively generating the exact `index.php?rp=` paths your current WHMCS configuration requires.
* **Smart Content Filtering:** Automatically detects and excludes articles marked as "Private" and articles located within hidden categories to keep client-only data secure.
* **Zero Redirect Chains:** Avoids 301 redirect chains that drain your SEO crawl budget by generating exact destination URLs.
* **Strict XML Formatting:** Escapes special characters seamlessly to prevent XML schema crashes that cause search engines to reject your sitemap.
* **Core Page Inclusion:** Automatically includes and prioritizes your core WHMCS static pages (Homepage, Knowledgebase Index, Announcements, Contact, and Store).
* **One-Click Generation:** A clean, native WHMCS admin interface allows you to regenerate your `sitemap.xml` file instantly.

## ⚠️ Requirements

* **WHMCS Version:** 8.x or later.
* **Routing Configuration:** Your WHMCS installation must be set to use **Basic URLs** (Check this via *System Settings -> General Settings -> General*).
* **Permissions:** Write permissions enabled for your
