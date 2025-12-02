# Risk Management Module - Documentation

## Overview
Modul Risk Management adalah sistem komprehensif untuk mengelola risiko, masalah, dan perubahan dalam proyek. Modul ini dirancang untuk membantu tim mengidentifikasi, melacak, dan mengelola risiko proyek secara proaktif.

## Fitur Utama

### 1. Risk List (Daftar Risiko)
**Route:** `/risk`

**Fitur:**
- Tabel interaktif dengan DataTables untuk filtering dan sorting
- Sistem scoring otomatis: `Risk Score = Probabilitas × Dampak`
- Perhitungan level urgensi otomatis:
  - **Critical:** Risk Score 15-25
  - **High:** Risk Score 10-14
  - **Medium:** Risk Score 6-9
  - **Low:** Risk Score 1-5
- Form input risiko dengan validasi
- Statistik real-time (Total Risks, Critical, High, Medium/Low)

**Input Fields:**
- Risk Description (deskripsi risiko)
- Potential Cause (penyebab potensi)
- Affected Module/Feature/Task
- Risk Category: SDM, Technical, Financial, Timeline
- Probability (1-5)
- Impact (1-5)
- Risk Score (auto-calculated)
- Urgency Level (auto-calculated)

### 2. Issue Tracker (Pelacak Masalah)
**Route:** `/risk/issues`

**Fitur:**
- Tracking masalah yang muncul dalam eksekusi
- Penugasan PIC (Person In Charge)
- Priority & Severity level (1-5)
- Status tracking: Open, In Progress, Resolved, Closed
- Link ke Risk terkait
- Link ke Task terkait atau buat task baru
- Upload bukti penyelesaian (screenshot/dokumen)
- Timeline aktivitas
- Filter berdasarkan Status, Priority, Severity, Assignee

**Input Fields:**
- Issue Title
- Issue Description
- Related Risk (optional)
- Related Task (pilih existing atau buat baru)
- Assign To (PIC)
- Deadline
- Priority (1-5)
- Severity (1-5)
- Status (Open/In Progress/Resolved/Closed)
- Resolution Proof (file upload)

### 3. Risk Dashboard
**Route:** `/risk/dashboard`

**Fitur:**
- Overview statistik (Total Risks, Critical Risks, Open Issues, Avg Risk Score)
- **Risk Distribution by Urgency** - Donut Chart
- **Risk by Category** - Donut Chart (Technical, SDM, Financial, Timeline)
- **Risk Trend Over Time** - Line Chart (6 bulan terakhir)
- **Issue Status** - Donut Chart (Open, In Progress, Resolved)
- Tabel Top 5 Critical Risks
- Visual charts menggunakan ApexCharts

### 4. Change Request Management
**Route:** `/risk/change-requests`

**Fitur:**
- Form comprehensive untuk perubahan scope/timeline/budget
- Approval workflow sederhana
- Status tracking: Pending Approval, Approved, Rejected
- Impact analysis (Timeline, Budget, Resource)
- Link ke Risk yang menyebabkan change request
- Rich text editor (Quill) untuk deskripsi detail
- Multi-file upload untuk supporting documents
- Approval actions dengan comments
- Notifikasi dan tracking approval history

**Input Fields:**
- Change Request Title
- Change Type: Scope, Timeline, Budget, Technical, Resource
- Detailed Description (rich text)
- Reason for Change
- Related Risk (optional)
- Business Justification
- Timeline Impact (+/- days/weeks/months)
- Budget Impact (+/- USD)
- Resource Impact
- Affected Modules (multi-select)
- Primary Approver
- Secondary Approver (optional)
- Priority Level

**Approval Workflow:**
1. PM/Team Lead submit Change Request
2. Request masuk ke Primary Approver (e.g., Pak Cahyo)
3. Approver review dan bisa:
   - Approve (timeline/budget otomatis update)
   - Reject dengan alasan
   - Request clarification
4. Jika ada Secondary Approver, lanjut ke tahap berikutnya
5. Setelah disetujui, workload timeline otomatis menyesuaikan

## Technical Stack

### Frontend
- **Bootstrap 5** - UI Framework
- **DataTables** - Interactive tables dengan sorting, filtering, pagination
- **Select2** - Enhanced multi-select dropdowns
- **Flatpickr** - Modern date picker
- **Quill** - WYSIWYG rich text editor
- **ApexCharts** - Beautiful interactive charts
- **SweetAlert2** - Alert/confirmation dialogs (untuk production)
- **jQuery** - DOM manipulation

### Backend (Untuk Future Implementation)
- Laravel 11
- MySQL Database
- Eloquent ORM
- RESTful API

## Routes

```php
Route::prefix('risk')->name('risk.')->group(function () {
    Route::get('/dashboard', 'RiskController@dashboard')->name('dashboard');
    Route::get('/', 'RiskController@index')->name('index');
    Route::get('/issues', 'RiskController@issues')->name('issues');
    Route::get('/change-requests', 'RiskController@changeRequests')->name('change-requests');
});
```

## Sidebar Menu Structure

```
Risk Management
├── Dashboard
├── Risk List
├── Issue Tracker
└── Change Requests
```

## File Structure

```
resources/views/risk/
├── dashboard.blade.php      # Risk Dashboard dengan charts
├── index.blade.php          # Risk List dengan table
├── issues.blade.php         # Issue Tracker
└── change-requests.blade.php # Change Request Management

routes/web.php               # Route definitions

resources/views/layouts/partials/
└── sidebar.blade.php        # Menu sidebar (updated)
```

## Data Flow

### Risk Management Flow
1. **Identifikasi Risiko** → Input ke Risk List
2. **Risk Materialization** → Create Issue di Issue Tracker
3. **Impact Analysis** → Jika besar, create Change Request
4. **Approval** → PM submit → Approver review
5. **Implementation** → Timeline/Budget adjust otomatis

