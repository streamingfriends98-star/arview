CAMERA VIEW — SINGLE WEBP OVERLAY
=================================

QR flow:
QR scan -> index.html -> camera opens -> live rear-camera view appears -> preview.webp is shown directly as a fixed overlay.

This is NOT AR/WebXR. There is no surface detection, hit-test, 3D placement, AR session, or tap-to-place step.

Replace assets/preview.webp with your own WebP image, keeping the same filename.

IMPORTANT:
- The camera page must be hosted over HTTPS.
- The browser will still require camera permission if it has not already been granted.
- The WebP is displayed as a centered overlay over the live camera feed.
- No Start AR button is required.

QR GENERATOR:
Open setup.html after uploading the project. Enter the published HTTPS URL of index.html and click Generate QR.
