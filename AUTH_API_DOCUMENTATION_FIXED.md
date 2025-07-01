# AUTH API DOCUMENTATION - UPDATED

## Database Schema Alignment

The authentication API has been updated to align with the correct `tb_user` database schema:

### `tb_user` Table Structure:
```sql
- id_user (primary key)
- nama_user (string, 255)
- no_telp (string, 255, unique)
- email (string, 255, unique)
- tanggal_lahir (date, nullable)
- password (string, 255)
- foto_profil (string, 255, nullable)
- role (enum: 'pelanggan', 'dokter', 'beautician', 'front office', 'kasir', 'admin', 'hrd')
- remember_token
- created_at, updated_at
```

## API Endpoints

### 1. Register User
**POST** `/api/auth/register`

**Request Body:**
```json
{
    "nama_user": "Test User",
    "no_telp": "081234567999",
    "email": "testuser@example.com", 
    "password": "password123",
    "password_confirmation": "password123",
    "role": "pelanggan",
    "tanggal_lahir": "1990-01-01",
    "foto_profil": "path/to/photo.jpg"
}
```

**Validation Rules:**
- `nama_user`: required, string, max 255 characters
- `no_telp`: required, string, max 255 characters, unique
- `email`: required, valid email, max 255 characters, unique
- `password`: required, min 8 characters, confirmed
- `role`: required, one of: admin, hrd, front office, kasir, dokter, beautician, pelanggan
- `tanggal_lahir`: optional, valid date
- `foto_profil`: optional, string, max 255 characters

**Success Response (201):**
```json
{
    "status": "success",
    "message": "User registered successfully",
    "data": {
        "user": {
            "id_user": 6,
            "nama_user": "Test User",
            "no_telp": "081234567999",
            "email": "testuser@example.com",
            "role": "pelanggan",
            "tanggal_lahir": "1990-01-01T00:00:00.000000Z",
            "foto_profil": null
        },
        "token": "9|PReA9TQHxpcJUHtD55nOIzuwdRBufGPhEzprjq5W504ba92d"
    }
}
```

**Error Response (422):**
```json
{
    "status": "error",
    "message": "Validation error",
    "errors": {
        "nama_user": ["Nama user harus diisi"],
        "no_telp": ["Nomor telepon harus diisi"]
    }
}
```

### 2. Login User
**POST** `/api/auth/login`

**Request Body:**
```json
{
    "email": "testuser@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id_user": 6,
            "nama_user": "Test User",
            "email": "testuser@example.com",
            "no_telp": "081234567999",
            "role": "pelanggan",
            "tanggal_lahir": "1990-01-01T00:00:00.000000Z"
        },
        "token": "10|FfxRlhChdjICgbFtDT3ZJ0yJ9dVn9BjosRhWCzCi211a0358"
    }
}
```

**Error Response (401):**
```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

### 3. Get User Profile
**GET** `/api/auth/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "status": "success",
    "data": {
        "user": {
            "id_user": 6,
            "nama_user": "Test User",
            "email": "testuser@example.com",
            "no_telp": "081234567999",
            "role": "pelanggan",
            "tanggal_lahir": "1990-01-01T00:00:00.000000Z",
            "foto_profil": null,
            "created_at": "2025-07-01T18:44:05.000000Z"
        }
    }
}
```

### 4. Update User Profile
**PUT** `/api/auth/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "nama_user": "Updated Name",
    "no_telp": "081234567888",
    "tanggal_lahir": "1991-01-01",
    "foto_profil": "new/path/photo.jpg",
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Validation Rules:**
- `nama_user`: optional, string, max 255 characters
- `no_telp`: optional, string, max 255 characters, unique (excluding current user)
- `tanggal_lahir`: optional, valid date
- `foto_profil`: optional, string, max 255 characters
- `current_password`: required if changing password
- `password`: optional, min 8 characters, confirmed

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Profile updated successfully",
    "data": {
        "user": {
            "id_user": 6,
            "nama_user": "Updated Name",
            "email": "testuser@example.com",
            "no_telp": "081234567888",
            "role": "pelanggan",
            "tanggal_lahir": "1991-01-01T00:00:00.000000Z",
            "foto_profil": "new/path/photo.jpg"
        }
    }
}
```

### 5. Logout User
**POST** `/api/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Logged out successfully"
}
```

### 6. Logout All Devices
**POST** `/api/auth/logout-all`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Logged out from all devices successfully"
}
```

## Additional User Management Endpoints