### Automatic Calculations

**Risk Score:**
```javascript
Risk Score = Probability × Impact
```

**Urgency Level:**
```javascript
if (score >= 15) → Critical
else if (score >= 10) → High
else if (score >= 6) → Medium
else → Low
```

## Sample Hardcoded Data

### Risks
- #R001: Database server overload (Score: 20, Critical)
- #R002: Key developer resign (Score: 12, High)
- #R003: Budget overrun (Score: 12, High)
- #R004: API integration delay (Score: 9, Medium)
- #R005: Security vulnerability (Score: 10, High)

### Issues
- #ISS001: Login form validation error (Priority: 5, Severity: 5)
- #ISS002: Database connection timeout (Priority: 4, Severity: 5)
- #ISS003: Missing API documentation (Priority: 3, Severity: 4)
- #ISS004: UI/UX feedback (Priority: 2, Severity: 3)
- #ISS005: Report generation bottleneck (Priority: 4, Severity: 4)

### Change Requests
- #CR001: Extend timeline untuk auth module (+2 weeks, +$5K) - Pending
- #CR002: Add reporting dashboard (+3 weeks, +$12K) - Approved
- #CR003: Change database MySQL → PostgreSQL (+4 weeks, +$20K) - Rejected
- #CR004: Budget increase for resources (+$15K) - Pending
- #CR005: Remove payment gateway (-1 week, -$8K) - Approved

## Color Coding

### Risk Urgency
- **Critical:** Red (#ff4c51)
- **High:** Orange (#ff9f43)
- **Medium:** Cyan (#00cfe8)
- **Low:** Green (#28c76f)

### Issue Status
- **Open:** Warning (Orange)
- **In Progress:** Info (Cyan)
- **Resolved:** Success (Green)
- **Closed:** Secondary (Gray)

### Change Request Status
- **Pending Approval:** Warning (Orange)
- **Approved:** Success (Green)
- **Rejected:** Danger (Red)

### Categories
- **Technical:** Primary (Purple)
- **SDM:** Warning (Orange)
- **Financial:** Success (Green)
- **Timeline:** Info (Cyan)

## Usage Example

### 1. Menambah Risiko Baru
1. Klik tombol "Add New Risk"
2. Isi Risk Description dan Potential Cause
3. Pilih Affected Module dan Category
4. Input Probability (1-5) dan Impact (1-5)
5. Sistem otomatis hitung Risk Score dan Urgency Level
6. Submit form

### 2. Melaporkan Issue
1. Klik tombol "Report New Issue"
2. Pilih Related Risk (jika ada)
3. Pilih Related Task atau buat baru
4. Assign ke PIC
5. Set Priority dan Severity
6. Set deadline
7. Upload resolution proof
8. Submit

### 3. Submit Change Request
1. Klik "Create Change Request"
2. Isi title dan pilih change type
3. Tulis detailed description (rich text)
4. Isi reason dan justification
5. Input impact analysis (timeline, budget, resource)
6. Pilih affected modules
7. Pilih approver(s)
8. Upload supporting documents
9. Submit for approval

### 4. Approve/Reject Change Request
1. Buka Change Request detail
2. Review semua informasi
3. Tambahkan comments
4. Klik "Approve" atau "Reject"
5. Sistem otomatis update timeline/budget jika approved

## Best Practices

1. **Risk Identification:**
   - Identifikasi risiko sedini mungkin
   - Update probability/impact secara berkala
   - Link risk dengan affected modules

2. **Issue Tracking:**
   - Selalu assign PIC dengan deadline jelas
   - Upload resolution proof untuk dokumentasi
   - Update status secara real-time

3. **Change Request:**
   - Berikan business justification yang kuat
   - Impact analysis harus detail dan akurat
   - Attach supporting documents
   - Follow up dengan approver jika pending lama

4. **Dashboard Monitoring:**
   - Review dashboard weekly
   - Prioritas critical risks
   - Track trend untuk early warning

## Future Enhancements

1. **Backend Integration:**
   - Controller untuk CRUD operations
   - Database migrations
   - API endpoints
   - Real-time notifications

2. **Advanced Features:**
   - Email notifications untuk approvals
   - Risk mitigation plans
   - Risk register export (PDF/Excel)
   - Integration dengan project timeline
   - Auto-adjustment workload setelah CR approved
   - Risk heat map visualization
   - Predictive risk analytics

3. **Automation:**
   - Auto-create issue dari risk yang materialized
   - Auto-suggest mitigation based on risk category
   - Auto-escalation untuk overdue issues
   - Integration dengan Slack/Teams

4. **Reporting:**
   - Monthly risk report generation
   - Executive dashboard
   - Risk audit trail
   - Custom report builder

## Access Control (Future)

Untuk production, tambahkan permission-based access:
- `view risks` - Lihat risk list
- `create risks` - Tambah risiko baru
- `edit risks` - Edit existing risks
- `delete risks` - Hapus risks
- `approve change requests` - Approve CR
- `view dashboard` - Akses dashboard

## Notes

- Semua data saat ini adalah **hardcoded** untuk keperluan UI testing
- Untuk production, perlu implementasi backend (Controller, Model, Migration)
- Form validation menggunakan JavaScript (client-side)
- Untuk production, tambahkan server-side validation
- File uploads perlu proper handling di backend
- Approval workflow perlu email notification system

## Support

Untuk pertanyaan atau issue, hubungi:
- Development Team
- Project Manager

---

**Version:** 1.0.0  
**Last Updated:** December 2, 2025  
**Status:** Frontend Complete (Hardcoded Data)
