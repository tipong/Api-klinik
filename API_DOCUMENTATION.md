# Klinik HRD API Documentation

This document outlines the API endpoints and testing procedures for the HRD & Management System API for the Klinik application.

## Getting Started

1. Clone the repository and navigate to the project directory
2. Install dependencies:
   ```
   composer install
   ```
3. Copy the `.env.example` file to `.env` and configure your database settings
4. Generate application key:
   ```
   php artisan key:generate
   ```
5. Run migrations:
   ```
   php artisan migrate
   ```
6. Seed the database (if needed):
   ```
   php artisan db:seed
   ```
7. Start the server:
   ```
   php artisan serve
   ```

## API Testing with Postman

We've included Postman collection and environment files to help you test the API:

- `Klinik-HRD-API.postman_collection.json` - Contains all API requests
- `Klinik-HRD-API-Local.postman_environment.json` - Environment variables for local testing

### Import to Postman

1. Open Postman
2. Click on "Import" button
3. Select both files to import
4. Select the "Klinik-HRD-API-Local" environment from the environment dropdown

### Testing Workflow

1. Register a new user (or use existing credentials)
2. Login with email and password to get the authentication token (automatically saved to environment)
3. All subsequent authenticated requests will use this token

## API Endpoints

### Authentication

- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Login with email and password to get token
- `GET /api/auth/profile` - Get current user profile
- `PUT /api/auth/profile` - Update user profile
- `POST /api/auth/logout` - Logout

### Pegawai (Employee) Management

- `GET /api/pegawai` - Get all employees
- `GET /api/pegawai/{id}` - Get specific employee
- `POST /api/pegawai` - Create new employee
- `PUT /api/pegawai/{id}` - Update employee
- `DELETE /api/pegawai/{id}` - Delete employee
- `GET /api/pegawai/{id}/absensi` - Get employee attendance records
- `GET /api/pegawai/{id}/gaji` - Get employee salary records
- `GET /api/pegawai/{id}/pelatihan` - Get employee training records

### Absensi (Attendance) Management

- `GET /api/absensi` - Get all attendance records
- `POST /api/absensi` - Create attendance (check-in)
- `GET /api/absensi/{id}` - Get specific attendance record
- `PUT /api/absensi/{id}` - Update attendance (check-out)
- `GET /api/absensi/user/today` - Get current user's today attendance
- `GET /api/absensi/user/history` - Get current user's attendance history

### Gaji (Salary) Management

- `GET /api/gaji` - Get all salary records
- `POST /api/gaji` - Create salary record
- `GET /api/gaji/{id}` - Get specific salary record
- `PUT /api/gaji/{id}` - Update salary record
- `DELETE /api/gaji/{id}` - Delete salary record

### Posisi (Position) Management

- `GET /api/posisi` - Get all positions
- `POST /api/posisi` - Create position
- `GET /api/posisi/{id}` - Get specific position
- `PUT /api/posisi/{id}` - Update position
- `DELETE /api/posisi/{id}` - Delete position

### Lowongan Pekerjaan (Job Posting) Management

- `GET /api/lowongan` - Get all job postings
- `GET /api/lowongan/{id}` - Get specific job posting
- `POST /api/lowongan` - Create job posting
- `PUT /api/lowongan/{id}` - Update job posting
- `DELETE /api/lowongan/{id}` - Delete job posting

### Lamaran Pekerjaan (Job Application) Management

- `GET /api/lamaran` - Get all job applications
- `GET /api/lamaran/{id}` - Get specific job application
- `POST /api/lowongan/apply` - Submit job application
- `PUT /api/lamaran/{id}` - Update job application
- `DELETE /api/lamaran/{id}` - Delete job application

### Wawancara (Interview) Management

- `GET /api/wawancara` - Get all interviews
- `POST /api/wawancara` - Create interview
- `GET /api/wawancara/{id}` - Get specific interview
- `PUT /api/wawancara/{id}` - Update interview
- `DELETE /api/wawancara/{id}` - Delete interview
- `GET /api/lamaran/{id}/wawancara` - Get interviews for specific application

### Hasil Seleksi (Selection Result) Management

- `GET /api/hasil-seleksi` - Get all selection results
- `POST /api/hasil-seleksi` - Create selection result
- `GET /api/hasil-seleksi/{id}` - Get specific selection result
- `PUT /api/hasil-seleksi/{id}` - Update selection result
- `DELETE /api/hasil-seleksi/{id}` - Delete selection result
- `GET /api/lamaran/{id}/hasil` - Get selection result for specific application

### Pelatihan (Training) Management

- `GET /api/pelatihan` - Get all trainings
- `POST /api/pelatihan` - Create training
- `GET /api/pelatihan/{id}` - Get specific training
- `PUT /api/pelatihan/{id}` - Update training
- `DELETE /api/pelatihan/{id}` - Delete training

## Authentication & Authorization

The API uses Laravel Sanctum for authentication. Most endpoints require authentication and appropriate role permissions:

- Public endpoints: Registration, Login, Public job listings
- Admin/HRD endpoints: All management features
- Staff/Pegawai endpoints: Attendance, personal information

## Error Handling

All API responses follow a consistent format:

```json
{
    "status": "success|error",
    "message": "Descriptive message",
    "data": {},
    "errors": {}
}
```

HTTP status codes are used appropriately:
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error