### 7. Get All Users (Admin/HRD Only)
**GET** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `role` (optional): Filter by user role (admin, hrd, dokter, etc.)
- `search` (optional): Search by name, email, or phone number
- `per_page` (optional): Number of results per page (default: 15)

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Users retrieved successfully",
    "data": {
        "users": [
            {
                "id_user": 1,
                "nama_user": "Admin HR",
                "email": "admin@klinik.com",
                "no_telp": "081234567890",
                "role": "admin",
                "tanggal_lahir": "1990-01-01T00:00:00.000000Z",
                "foto_profil": null,
                "created_at": "2025-07-01T16:48:02.000000Z",
                "updated_at": "2025-07-01T16:48:02.000000Z"
            },
            {
                "id_user": 2,
                "nama_user": "HRD Manager",
                "email": "hrd@klinik.com",
                "no_telp": "081234567891",
                "role": "hrd",
                "tanggal_lahir": "1985-05-15T00:00:00.000000Z",
                "foto_profil": null,
                "created_at": "2025-07-01T16:48:02.000000Z",
                "updated_at": "2025-07-01T16:48:02.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 1,
            "per_page": 15,
            "total": 11
        }
    }
}
```

**Error Response (403):**
```json
{
    "status": "error",
    "message": "Forbidden. Admin or HRD access required.",
    "user_role": "pelanggan",
    "required_privileges": "admin or hrd"
}
```

### 8. Get User by ID (Admin/HRD Only)
**GET** `/api/user/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Parameters:**
- `id` (required): User ID to retrieve

**Success Response (200):**
```json
{
    "status": "success",
    "message": "User retrieved successfully",
    "data": {
        "user": {
            "id_user": 3,
            "nama_user": "Dokter Ahmad",
            "email": "dokter@klinik.com",
            "no_telp": "081234567892",
            "role": "dokter",
            "tanggal_lahir": "1988-03-20T00:00:00.000000Z",
            "foto_profil": null,
            "created_at": "2025-07-01T16:48:03.000000Z",
            "updated_at": "2025-07-01T16:48:03.000000Z"
        }
    }
}
```

**Error Response (404):**
```json
{
    "status": "error",
    "message": "User not found"
}
```

**Error Response (403):**
```json
{
    "status": "error",
    "message": "Unauthorized. Only admin or HRD can view user details."
}
```

## Testing Examples

### Register Admin User
```bash
curl -X POST "http://127.0.0.1:8001/api/auth/register" \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-d '{
    "nama_user": "Admin User",
    "no_telp": "081234567890",
    "email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
}'
```

### Register HRD User
```bash
curl -X POST "http://127.0.0.1:8001/api/auth/register" \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-d '{
    "nama_user": "HRD Manager",
    "no_telp": "081234567891",
    "email": "hrd@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "hrd",
    "tanggal_lahir": "1985-05-15"
}'
```

### Get All Users with Filter
```bash
# Get all admin users
curl -X GET "http://127.0.0.1:8001/api/users?role=admin" \
-H "Authorization: Bearer {admin_token}" \
-H "Accept: application/json"

# Search users by name
curl -X GET "http://127.0.0.1:8001/api/users?search=Ahmad" \
-H "Authorization: Bearer {admin_token}" \
-H "Accept: application/json"

# Get users with pagination
curl -X GET "http://127.0.0.1:8001/api/users?per_page=5" \
-H "Authorization: Bearer {admin_token}" \
-H "Accept: application/json"
```

### Get Specific User
```bash
# Get user with ID 1
curl -X GET "http://127.0.0.1:8001/api/user/1" \
-H "Authorization: Bearer {admin_token}" \
-H "Accept: application/json"

# Test with HRD access
curl -X GET "http://127.0.0.1:8001/api/user/3" \
-H "Authorization: Bearer {hrd_token}" \
-H "Accept: application/json"
```

### Access Control Testing
```bash
# Test with regular user (should fail)
curl -X GET "http://127.0.0.1:8001/api/users" \
-H "Authorization: Bearer {customer_token}" \
-H "Accept: application/json"
# Expected: 403 Forbidden
```

## Access Control Summary

### Allowed Roles:
- **Admin**: Full access to all user management endpoints
- **HRD**: Full access to all user management endpoints (same as admin)

### Restricted Roles:
- **Dokter, Beautician, Front Office, Kasir, Pelanggan**: Cannot access user management endpoints

## Features Implemented:

1. **Complete User Listing**: Get all users with pagination and filtering
2. **User Details**: Get detailed information for specific users
3. **Role-based Access Control**: Only admin and HRD can access these endpoints
4. **Search and Filter**: Support for role filtering and text search
5. **Pagination**: Efficient handling of large user datasets
6. **Proper Error Handling**: Clear error messages for unauthorized access
7. **Database Schema Compliance**: All responses use correct field names

The user management system now provides comprehensive functionality for admin and HRD users to manage and view user information while maintaining proper security controls.
