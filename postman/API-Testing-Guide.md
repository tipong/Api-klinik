# 📋 Panduan Testing API Klinik Management System

## 🚀 Quick Start

### 1. Setup Postman
1. **Import Collection**: Import file `postman/Klinik-API-Collection.postman_collection.json`
2. **Import Environment**: Import file `postman/Klinik-API-Local.postman_environment.json`
3. **Select Environment**: Pilih "Klinik API - Local" sebagai active environment

### 2. Pastikan Server Berjalan
```bash
# Start Laravel server
php artisan serve

# Server akan berjalan di: http://127.0.0.1:8000
```

## 🧪 Daftar Pengujian API

### ✅ **1. Health Check**
**Endpoint**: `GET /api/health`
**Tujuan**: Memastikan API berjalan dengan baik
**Expected Response**:
```json
{
    "status": "success",
    "message": "API is running",
    "timestamp": "2025-06-29T06:49:45.255125Z",
    "version": "1.0.0"
}
```

---

### ✅ **2. Authentication Tests**

#### 2.1 Register User Baru
**Endpoint**: `POST /api/auth/register`
**Body**:
```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890",
    "address": "Jl. Test No. 123",
    "role": "pelanggan"
}
```

#### 2.2 Login dengan berbagai Role
**Endpoint**: `POST /api/auth/login`

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@klinik.com | password123 |
| HRD | hrd@klinik.com | password123 |
| Beautician | beautician@klinik.com | password123 |
| Dokter | dokter@klinik.com | password123 |
| Front Office | frontoffice@klinik.com | password123 |
| Kasir | kasir@klinik.com | password123 |
| Pelanggan | pelanggan@test.com | password123 |

**Expected Response**:
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin Klinik",
            "email": "admin@klinik.com",
            "role": "admin",
            "needs_attendance": false,
            "can_manage_all": true
        },
        "token": "1|HaqIJNyWCVCUfhzun33lYKJtWdI9VLIdECdv2xTQ20795319"
    }
}
```

#### 2.3 Get Profile
**Endpoint**: `GET /api/auth/profile`
**Headers**: `Authorization: Bearer {token}`

#### 2.4 Update Profile
**Endpoint**: `PUT /api/auth/profile`
**Headers**: `Authorization: Bearer {token}`
**Body**:
```json
{
    "name": "Admin Klinik Updated",
    "phone": "081234567899",
    "address": "Jl. Admin No. 1, Jakarta Updated"
}
```

#### 2.5 Change Password
**Endpoint**: `PUT /api/auth/profile`
**Headers**: `Authorization: Bearer {token}`
**Body**:
```json
{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

#### 2.6 Logout
**Endpoint**: `POST /api/auth/logout`
**Headers**: `Authorization: Bearer {token}`

---

### ✅ **3. Role-based Access Control Tests**

#### 3.1 Dashboard Access - Admin/HRD (✅ Should Success)
**Endpoint**: `GET /api/dashboard`
**Headers**: `Authorization: Bearer {admin_token atau hrd_token}`
**Expected**: HTTP 200

#### 3.2 Dashboard Access - Other Roles (❌ Should Fail)
**Endpoint**: `GET /api/dashboard`
**Headers**: `Authorization: Bearer {beautician_token}`
**Expected**: HTTP 403
```json
{
    "status": "error",
    "message": "Forbidden. You do not have permission to access this resource.",
    "required_roles": ["admin", "hrd"],
    "user_role": "beautician"
}
```

#### 3.3 Unauthorized Access (❌ Should Fail)
**Endpoint**: `GET /api/dashboard`
**Headers**: `(No Authorization header)`
**Expected**: HTTP 401
```json
{
    "status": "error",
    "message": "Unauthorized. Please login first."
}
```

---

### ✅ **4. Error Handling Tests**

#### 4.1 Invalid Login Credentials
**Endpoint**: `POST /api/auth/login`
**Body**:
```json
{
    "email": "wrong@email.com",
    "password": "wrongpassword"
}
```
**Expected**: HTTP 401
```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

#### 4.2 Validation Errors - Register
**Endpoint**: `POST /api/auth/register`
**Body**:
```json
{
    "name": "Test User"
}
```
**Expected**: HTTP 422
```json
{
    "status": "error",
    "message": "Validation error",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

#### 4.3 Duplicate Email Registration
**Endpoint**: `POST /api/auth/register`
**Body**:
```json
{
    "name": "Another Admin",
    "email": "admin@klinik.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```
**Expected**: HTTP 422
```json
{
    "status": "error",
    "message": "Validation error",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

---

## 🔧 **Testing Workflow**

### Recommended Testing Order:
1. **Health Check** - Pastikan API berjalan
2. **Login Tests** - Test semua role login
3. **Profile Management** - Test CRUD profile
4. **Role Access Tests** - Test permission setiap role
5. **Error Handling** - Test validasi dan error cases

### Auto Token Management:
Collection sudah dikonfigurasi untuk menyimpan token secara otomatis ke environment variables setelah login berhasil:
- `admin_token`
- `hrd_token`
- `beautician_token`
- `pelanggan_token`
- dll.

### Expected HTTP Status Codes:
- ✅ **200**: Success (GET requests)
- ✅ **201**: Created (POST register)
- ❌ **401**: Unauthorized (no token/invalid token)
- ❌ **403**: Forbidden (wrong role)
- ❌ **422**: Validation Error

---

## 📊 **Test Results Checklist**

### ✅ Authentication
- [ ] Register user baru
- [ ] Login Admin
- [ ] Login HRD  
- [ ] Login Beautician
- [ ] Login Staff lainnya
- [ ] Login Pelanggan
- [ ] Get profile
- [ ] Update profile
- [ ] Change password
- [ ] Logout

### ✅ Authorization
- [ ] Admin access dashboard (✅ success)
- [ ] HRD access dashboard (✅ success)
- [ ] Staff access dashboard (❌ forbidden)
- [ ] Pelanggan access dashboard (❌ forbidden)
- [ ] No token access (❌ unauthorized)

### ✅ Error Handling
- [ ] Invalid credentials
- [ ] Missing required fields
- [ ] Invalid email format
- [ ] Duplicate email
- [ ] Wrong current password
- [ ] Password confirmation mismatch

---

## 🎯 **Tips Testing**

1. **Test secara berurutan**: Mulai dari health check, lalu authentication
2. **Perhatikan token**: Pastikan token tersimpan otomatis setelah login
3. **Check response format**: Semua response harus format JSON konsisten
4. **Test error cases**: Pastikan error handling bekerja dengan baik
5. **Role-based testing**: Test akses setiap role sesuai permission

## 📁 **File Locations**
- Collection: `postman/Klinik-API-Collection.postman_collection.json`
- Environment: `postman/Klinik-API-Local.postman_environment.json`
- Documentation: `postman/API-Testing-Guide.md`

---

**Happy Testing! 🚀**
