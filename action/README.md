# Enrollment System Backend Functions

This document describes all the backend functions available in the `/action/` folder.

## Database Setup

First, run the database schema to create all necessary tables:

```sql
-- Execute this file to create the database and tables
mysql -u root -p < action/database_schema.sql
```

## Student Functions (`/action/student/`)

### 1. Queue Management

#### Get Queue (`get_queue.php`)
- **Method**: POST
- **Purpose**: Generate a new queue ticket for a student
- **Input**: 
  ```json
  {
    "department": "Registrar|Treasury|Enrollment",
    "student_id": "2024-61582"
  }
  ```
- **Output**: Queue number, QR code data, expiration time

#### Check Queue Status (`check_queue_status.php`)
- **Method**: GET
- **Purpose**: Check active queue ticket status
- **Input**: `?student_id=2024-61582`
- **Output**: Current queue status, time remaining, QR data

#### Cancel Queue (`cancel_queue.php`)
- **Method**: POST
- **Purpose**: Cancel active queue ticket
- **Input**: 
  ```json
  {
    "student_id": "2024-61582"
  }
  ```

### 2. Registration System

#### Process Registration (`process_registration.php`)
- **Method**: POST
- **Purpose**: Submit college registration form
- **Input**: All form fields from college-registration.php
- **Output**: Generated student ID, registration status

#### Get Provinces (`get_provinces.php`)
- **Method**: GET
- **Purpose**: Get provinces by region code
- **Input**: `?region_code=01`
- **Output**: List of provinces

#### Get Municipalities (`get_municipalities.php`)
- **Method**: GET
- **Purpose**: Get cities/municipalities by province code
- **Input**: `?province_code=0128`
- **Output**: List of cities/municipalities

#### Get Barangays (`get_barangays.php`)
- **Method**: GET
- **Purpose**: Get barangays by city/municipality code
- **Input**: `?citymun_code=012801`
- **Output**: List of barangays

### 3. Enrollment System

#### Get Enrollment Info (`get_enrollment_info.php`)
- **Method**: GET
- **Purpose**: Get student enrollment details, subjects, and assessment
- **Input**: `?student_id=2024-61582`
- **Output**: Student info, enrolled subjects, fee assessment

## Staff/Admin Functions (`/action/student-assistant/`)

### 1. Queue Management for Staff

#### Get Queue List (`get_queue_list.php`)
- **Method**: GET
- **Purpose**: Get current queue for a department
- **Input**: `?department=Registrar`
- **Output**: Queue list, statistics

#### Manage Queue (`manage_queue.php`)
- **Method**: POST
- **Purpose**: Call next, complete, or mark as no-show
- **Input**: 
  ```json
  {
    "ticket_id": 1,
    "action": "call_next|complete|no_show"
  }
  ```

### 2. Registration Management

#### Get Registrations (`get_registrations.php`)
- **Method**: GET
- **Purpose**: Get list of student registrations
- **Input**: `?status=pending&limit=50&offset=0`
- **Output**: Paginated list of registrations

#### Approve Registration (`approve_registration.php`)
- **Method**: POST
- **Purpose**: Approve or reject student registration
- **Input**: 
  ```json
  {
    "registration_id": 1,
    "action": "approve|reject",
    "remarks": "Optional remarks"
  }
  ```

## Database Tables

### Core Tables:
- `student_registrations` - Student registration data
- `queue_tickets` - Queue management system
- `enrollments` - Student enrollment records
- `subjects` - Available subjects/courses
- `enrolled_subjects` - Student-subject relationships
- `student_assessments` - Fee assessments
- `student_payments` - Payment records
- `registration_logs` - Audit trail for registration actions

## Usage Examples

### Frontend Integration

#### 1. Queue Management in Dashboard
```javascript
// Get queue ticket
async function getQueue(department, studentId) {
    const response = await fetch('/action/student/get_queue.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ department, student_id: studentId })
    });
    return await response.json();
}

// Check queue status
async function checkQueueStatus(studentId) {
    const response = await fetch(`/action/student/check_queue_status.php?student_id=${studentId}`);
    return await response.json();
}
```

#### 2. Registration Form
```javascript
// Submit registration form
async function submitRegistration(formData) {
    const response = await fetch('/action/student/process_registration.php', {
        method: 'POST',
        body: formData
    });
    return await response.json();
}

// Load address data
async function loadProvinces(regionCode) {
    const response = await fetch(`/action/student/get_provinces.php?region_code=${regionCode}`);
    return await response.json();
}
```

#### 3. Enrollment View
```javascript
// Get enrollment information
async function getEnrollmentInfo(studentId) {
    const response = await fetch(`/action/student/get_enrollment_info.php?student_id=${studentId}`);
    return await response.json();
}
```

## Security Features

1. **Input Validation**: All inputs are validated and sanitized
2. **SQL Injection Prevention**: Uses prepared statements
3. **XSS Protection**: HTML entities are escaped
4. **Session Management**: Session-based authentication ready
5. **Error Handling**: Comprehensive error handling and logging

## File Structure
```
/action/
├── database_schema.sql
├── student/
│   ├── get_queue.php
│   ├── check_queue_status.php
│   ├── cancel_queue.php
│   ├── process_registration.php
│   ├── get_provinces.php
│   ├── get_municipalities.php
│   ├── get_barangays.php
│   └── get_enrollment_info.php
└── student-assistant/
    ├── get_queue_list.php
    ├── manage_queue.php
    ├── get_registrations.php
    └── approve_registration.php
```

## Next Steps

1. **Authentication System**: Add login/logout functionality
2. **Session Management**: Implement user sessions
3. **Admin Dashboard**: Create admin interface for managing the system
4. **Email Notifications**: Add email notifications for registration status
5. **Payment Integration**: Add payment processing capabilities
6. **Reports**: Generate enrollment and queue reports

All backend functions are now ready to be integrated with your frontend UI!
