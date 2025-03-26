

<?php

include_once "models/Database.php";
include_once "models/studentClass.php";

$student=new Students();

if(isset($_POST['btn_submit'])){
$student_name=$_POST['student_name'];
$student_section_id=$_POST['student_section'];
$student_roll=$_POST['student_roll'];
$student_grade=$_POST['student_grade'];

$student_image = $_FILES['image']['name'];
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);
move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
$student_date=$_POST['date'];



$output=$student->insertData($student_name,$student_section_id,$student_roll,$student_grade,$student_image,$student_date);

if($output){
    echo "successfully inserted data";
} else {
    echo "failed to insert data";
}







}





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
  <title>Attendence Managment System</title>
</head>

<body>
    <div class="main-container d-flex">
        <div class="sidebar_container">
            <ul class="side_menu">
                <li><a href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="student_managment.php"><i class="fa-solid fa-user-graduate"></i> Manage Students</a></li>
                <li><a href="attendence.php"><i class="fa-solid fa-clipboard-check"></i> Attendance</a></li>
                <li><a href="class_managment.php"><i class="fa-solid fa-chalkboard-teacher"></i> Class Management</a></li>
                <li><a href="#"><i class="fa-solid fa-user-tie"></i> Teachers</a></li>
            </ul>
        </div>
    
        <div class="content w-100">
            <div class="section_student_div text-center py-3">
                <h1>Attendance Management System</h1>
            </div>
           <div class="form_container">
           <form  method="POST" enctype="multipart/form-data">
                <input type="text" class="form-control w-50 ml-5 mt-5" name="student_name" placeholder="enter student's full  name">
                 <select name="student_section"  class="form-control w-50 ml-5 mt-5">
                    <option value="">Select  Section</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>


                 </select>
                <input type="number" name="student_roll" class="form-control w-50 ml-5 mt-5" placeholder="enter student  roll number">


                <select name="student_grade"  class="form-control w-50 ml-5 mt-5">
                    <option value="">Select  grade</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>






                 </select>

                <input type="file" name="image" class="form-control w-50 ml-5 mt-5" placeholder="enter student image">
               <label class="enroll mt-5 ml-5">enrollment date</label>
                <input type="date" class="form-control w-50 ml-5 mt-1" name="date" placeholder="enter student enrollment date">


                <button class="btn btn-info ml-5 mt-2" name="btn_submit">submit</button>
 
            </form>
           </div>
            
        </div>
       
    </div>
    
 












<script src="https://kit.fontawesome.com/YOUR-KIT-ID.js" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
    crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    -->
</body>

</html>