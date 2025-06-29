# ✅ API Testing Checklist

## 🚀 Pre-Testing Setup
- [ ] Laravel server running (`php artisan serve`)
- [ ] Database migrated and seeded (`php artisan migrate:fresh --seed`)
- [ ] Postman collection imported
- [ ] Environment "Klinik API - Local" selected

---

## 🔍 **1. Basic API Tests**

### Health Check
- [ ] `GET /api/health` returns status "success"
- [ ] Response contains timestamp and version
- [ ] HTTP status 200

---

## 🔐 **2. Authentication Tests**

### Registration
- [ ] `POST /api/auth/register` with valid data creates user
- [ ] Returns user data and token
- [ ] HTTP status 201

### Login Tests (All Roles)
- [ ] Admin login (`admin@klinik.com`) ✅
- [ ] HRD login (`hrd@klinik.com`) ✅  
- [ ] Beautician login (`beautician@klinik.com`) ✅
- [ ] Dokter login (`dokter@klinik.com`) ✅
- [ ] Front Office login (`frontoffice@klinik.com`) ✅
- [ ] Kasir login (`kasir@klinik.com`) ✅
- [ ] Pelanggan login (`pelanggan@test.com`) ✅

### Profile Management
- [ ] `GET /api/auth/profile` returns user data
- [ ] `PUT /api/auth/profile` updates user info
- [ ] Password change with correct current password
- [ ] Logout single device works
- [ ] Logout all devices works

---

## 🛡️ **3. Authorization Tests**

### Dashboard Access
- [ ] Admin can access (`GET /api/dashboard`) ✅
- [ ] HRD can access (`GET /api/dashboard`) ✅
- [ ] Beautician CANNOT access (HTTP 403) ❌
- [ ] Dokter CANNOT access (HTTP 403) ❌
- [ ] Front Office CANNOT access (HTTP 403) ❌
- [ ] Kasir CANNOT access (HTTP 403) ❌
- [ ] Pelanggan CANNOT access (HTTP 403) ❌

### Token Validation
- [ ] No token = HTTP 401 (Unauthorized)
- [ ] Invalid token = HTTP 401 (Unauthorized)
- [ ] Valid token + wrong role = HTTP 403 (Forbidden)

---

## ❌ **4. Error Handling Tests**

### Authentication Errors
- [ ] Invalid email/password = HTTP 401
- [ ] Missing required fields = HTTP 422
- [ ] Invalid email format = HTTP 422
- [ ] Duplicate email registration = HTTP 422
- [ ] Password confirmation mismatch = HTTP 422

### Profile Update Errors
- [ ] Wrong current password = HTTP 422
- [ ] Invalid field formats = HTTP 422

### General Errors
- [ ] Consistent JSON error format
- [ ] Proper HTTP status codes
- [ ] Descriptive error messages

---

## 📊 **5. Response Format Validation**

### Success Responses
- [ ] Status: "success"
- [ ] Contains relevant data
- [ ] Consistent structure

### Error Responses  
- [ ] Status: "error"
- [ ] Contains error message
- [ ] Validation errors in "errors" field
- [ ] Consistent structure

---

## 🎯 **6. Role-Based Feature Access**

### Admin Role
- [ ] Full dashboard access ✅
- [ ] No attendance required ✅
- [ ] `can_manage_all: true` ✅
- [ ] `needs_attendance: false` ✅

### HRD Role  
- [ ] Full dashboard access ✅
- [ ] Attendance required ✅
- [ ] `can_manage_all: true` ✅
- [ ] `needs_attendance: true` ✅

### Staff Roles (Beautician, Dokter, etc.)
- [ ] No dashboard access ❌
- [ ] Attendance required ✅
- [ ] `can_manage_all: false` ✅
- [ ] `needs_attendance: true` ✅

### Pelanggan Role
- [ ] No dashboard access ❌
- [ ] No attendance required ❌
- [ ] `can_manage_all: false` ✅
- [ ] `needs_attendance: false` ✅

---

## 🔧 **7. Token Management**

### Auto Token Storage (Postman)
- [ ] Admin token saved to `admin_token`
- [ ] HRD token saved to `hrd_token`
- [ ] Beautician token saved to `beautician_token`
- [ ] Other tokens saved properly

### Token Usage
- [ ] Tokens work for authorized endpoints
- [ ] Tokens properly rejected for unauthorized endpoints

---

## 📋 **8. Data Validation**

### User Data
- [ ] User roles saved correctly
- [ ] Employee records created for staff
- [ ] Phone and address optional fields work
- [ ] Active status properly set

### Employee Data
- [ ] Employee IDs auto-generated
- [ ] Department and position saved
- [ ] Salary components calculated
- [ ] Contract dates handled properly

---

## 🚨 **Critical Tests**

### Security
- [ ] No SQL injection vulnerabilities
- [ ] Password hashing working
- [ ] Token-based auth secure
- [ ] Role restrictions enforced

### Performance
- [ ] Response times acceptable
- [ ] Database queries optimized
- [ ] No memory leaks
- [ ] Proper error handling

---

## ✅ **Testing Status**

**Total Tests**: ~50+ test cases
**Current Status**: 🟢 All basic tests passing

### Completed ✅
- Authentication system
- Role-based access control  
- Error handling
- Token management
- Basic API structure

### Next Phase 🔄
- Attendance endpoints (GPS)
- Recruitment management
- Training system
- Payroll calculations
- File upload handling

---

## 📞 **Issues & Troubleshooting**

### Common Issues:
1. **Server not running**: `php artisan serve`
2. **Database empty**: `php artisan migrate:fresh --seed`
3. **Token not saved**: Check Postman environment
4. **403 Errors**: Verify user role permissions

### Test Environment:
- **Base URL**: http://127.0.0.1:8000/api
- **Database**: SQLite (development)
- **PHP Version**: 8.2+
- **Laravel Version**: 11

---

**Status**: ✅ **API Foundation Ready for Frontend Integration**

Dokumentasi lengkap di: `postman/API-Testing-Guide.md`
