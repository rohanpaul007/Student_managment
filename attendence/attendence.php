<?php
include_once "models/Database.php";
include_once "models/studentClass.php";

$student = new Students();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['h_action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['h_action']) {
            case 'update_attendance':
                $attendance_status = $_POST['status'];
                $student_id = $_POST['student_id'];
                $date = $_POST['date'] ?? date('Y-m-d');
                
                if (empty($student_id) || !in_array($attendance_status, ['Present', 'Absent'])) {
                    throw new Exception("Invalid input data");
                }
                
                $result = $student->updateAttendance($student_id, $attendance_status, $date);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Attendance updated successfully',
                        'status' => $attendance_status
                    ]);
                } else {
                    throw new Exception("Database update failed");
                }
                exit;
                
            case 'get_attendance':
                $date = $_POST['date'] ?? date('Y-m-d');
                $attendance_report = $student->getAttendanceReport($date);
                echo json_encode([
                    'success' => true,
                    'data' => $attendance_report
                ]);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Initial page load// In your controller code, ensure the date format matches your database:
$attendance_date = $_GET['date'] ?? date('Y-m-d');
$attendance_report = $student->getAttendanceReport($attendance_date);

// Debug output
error_log("Requested Date: ".$attendance_date);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Attendance Management System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">



    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@100..900&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
    .attendance-option {
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .attendance-option:hover {
        background-color: #f8f9fa;
    }

    .present-checked {
        background-color: #d4edda;
    }

    .absent-checked {
        background-color: #f8d7da;
    }

    .loading {
        background-color: #fff3cd;
    }
    </style>
</head>

<body>
    <button class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="main-container d-flex">
        <div class="sidebar_container">
            <ul class="side_menu">
                <i class="fa fa-arrow-right"></i>
                <li><a href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="student_managment.php"><i class="fa-solid fa-user-graduate"></i> Manage Students</a></li>
                <li><a href="attendence.php" class="active"><i class="fa-solid fa-clipboard-check"></i> Attendance</a>
                </li>
                <li><a href="class_managment.php"><i class="fa-solid fa-chalkboard-teacher"></i> Class Management</a>
                </li>
                <li><a href="#"><i class="fa-solid fa-user-tie"></i> Teachers</a></li>
                <i class="fa fa-arrow-right"></i>
            </ul>
        </div>

        <div class="content w-100">
            <div class="section_student_div text-center py-3">
                <h1>Attendance Management System</h1>
            </div>

            <section class="class_managment ml-5">
                <div class="container-fluid">
                    <!-- Date Selector Card -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-12 col-md-8 col-lg-6">
                            <div class="card shadow-sm w-100" >
                                <div class="card-body w-100">
                                    <label for="attendance_date" class="form-label fw-bold">Select Date:</label>
                                    <input type="date" id="attendance_date" class="form-control"
                                        value="<?= $attendance_date ?>" max="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Cards Grid -->
                    <div class="row g-4" id="attendanceCardsContainer">
                        <?php foreach($attendance_report as $index => $data): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm student-card w-100 mb-3" data-student-id="<?= $data['id'] ?>">
                                <div class="card-body text-center p-3">
                                    <!-- Student Image -->
                                    <div class="mb-3">
                                        <img src="uploads/<?= $data['image'] ?? 'default.png' ?>" alt="Student Photo"
                                            class="rounded-circle border"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>

                                    <!-- Student Info -->
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($data['student_name']) ?></h5>
                                    <div class="text-muted small mb-2">
                                        Roll: <?= $data['student_roll'] ?> |
                                        <?= $data['student_grade'] ?>-<?= $data['student_section'] ?>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="mb-3">
                                        <span
                                            class="badge bg-<?= ($data['status'] ?? '') == 'Present' ? 'success' : 'danger' ?> p-2">
                                            <?= $data['status'] ?? 'Not Marked' ?>
                                        </span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-2 d-md-flex justify-content-center">
                                        <button class="btn btn-sm btn-success mark-present flex-grow-1"
                                            data-student-id="<?= $data['id'] ?>">
                                            <i class="fas fa-check me-1"></i> Present
                                        </button>
                                        <button class="btn btn-sm btn-danger mark-absent flex-grow-1"
                                            data-student-id="<?= $data['id'] ?>">
                                            <i class="fas fa-times me-1"></i> Absent
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
// Update the JavaScript to handle card layout
$(document).ready(function() {
    // Handle date change
    $('#attendance_date').change(function() {
        const date = $(this).val();
        loadAttendanceData(date);
    });

    // Handle present/absent clicks - UPDATED VERSION
    $(document).on('click', '.mark-present, .mark-absent', function() {
        const button = $(this);
        const card = button.closest('.student-card');
        const studentId = card.data('student-id');
        const status = button.hasClass('mark-present') ? 'Present' : 'Absent';
        
        // INSTANTLY UPDATE UI FIRST
        card.find('.badge')
            .removeClass('bg-success bg-danger')
            .addClass(status === 'Present' ? 'bg-success' : 'bg-danger')
            .text(status);
            
        // Then send to server
        updateAttendance(studentId, status, card);
    });

    // Modified loadAttendanceData function for cards
    function loadAttendanceData(date) {
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: {
                h_action: 'get_attendance',
                date: date
            },
            dataType: 'json',
            beforeSend: function() {
                $('#attendanceCardsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading attendance data...</p>
                    </div>
                `);
            },
            success: function(response) {
                if (response.success) {
                    renderAttendanceCards(response.data);
                } else {
                    showToast('Failed to load attendance data', 'danger');
                }
            },
            error: function() {
                showToast('Error loading attendance data', 'danger');
            }
        });
    }

    // Function to render cards
    function renderAttendanceCards(data) {
        let html = '';
        
        if (data.length === 0) {
            html = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No attendance data found for selected date
                    </div>
                </div>
            `;
        } else {
            data.forEach((student) => {
                html += `
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm student-card" data-student-id="${student.id}">
                        <div class="card-body text-center p-3">
                            <div class="mb-3">
                                <img src="uploads/${student.image || 'default.png'}" 
                                     class="rounded-circle border" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            
                            <h5 class="card-title mb-1">${escapeHtml(student.student_name)}</h5>
                            <div class="text-muted small mb-2">
                                Roll: ${student.student_roll} | 
                                ${student.student_grade}-${student.student_section}
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge bg-${(student.status || '') === 'Present' ? 'success' : 'danger'} p-2">
                                    ${student.status || 'Not Marked'}
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-center">
                                <button class="btn btn-sm btn-success mark-present flex-grow-1">
                                    <i class="fas fa-check me-1"></i> Present
                                </button>
                                <button class="btn btn-sm btn-danger mark-absent flex-grow-1">
                                    <i class="fas fa-times me-1"></i> Absent
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });
        }
        
        $('#attendanceCardsContainer').html(html);
    }

    // Optimized updateAttendance function
    function updateAttendance(studentId, status, card) {
        const date = $('#attendance_date').val();
        const buttons = card.find('.btn');
        
        // Disable buttons during update
        buttons.prop('disabled', true);
        card.addClass('loading');
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: {
                h_action: 'update_attendance',
                student_id: studentId,
                status: status,
                date: date
            },
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    // If server update failed, revert the UI change
                    const currentStatus = card.find('.badge').text();
                    const oppositeStatus = status === 'Present' ? 'Present' : 'Absent';
                    
                    if (currentStatus === status) {
                        card.find('.badge')
                            .removeClass('bg-success bg-danger')
                            .addClass(oppositeStatus === 'Present' ? 'bg-success' : 'bg-danger')
                            .text(oppositeStatus);
                    }
                    
                    showToast(response.message || 'Update failed', 'danger');
                }
            },
            error: function(xhr) {
                // Revert UI change on error
                const oppositeStatus = status === 'Present' ? 'Present' : 'Absent';
                card.find('.badge')
                    .removeClass('bg-success bg-danger')
                    .addClass(oppositeStatus === 'Present' ? 'bg-success' : 'bg-danger')
                    .text(oppositeStatus);
                    
                showToast('Network error occurred', 'danger');
            },
            complete: function() {
                buttons.prop('disabled', false);
                card.removeClass('loading');
            }
        });
    }

    // Utility functions
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showToast(message, type = 'info') {
        // Create a simple toast notification
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'danger' ? 'danger' : 'success'} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Remove toast after it hides
        toast.on('hidden.bs.toast', function() {
            toast.remove();
        });
    }
});
</script>
</body>

</html>