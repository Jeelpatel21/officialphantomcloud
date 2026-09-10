## 📊 Sheet & Web App Details
- **Google Sheet URL**: [Open Google Sheet](https://docs.google.com/spreadsheets/d/1KivF1gWUzPHOsw3tLeBOrgunsdEjo5-UJLTjJRHyKuA/edit?usp=sharing)
- **Spreadsheet ID**: `1KivF1gWUzPHOsw3tLeBOrgunsdEjo5-UJLTjJRHyKuA`
- **Web App URL**: `https://script.google.com/macros/s/AKfycbyyuRfMctsR2uUZtomahosGyB7pdbV1j-8EdNVFG1goCGBT4q7EvijnkNPHcjvT7MLQ/exec`
- **Status**: ✅ **Active & Verified**

---

## 🛠️ Recommended Setup (Direct Form-to-Sheet via Google Apps Script)

To automatically record submissions from `contact.html` into this Google Sheet:

### Step 1: Set Up Sheet Columns
Open the sheet and add these headers in **Row 1**:
| A | B | C | D | E | F | G |
|---|---|---|---|---|---|---|
| **Timestamp** | **Name** | **Email** | **Phone** | **Service** | **Budget** | **Message** |

---

### Step 2: Add Google Apps Script
1. Inside your Google Sheet, click **Extensions** > **Apps Script**.
2. Replace any existing code with the following snippet:

```javascript
function doPost(e) {
  var lock = LockService.getScriptLock();
  lock.tryLock(10000);

  try {
    // Explicitly target your exact Phantom Cloud sheet by ID
    var SPREADSHEET_ID = "1KivF1gWUzPHOsw3tLeBOrgunsdEjo5-UJLTjJRHyKuA";
    var doc = SpreadsheetApp.openById(SPREADSHEET_ID);
    var sheet = doc.getSheetByName("Sheet1") || doc.getSheets()[0];

    var nextRow = sheet.getLastRow() + 1;
    var rowData = [
      new Date(),
      e.parameter.name || '',
      e.parameter.email || '',
      e.parameter.phone || '',
      e.parameter.service || '',
      e.parameter.budget || '',
      e.parameter.message || ''
    ];

    sheet.getRange(nextRow, 1, 1, rowData.length).setValues([rowData]);

    return ContentService
      .createTextOutput(JSON.stringify({ result: 'success', row: nextRow }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (error) {
    return ContentService
      .createTextOutput(JSON.stringify({ result: 'error', error: error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  } finally {
    lock.releaseLock();
  }
}
```

3. Click **Save** (disk icon).

---

### Step 3: Deploy as Web App
1. Click **Deploy** > **New deployment**.
2. Select type: **Web app**.
3. Set description: `Phantom Cloud Contact Form`.
4. Set **Execute as**: `Me`.
5. Set **Who has access**: `Anyone`.
6. Click **Deploy** and authorize access when prompted.
7. Copy the generated **Web App URL** (looks like `https://script.google.com/macros/s/.../exec`).

---

### Step 4: Add the Web App URL to the Project
Paste that URL into [sheet_config.json](file:///c:/Users/Jeel/Downloads/Workbook%20Images/Phantom%20Cloud/Phantom%20Cloud/sheet_config.json) under `"web_app_url"`.
Whenever you're ready, we can update [script.js](file:///c:/Users/Jeel/Downloads/Workbook%20Images/Phantom%20Cloud/Phantom%20Cloud/script.js) or [contact.php](file:///c:/Users/Jeel/Downloads/Workbook%20Images/Phantom%20Cloud/Phantom%20Cloud/contact.php) to send form data directly to that URL without needing MySQL!
