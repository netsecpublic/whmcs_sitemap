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
* **Permissions:** Write permissions enabled for your root WHMCS directory so the script can successfully save the `sitemap.xml` file.

## 🚀 Installation Instructions

1. **Download the Module:** Download the zipped module file directly from this repository: [whmcs_sitemap.zip](https://github.com/netsecpublic/whmcs_sitemap/blob/main/whmcs_sitemap.zip) *(Click the "Download raw" button on the GitHub page).*
2. **Extract the Files:**
   Extract the `.zip` file on your local computer. You should see a folder named `whmcs_sitemap` containing the `whmcs_sitemap.php` file.
3. **Upload to Your Server:**
   Using FTP or your web hosting control panel, upload the entire `whmcs_sitemap` folder to the `modules/addons/` directory of your WHMCS installation.
   * *Path should look like: `/your-whmcs-dir/modules/addons/whmcs_sitemap/whmcs_sitemap.php`*
4. **Activate the Module:**
   * Log into your WHMCS Admin area.
   * Navigate to **Configuration** (wrench icon) -> **System Settings** -> **Addon Modules**.
   * Find **Sitemap Generator** in the list and click **Activate**.
5. **Configure Permissions:**
   * After activation, click the **Configure** button on the same module.
   * Check the boxes to grant access to your specific administrator roles (e.g., "Full Administrator").
   * Click **Save Changes**.

## ⚙️ How to Use

1. In your WHMCS Admin area, navigate to the **Addons** dropdown menu in the top navigation bar.
2. Select **Sitemap Generator**.
3. You will see an interface explaining the tool. Click the **Generate sitemap.xml** button.
4. A success message will appear with a direct link to your newly generated sitemap. Submit this URL to Google Search Console.

## 🛠️ Troubleshooting

* **File Write Error:** If you get an error that the file could not be written, ensure your WHMCS root directory has the correct ownership and permissions (often `755` or `775` depending on your server environment) to allow PHP to create files.
* **URLs are Returning 404:** Ensure your WHMCS instance is actually set to "Basic URLs" in the General Settings. If you switch to "Full Friendly URLs" in the future, this module will need to be updated to output paths formatted as `/knowledgebase/article/ID/slug`.
* **Sitemap Doesn't Seem to Update:** If you click generate but the file looks unchanged in your browser, your CDN (like Cloudflare) or browser caching is likely serving a stale version. Purge your CDN cache and perform a hard refresh in your browser.
