# Firebase Analytics (GA4) Setup Guide

Status: **Implementation ready for configuration**

---

## Quick Start

### 1. Create Firebase Project & Get Measurement ID

1. **Go to Firebase Console**: https://console.firebase.google.com/
2. **Create New Project**:
   - Project Name: `simpel-landasan-ulin` (or similar)
   - Google Analytics: Enable (optional but recommended)
   - Click "Create Project"

3. **Get Google Analytics Property**:
   - If you enabled Analytics: it creates automatically
   - If not: Go to **Analytics** > **Admin** > **Create Property**
   - Choose Web as platform
   - App name: `SiMPEL Landasan Ulin`
   - Website URL: Your deployment URL (e.g., `http://localhost` for dev, `https://simpel-landasan-ulin.id` for prod)

4. **Copy Measurement ID**:
   - Go to **Admin** > **Property Settings** > **Property Details**
   - Copy **Measurement ID** (format: `G-XXXXXXXXXX`)

---

## Environment Setup

### Development Environment (Local)

**File: `.env`** (your local machine)

```env
# ... existing config ...

# Google Analytics (Firebase)
FIREBASE_MEASUREMENT_ID=G-XXXXXXXXXX
```

Replace `G-XXXXXXXXXX` with your development Firebase Measurement ID from console.

**Quick test locally**:
```bash
php artisan config:clear
php artisan serve
```

Open browser DevTools (F12) → Network tab → refresh page
- Look for request to `www.googletagmanager.com`
- Should see gtag.js loading (200 status)
- Check Console tab for any errors

---

### Staging/Testing Environment

**File: `.env.staging`** (if using separate config)

```env
FIREBASE_MEASUREMENT_ID=G-STAGING_ID_HERE
```

Or use same ID as production to test analytics pipeline.

---

### Production Environment

**File: `.env.production`** (on production server)

```env
FIREBASE_MEASUREMENT_ID=G-PROD_ID_HERE
```

**Important**:
- Use **separate Firebase project** for production (recommended)
- Never hardcode IDs in code — always use `.env`
- Deploy `.env` securely (not via git)

**Create separate GA4 property for prod**:
1. In same Firebase project (or separate), create new property with prod domain
2. Copy prod Measurement ID to `.env.production`

---

## Testing & Verification

### 1. Verify Script Loading

**Local test**:
```bash
# Terminal
php artisan serve

# Browser (while running)
# - Open DevTools (F12)
# - Network tab → Filter: "gtag"
# - Refresh page
# - Should see www.googletagmanager.com request with 200 status
```

### 2. Check Event Firing

**Verify `permohonan_submitted` event**:
1. On local: `http://localhost:8000/layanan`
2. Fill form → Submit
3. Success modal shows → check DevTools Console:
   ```javascript
   dataLayer
   // Should show: [{event: "permohonan_submitted", token: "..."}]
   ```

**Verify `tracking_status_viewed` event**:
1. Go to `http://localhost:8000/cek-status`
2. Enter any valid track_token and submit
3. If permohonan found → check Console → dataLayer shows event

**Verify `surat_downloaded` event**:
1. Same as above, but click "Unduh Surat" button
2. Check Console → dataLayer before file downloads

### 3. Firebase Console Real-Time View

1. Go to Firebase Console → **Analytics** > **Realtime**
2. Do one of the actions above (submit form, check status, download surat)
3. Within 1-2 seconds, event should appear in Realtime view
   - Event name: `permohonan_submitted`, `tracking_status_viewed`, or `surat_downloaded`
   - Event count increments

### 4. Test Empty Measurement ID (No Analytics)

**Simulate no Firebase configured**:
```env
# In .env.testing or .env.local
FIREBASE_MEASUREMENT_ID=
```

Then:
```bash
php artisan config:clear
php artisan serve
```

- Open page in browser
- Network tab should NOT show `googletagmanager.com` request
- Console should have no gtag errors
- Page works normally

---

## Implementation Details

### Files Changed

| File | Change | Reason |
|------|--------|--------|
| `config/services.php` | Added `firebase` config block | Read Measurement ID from env |
| `.env.example` | Added `FIREBASE_MEASUREMENT_ID=` | Template for developers |
| `resources/views/layouts/landing.blade.php` | Added gtag script in `<head>` | Public portal analytics |
| `resources/views/layouts/app.blade.php` | Added gtag script in `<head>` | Admin dashboard analytics |
| `resources/views/services/index.blade.php` | Added success modal + event push | Track form submissions |
| `resources/views/user/permohonan/track.blade.php` | Added 2 events (status view + download) | Track user engagement |

