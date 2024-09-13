# Telegram Bot Proxy

This project provides a single-file PHP script that acts as a proxy for accessing the Telegram Bot API. It's designed specifically for developers in regions where direct access to Telegram's API might be restricted.

### Benefits

* **Simple Setup:** Just copy the script to your shared hosting and point your domain to it.
* **Improved Access:** Bypass potential geo-restrictions and interact with the Telegram Bot API.
* **Lightweight Docker Image:** For deployment on cloud infrastructure, consider the pre-built Docker image. It provides a minimal environment to run the script efficiently. 


## Usage

**Using Shared Hosting:**

1. **Download:** Download the `index.php` script from this repository.
2. **Upload:** Upload the downloaded script to your shared hosting directory accessible via the web.
3. **Configuration (Optional):**
    * To restrict access to specific bots, set an environment variable named `ALLOWED_BOTS`. 
    * Inside `ALLOWED_BOTS`, list authorized bot access tokens separated by commas (`,`). 
    * Example: `ALLOWED_BOTS=1234567890:XXABCDeFgX1XXb22fY56V3WeNB32bCYnXcCc,9876543210:YYZZaBcCdDeEeFghH11iIjJkKlL`

4. **Access:** 
    * Replace `api.telegram.org` in your Telegram Bot API calls with the URL pointing to your uploaded script.
    * Example (assuming your script is uploaded to `https://bot.yourdomain.com/`):

    ```php
    $url = "https://bot.yourdomain.com/bot1234567890:XXABCDeFgX1XXb22fY56V3WeNB32bCYnXcCc/getMe";
    // ... rest of your API call using $url
    ```

**Using Docker:**

1. **Pull the image:**

    ```bash
    docker pull ghcr.io/yeganemehr/telegram-bot-proxy:latest
    ```

2. **Run the container:**

    * Basic usage:

    ```bash
    docker run -d ghcr.io/yeganemehr/telegram-bot-proxy:latest
    ```

    * With environment variable for bot restriction:

    ```bash
    docker run -d -e ALLOWED_BOTS=1234567890:XXABCDeFgX1XXb22fY56V3WeNB32bCYnXcCc ghcr.io/yeganemehr/telegram-bot-proxy:latest
    ```

3. **Access:** 
    * Use your container's IP address or hostname as the base URL for API calls. The script will be accessible on port 80 by default.
    * Example (assuming container runs on `172.17.0.1`):

    ```php
    $url = "http://172.17.0.1/bot1234567890:XXABCDeFgX1XXb22fY56V3WeNB32bCYnXcCc/getMe";
    // ... rest of your API call using $url
    ```

### Important Notes

* This script acts as a proxy and forwards your requests to Telegram's API servers. 
* Ensure your hosting provider allows remote connections to `api.telegram.org`.
* For security reasons, avoid exposing your actual Telegram Bot API keys publicly.
