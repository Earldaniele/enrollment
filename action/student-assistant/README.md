# Student Assistant Backend System - Technical Documentation

## Overview
The Student Assistant module provides backend functionality for managing student queues, processing QR codes, and maintaining real-time dashboard data. The system is built using PHP + MySQL and designed to work offline with XAMPP or similar local environments.

## Database Structure

### Existing Tables (No Changes Required)
The system uses the existing `enrollment_db` database structure:

#### `queue_tickets` Table
```sql
CREATE TABLE `queue_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `department` enum('Registrar','Treasury','Enrollment') NOT NULL,
  `queue_number` varchar(10) NOT NULL,
  `qr_data` text DEFAULT NULL,
  `status` enum('waiting','in_progress','completed','cancelled','expired') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

#### `student_registrations` Table
```sql
CREATE TABLE `student_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  -- ... other fields
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## Backend API Endpoints

### 1. Dashboard Data Retrieval
**Endpoint:** `GET /action/student-assistant/get_queue_list.php`

**Purpose:** Retrieves real-time queue statistics and current queue list for the dashboard.

**Response:**
```json
{
  "success": true,
  "stats": {
    "waiting": 5,
    "in_progress": 2,
    "completed": 12,
    "no_show": 1,
    "expired": 0,
    "total": 20
  },
  "queue": [
    {
      "id": 1,
      "student_id": "2025-00001",
      "queue_number": "RG-001",
      "status": "waiting",
      "department": "Registrar",
      "student_name": "Juan Dela Cruz",
      "created_at": "2025-01-20 09:00:00"
    }
  ]
}
```

### 2. Queue Ticket Verification
**Endpoint:** `GET /action/student-assistant/verify_queue.php`

**Parameters:**
- `student_id` (required): Student ID to verify
- `queue_id` (optional): Specific queue ticket ID

**Purpose:** Verifies student queue tickets and returns student information.

**Response:**
```json
{
  "success": true,
  "message": "Student found with active queue ticket",
  "ticket": {
    "id": 1,
    "student_id": "2025-00001",
    "queue_number": "RG-001",
    "status": "waiting",
    "department": "Registrar"
  },
  "student": {
    "student_id": "2025-00001",
    "student_name": "Juan Dela Cruz",
    "status": "approved"
  }
}
```

### 3. Queue Management
**Endpoint:** `POST /action/student-assistant/manage_queue.php`

**Purpose:** Updates queue ticket statuses (call next, complete, mark as no-show).

**Request Body:**
```json
{
  "ticket_id": 1,
  "action": "call_next"
}
```

**Actions Available:**
- `call_next`: Changes status from 'waiting' to 'in_progress'
- `complete`: Changes status from 'in_progress' to 'completed'
- `no_show`: Changes status from 'waiting' or 'in_progress' to 'cancelled'

**Response:**
```json
{
  "success": true,
  "message": "Student has been called successfully",
  "ticket_id": 1,
  "old_status": "waiting",
  "new_status": "in_progress"
}
```

### 4. QR Code Image Upload Processing
**Endpoint:** `POST /action/student-assistant/process_qr_upload.php`

**Purpose:** Processes uploaded QR code images (placeholder functionality for demonstrations).

**Request:** Multipart form data with `qr_file` field.

**Response:**
```json
{
  "success": true,
  "message": "QR Code processed successfully",
  "ticket": {
    "id": 1,
    "student_id": "2025-00001",
    "queue_number": "RG-001",
    "status": "waiting"
  },
  "student": {
    "student_id": "2025-00001",
    "student_name": "Juan Dela Cruz"
  },
  "upload_info": {
    "filename": "qr_code.jpg",
    "size": 24576,
    "type": "image/jpeg"
  }
}
```

### 5. QR Code Camera Scan Processing
**Endpoint:** `POST /action/student-assistant/process_qr_scan.php`

**Purpose:** Processes QR code data from live camera scanning.

**Request Body:**
```json
{
  "qr_data": {
    "student_id": "2025-00001",
    "queue_id": 1,
    "department": "registrar",
    "timestamp": 1705747200
  }
}
```

**Response:** Same format as QR upload processing.

### 6. Demo Data Generation
**Endpoint:** `POST /action/student-assistant/generate_demo_data.php`

**Purpose:** Generates sample queue tickets for testing and demonstration purposes.

**Response:**
```json
{
  "success": true,
  "message": "Demo data generated successfully",
  "tickets_generated": 8,
  "tickets": [
    {
      "student_id": "2025-00001",
      "student_name": "Juan Dela Cruz",
      "department": "Registrar",
      "queue_number": "RG-001",
      "status": "waiting"
    }
  ]
}
```

## System Architecture

### Data Flow
1. **Dashboard Load**: Frontend calls `get_queue_list.php` to populate statistics and queue table
2. **QR Processing**: Both camera scanning and file upload use the same verification logic
3. **Queue Management**: Status updates trigger real-time dashboard refresh
4. **Error Handling**: All endpoints return consistent error responses

### Database Relationships
- `queue_tickets.student_id` → `student_registrations.student_id`
- Queue statuses follow a logical flow: waiting → in_progress → completed
- Expired tickets are automatically marked based on `expires_at` timestamp

### Security Features
- Input validation and sanitization
- Prepared statements to prevent SQL injection
- File upload validation (type, size)
- Session-based authentication (can be enhanced for production)

## Implementation Notes

### Offline Operation
- No external API dependencies
- All processing happens locally
- Database operations use local MySQL instance

### QR Code Processing
- **File Upload**: Simulates QR code scanning for demonstration
- **Camera Scanner**: Processes live QR code data
- Both methods use the same backend verification logic

### Real-time Updates
- Dashboard refreshes every 30 seconds
- Manual refresh buttons available
- Queue actions immediately update statistics

### Error Handling
- Invalid QR codes return appropriate error messages
- Database connection failures are gracefully handled
- File upload errors include specific validation messages

## Frontend Integration

### Dashboard Components
- **Statistics Cards**: Display real-time counts from `get_queue_list.php`
- **Queue Table**: Shows current queue from same endpoint
- **QR Scanner**: Integrates with both upload and camera endpoints
- **Action Buttons**: Use `manage_queue.php` for status updates

### JavaScript Functions
- `loadQueueData()`: Fetches dashboard data
- `verifyQueueTicket()`: Processes QR codes
- `performQueueAction()`: Updates ticket statuses
- Auto-refresh timer for real-time updates

## Testing and Development

### Demo Mode
- Generate sample data using `generate_demo_data.php`
- Test QR processing with any image file
- Simulate queue management actions

### Database Testing
- Verify existing student data
- Test queue ticket creation
- Validate status transitions

### Error Scenarios
- Invalid student IDs
- Expired queue tickets
- Network failures
- File upload errors

## Production Considerations

### Security Enhancements
- Implement proper user authentication
- Add CSRF protection
- Enable HTTPS for camera access
- Add rate limiting

### Performance Optimization
- Add database indexes for frequently queried fields
- Implement caching for dashboard data
- Optimize database queries for large datasets

### Monitoring
- Add logging for all queue operations
- Monitor database performance
- Track QR code processing success rates

## Troubleshooting

### Common Issues
1. **Database Connection**: Check `db_config.php` settings
2. **File Uploads**: Verify PHP upload limits and permissions
3. **QR Processing**: Ensure proper JSON format in QR codes
4. **Queue Updates**: Check database transaction handling

### Debug Mode
- Enable error reporting in development
- Check browser console for JavaScript errors
- Verify API endpoint responses

---

**Note**: This system is designed for demonstration and can be enhanced for production use with additional security measures and performance optimizations.