### Script Conditional Loading

All gtag scripts are wrapped with:
```blade
@if(config('services.firebase.measurement_id'))
  <!-- gtag script loads only if ID is set -->
@endif
```

**Why**: Prevents errors in local/testing environments where Measurement ID might be empty.

### Event Structure

All events check if gtag is available:
```javascript
if (typeof gtag !== 'undefined') {
  gtag('event', 'event_name', { param: 'value' });
}
```

**Why**: Safe fallback if gtag fails to load (prevents JS errors).

### Event Parameters (Privacy-Safe)

Events send **only aggregated data**, NO personal identifiers:

| Event | Parameters | NOT sent |
|-------|-----------|----------|
| `permohonan_submitted` | `token` (for dedup only) | NIK, phone, name |
| `tracking_status_viewed` | `jenis_surat`, `status` | track_token, personal data |
| `surat_downloaded` | `jenis_surat` | track_token, file path |

---

## Monitoring & Analytics Dashboard

### Common Queries

**1. Daily Application Submissions**:
- Event: `permohonan_submitted`
- Group by: Date
- Metric: Event count

**2. Most Popular Letter Types**:
- Event: `tracking_status_viewed` or `surat_downloaded`
- Group by: `jenis_surat` parameter
- Metric: Event count

**3. Tracking Engagement**:
- Event: `tracking_status_viewed` vs `surat_downloaded`
- Ratio shows % of people who checked status AND downloaded (vs just checked)

**4. Hourly Traffic Peaks**:
- Any event
- Group by: Hour of day
- Identify peak usage times for capacity planning

---

## Troubleshooting

### gtag Script Not Loading

**Problem**: `www.googletagmanager.com` not in Network tab

**Solutions**:
1. Check `.env` has valid `FIREBASE_MEASUREMENT_ID` (format `G-...`)
2. Run `php artisan config:clear` after `.env` change
3. Hard-refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
4. Check browser console for errors

### Events Not Appearing in Firebase Console

**Problem**: Real-time view empty after triggering event

**Solutions**:
1. **Wait 1-2 seconds** (there's a delay)
2. Ensure `FIREBASE_MEASUREMENT_ID` is correct (copy-paste from console)
3. Check DevTools Console → `dataLayer` array populated?
   ```javascript
   console.log(dataLayer)  // Should show events array
   ```
4. Event name correct? (case-sensitive: `permohonan_submitted`, not `Permohonan_Submitted`)

### Test Environment Getting Production Data

**Problem**: Events in `.env.testing` going to production GA4

**Solution**: Set different ID for testing:
```env
# .env.testing
FIREBASE_MEASUREMENT_ID=G-TEST_ID_HERE
```

Or use same ID but tag events with `environment: 'testing'` parameter.

---

## Production Deployment Checklist

- [ ] Measurement ID copied to `.env.production`
- [ ] `.env.production` deployed securely (not in git)
- [ ] Firebase project created & property set up with prod domain
- [ ] Real-time view opened in Firebase console during deployment
- [ ] At least one test submission goes through on prod
- [ ] Event appears in Firebase Realtime view within 2 seconds
- [ ] No console errors in production (check with browser DevTools on prod)
- [ ] Analytics custom events setup in Firebase (optional: create custom events list for team reference)

---

## Next Steps (Optional Enhancements)

1. **Custom Events in Firebase** (for better organization):
   - Go to **Analytics** > **Events** > **Create event**
   - Define `permohonan_submitted`, `tracking_status_viewed`, `surat_downloaded` as custom events
   - Add descriptions for team reference

2. **Conversion Tracking**:
   - Mark `surat_downloaded` as conversion (equals successful application completion)
   - Track conversion rate over time

3. **Audience Segmentation**:
   - Create audiences for users who submit permohonan but don't download
   - Target with follow-up campaigns

4. **Alerts**:
   - Set alert if `permohonan_submitted` drops below threshold (system issue detection)

5. **Integration with BigQuery** (for advanced analysis):
   - Firebase → BigQuery export (premium feature)
   - Run SQL queries on raw event data

---

## Support

- **Firebase Docs**: https://firebase.google.com/docs/analytics
- **GA4 Setup Guide**: https://support.google.com/analytics/answer/10089681
- **Real-time Event Debugging**: https://firebase.google.com/docs/analytics/debugview
