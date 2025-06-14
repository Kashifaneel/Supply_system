Prompt for Laravel Web App with vue.js and MySQL
Project Name: Supply Management System
Technologies:
- Backend: Laravel (PHP)
- Frontend: vue.js
- Database: MySQL
-
- PDF Generation: Laravel DomPDF
- File Uploads: Laravel Storage
User Roles & Authentication:
- Admin User:
- Default user: admin@example.com, Password: password
- Can add users and assign roles (Admin, User)
- Standard User:
- Can create PO (Purchase Orders) and supply them
Purchase Order (PO) Management:
- Users can enter PO details:
- PO Number, PO Date, PO Image, Institution/Hospital Name, Email, Phone, Address
- Multiple Product Items with Name, Price, Quantity, Batch No, Mfg Date, Expiry Date, Total Amount
Supply Management:
- Users can supply PO (another page)
- Select PO for supply
- Supply Partially or Fully
- Update PO Status:
- Fully Supplied
- Partially Supplied
- If Partially , allow item selection for partial supply
- Once supply is completed partially or fully:
- Generate DC (Delivery Challan) & Invoice (PDF Format)
- Users can view/upload DC & Invoice documents stamped for confirmation
Payment Management:
- Users can create payment for supplied items
- Payment details:
- Cheque No, Amount, Cheque Image
- Admin clicks OK to confirm payment
- Status updates: Complete when Admin verifies payment

Implementation Plan
- Setup Laravel with Sanctum Authentication
- Create Models & Migrations for Users, PO, Supply, Payments
- Build (CRUD for PO, Supply, Payment)
- Pages for Dashboard, PO Management, Supply, DC & Invoice, Payment
- Generate PDF using Laravel DomPDF for DC & Invoice
- Handle File Uploads for Stamped DC & Invoice
- Finalize Admin Approvals & Status Management
