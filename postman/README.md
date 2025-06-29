# 📮 Postman Collection - API Klinik Management System

## 📂 Files dalam folder ini:

### 1. `Klinik-API-Collection.postman_collection.json`
**Collection lengkap untuk testing API Klinik Management System**

**Berisi:**
- 🔐 **Authentication** (10 endpoints)
  - Register, Login semua role, Profile management, Logout
- 📊 **Dashboard & General** (5 endpoints)  
  - Health check, Dashboard access tests
- 🧪 **Role-based Access Tests** (3 endpoints)
  - Test permission berbagai role
- 🔍 **Error Handling Tests** (5 endpoints)
  - Test validasi dan error cases

**Total: 23 endpoints untuk testing komprehensif**

### 2. `Klinik-API-Local.postman_environment.json`
**Environment variables untuk development local**

**Variables:**
- `base_url`: http://127.0.0.1:8000/api
- Token storage untuk setiap role (auto-saved setelah login)
- User ID storage untuk reference

### 3. `API-Testing-Guide.md`
**Panduan lengkap testing API**

**Berisi:**
- Setup instructions
- Test scenarios & expected responses
- Error handling examples
- Testing workflow recommendations
- Checklist untuk validasi

## 🚀 Quick Setup

### 1. Import ke Postman
1. Buka Postman
2. Import Collection: `Klinik-API-Collection.postman_collection.json`
3. Import Environment: `Klinik-API-Local.postman_environment.json`
4. Pilih environment "Klinik API - Local"

### 2. Start Testing
1. Pastikan Laravel server berjalan: `php artisan serve`
2. Test Health Check dulu
3. Login dengan role yang diinginkan
4. Token akan tersimpan otomatis
5. Test endpoints lainnya

## 🧪 Test Coverage

### ✅ **Authentication & Authorization**
- User registration & validation
- Multi-role login system
- Profile management (CRUD)
- Password change functionality
- Token-based authentication
- Role-based access control
- Logout (single & all devices)

### ✅ **Security Testing**
- Invalid credentials handling
- Unauthorized access protection
- Forbidden access (wrong role)
- Input validation
- Token expiration
- SQL injection prevention (built-in Laravel protection)

### ✅ **Error Handling**
- HTTP status codes validation
- Consistent JSON error format
- Field validation errors
- Business logic errors
- Server error handling

### ✅ **Role-based Access**
| Role | Dashboard Access | Attendance Required |
|------|-----------------|-------------------|
| Admin | ✅ Yes | ❌ No |
| HRD | ✅ Yes | ✅ Yes |
| Beautician | ❌ No | ✅ Yes |
| Dokter | ❌ No | ✅ Yes |
| Front Office | ❌ No | ✅ Yes |
| Kasir | ❌ No | ✅ Yes |
| Pelanggan | ❌ No | ❌ No |

## 📋 Default Test Users

| Role | Email | Password | Employee ID |
|------|-------|----------|-------------|
| Admin | admin@klinik.com | password123 | - |
| HRD | hrd@klinik.com | password123 | EMP00001 |
| Beautician | beautician@klinik.com | password123 | EMP00002 |
| Dokter | dokter@klinik.com | password123 | EMP00003 |
| Front Office | frontoffice@klinik.com | password123 | EMP00004 |
| Kasir | kasir@klinik.com | password123 | EMP00005 |
| Pelanggan | pelanggan@test.com | password123 | - |

## 🎯 Testing Best Practices

### 1. **Sequential Testing**
```
Health Check → Authentication → Authorization → Error Cases
```

### 2. **Token Management**
- Token tersimpan otomatis setelah login
- Gunakan token sesuai role untuk testing
- Test logout untuk cleanup

### 3. **Expected Responses**
- Success: Status "success" + data
- Error: Status "error" + message + details
- HTTP codes: 200/201 (success), 401 (unauthorized), 403 (forbidden), 422 (validation)

### 4. **Test Scenarios**
- ✅ Happy path (normal flow)
- ❌ Error path (invalid inputs)
- 🔒 Security path (unauthorized access)
- 🔀 Edge cases (boundary conditions)

## 📊 Test Automation

### Pre-request Scripts
Collection menggunakan scripts untuk:
- Auto-save tokens setelah login
- Set user IDs untuk reference
- Environment variable management

### Test Scripts
Auto-validation untuk:
- Response status codes
- JSON structure validation
- Token extraction & storage
- Error message verification

## 🔧 Troubleshooting

### Common Issues:

1. **Server not running**
   ```bash
   php artisan serve
   ```

2. **Database not seeded**
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Token expired**
   - Login ulang untuk refresh token
   - Check environment variables

4. **Environment not selected**
   - Pastikan "Klinik API - Local" dipilih sebagai active environment

## 📈 Future Enhancements

Collection ini akan di-update seiring pengembangan API:
- ✏️ Attendance endpoints (GPS tracking)
- 🏢 Recruitment management
- 📚 Training system
- 💰 Payroll & salary calculation
- 👥 Employee management
- 📊 Dashboard statistics
- 📄 Reporting endpoints

---

**Happy Testing! 🚀**

Untuk pertanyaan atau issues, silakan check dokumentasi di `API-Testing-Guide.md`
