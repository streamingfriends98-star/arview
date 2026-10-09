CAMERA PREVIEW + JSON VISITOR TRACKER
====================================

Files:
- index.html: live rear-camera view with a single WebP overlay
- assets/preview.webp: image displayed over the camera
- track.php: counts page visits and records approximate location
- data/visitors.json: JSON database written by track.php
- visitors-admin.php: simple password-protected visitor report (set your password first)

INSTALLATION
1. Upload all files/folders to a PHP-enabled HTTPS hosting account.
2. Make sure the data/ folder is writable by PHP (typically permissions 755 or 775; use your host's guidance).
3. Open https://yourdomain.com/your-folder/ to test the camera preview.
4. Each page load sends one request to track.php. Tracking errors do not block the camera view.
5. Open visitors-admin.php to view visit count and location records after setting an admin password in that file.

LOCATION AND PRIVACY
- Location is approximate city/region/country based on the visitor's public IP address; it is not GPS or an exact address.
- The public IP address is not saved in the JSON database.
- Approximate lookup uses ip-api.com and may fail due to hosting/network restrictions or its service limits. In that case location is saved as Unknown.
- Add an appropriate privacy notice if this site is used publicly.

JSON DATABASE
The data/visitors.json file stores total_visits and a visits array with timestamp, approximate location, and page name. Back up this file periodically. Do not expose the data folder publicly in production; ideally deny direct HTTP access to it using hosting rules.

CAMERA NOTES
- Must be served over HTTPS.
- The browser requires camera permission when not already granted.
- This is a camera overlay, not AR/WebXR.

VIEW THE TRACKER
Open https://yourdomain.com/your-folder/visitors-admin.php
Before uploading, edit visitors-admin.php and replace CHANGE_THIS_PASSWORD with a strong private password.
Do not share the admin URL/password publicly.
