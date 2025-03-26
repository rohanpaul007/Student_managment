<?php
include_once "models/Database.php";
include_once "models/studentClass.php";

$student = new Students();

// Handle form actions (update/delete)
$h_action = '';
$data_id = '';

if (isset($_POST['h_action'])) {
    $h_action = $_POST['h_action'];
}

if (isset($_POST['h_id'])) {
    $data_id = $_POST['h_id'];
}

if ($h_action == 'update') {
    $update_student_name = $_POST['student_name_' . $data_id];
    $update_student_section = $_POST['student_section_' . $data_id];
    $update_student_roll = $_POST['student_roll_' . $data_id];
    $update_student_grade = $_POST['student_grade_' . $data_id];

    // Get the existing image from the database
    $existing_student = $student->getStudentById($data_id);
    $existing_image = $existing_student['image']; // Fetch existing image

    $update_student_image = $_FILES['student_image_' . $data_id]['name'];

    if (!empty($update_student_image)) {
        $target_dir = "uploads/"; // Make sure the directory exists
        $target_file = $target_dir . basename($update_student_image);
        move_uploaded_file($_FILES['student_image_' . $data_id]["tmp_name"], $target_file);
    } else {
        $update_student_image = $existing_image; // Keep old image if no new image is uploaded
    }

    $update_student_date = $_POST['student_date_' . $data_id];
    $result = $student->UpdateData($update_student_name, $update_student_section, $update_student_roll, $update_student_grade, $update_student_image, $update_student_date, $data_id);

    if ($result) {
        echo "data successfully updated";
    } else {
        echo "failed to update data";
    }
}

if ($h_action == 'delete') {
    $output = $student->Deletedata($data_id);
    if ($output) {
        echo "data successfully deleted";
    } else {
        echo "failed to delete data";
    }
}

// Filtering logic
$min_roll = isset($_GET['min_roll']) ? (int)$_GET['min_roll'] : null;
$max_roll = isset($_GET['max_roll']) ? (int)$_GET['max_roll'] : null;

// Fetch filtered data
$show_data = $student->showData($min_roll, $max_roll);
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" type="image/x-icon" href="">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <title>Attendance Management System</title>

    <script>
        function dataUpdate(update_id) {
            document.form1.h_action.value = "update";
            document.form1.h_id.value = update_id;
            document.form1.submit();
        }

        function dataDelete(update_id) {
            document.form1.h_action.value = "delete";
            document.form1.h_id.value = update_id;
            document.form1.submit();
        }
    </script>
</head>

<body>
    <div class="main-container d-flex">
        <div class="sidebar_container">
            <ul class="side_menu">
                <i class="fa fa-arrow-right"></i>
                <li><a href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="student_managment.php"><i class="fa-solid fa-user-graduate"></i> Manage Students</a></li>
                <li><a href="attendence.php"><i class="fa-solid fa-clipboard-check"></i> Attendance</a></li>
                <li><a href="class_managment"><i class="fa-solid fa-chalkboard-teacher"></i> Class Management</a></li>
                <li><a href="#"><i class="fa-solid fa-user-tie"></i> Teachers</a></li>
                <i class="fa fa-arrow-right"></i>
            </ul>
        </div>

        <div class="content w-100">
            <div class="section_student_div text-center py-3">
                <h1>Attendance Management System</h1>
            </div>

            <section class="class_managment">
                <!-- Filtering Form -->
                <form method="get" action="">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="min_roll">Minimum Roll Number</label>
                                <input type="number" class="form-control" id="min_roll" name="min_roll" 
                                       value="<?php echo isset($_GET['min_roll']) ? $_GET['min_roll'] : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="max_roll">Maximum Roll Number</label>
                                <input type="number" class="form-control" id="max_roll" name="max_roll" 
                                       value="<?php echo isset($_GET['max_roll']) ? $_GET['max_roll'] : ''; ?>">
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Main Form for Update/Delete -->
                <form method="post" name="form1" enctype="multipart/form-data">
                    <input type="hidden" name="h_action">
                    <input type="hidden" name="h_id">
                    <div class="container">
                        <div class="row">
                            <?php
                            if (!empty($show_data)) {
                                foreach ($show_data as $data) {
                            ?>
                                    <div class="col-md-4">
                                        <div class="card mt-5 w-100"
                                            style="border:1px solid #ddd; box-shadow:3px 6px 7x 10px rgba(0,0,0,0.1);">
                                            <div class="card-body w-100">
                                                <img src="uploads/<?php echo $data['image']; ?>"
                                                    name="student_image_<?= $data['id'] ?>" alt='Image' class="img-fluid"
                                                    style="height:250px; object-fit:contain;">
                                                <br>
                                                <input type="file" name="student_image_<?= $data['id'] ?>" class="form-control w-50 ml-5 mt-5"
                                                    value="<?= $data['image']; ?>" placeholder="enter student image">
                                                <br>
                                                <label for="">Student Name</label>
                                                <input class="form-control" type="text" placeholder="student name"
                                                    name="student_name_<?= $data['id'] ?>" value="<?= $data['student_name'] ?>">

                                                <label for="">Student Section</label>
                                                <input class="form-control" type="text" placeholder="student section"
                                                    name="student_section_<?= $data['id'] ?>"
                                                    value="<?= $data['student_section'] ?>">

                                                <label for="">Student Roll</label>
                                                <input class="form-control" type="number" name="student_roll_<?= $data['id'] ?>"
                                                    value="<?= $data['student_roll'] ?>">

                                                <label for="">Student Class</label>
                                                <input class="form-control" type="number" name="student_grade_<?= $data['id'] ?>"
                                                    value="<?= $data['student_grade'] ?>">

                                                <label for="">Admission Date</label>
                                                <input class="form-control" type="date" name="student_date_<?= $data['id'] ?>"
                                                    value="<?= $data['date'] ?>">

                                                <div class="button_section mt-2">
                                                    <button class="btn btn-success" onclick="dataUpdate(<?= $data['id'] ?>)">
                                                        Update
                                                    </button>
                                                    <button class="btn btn-danger" onclick="dataDelete(<?= $data['id'] ?>)">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo "<div class='col-md-12'><p>No students found in the specified range.</p></div>";
                            }
                            ?>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
</body>

</html>