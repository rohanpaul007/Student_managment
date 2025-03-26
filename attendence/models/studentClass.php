<?php

class Students extends Database {

    public function insertData($name, $student_section, $roll, $grade, $image, $date) {
        try {
            $sql = $this->conn->prepare("INSERT INTO student_data 
                (student_name, student_section, student_roll, student_grade, image, date) 
                VALUES (:name, :section, :roll, :grade, :image, :date)");
            
            $sql->bindParam(":name", $name, PDO::PARAM_STR);
            $sql->bindParam(":section", $student_section, PDO::PARAM_STR);
            $sql->bindParam(":roll", $roll, PDO::PARAM_INT);
            $sql->bindParam(":grade", $grade, PDO::PARAM_STR);
            $sql->bindParam(":image", $image, PDO::PARAM_STR);
            $sql->bindParam(":date", $date, PDO::PARAM_STR);
            
            return $sql->execute();
            
        } catch (PDOException $e) {
            error_log("Insert student error: " . $e->getMessage());
            return false;
        }
    }

    public function showData($min_roll = null, $max_roll = null) {
        try {
            $sql = "SELECT * FROM student_data";
        
            if ($min_roll !== null && $max_roll !== null) {
                $sql .= " WHERE student_roll BETWEEN :min_roll AND :max_roll";
            }
        
            $stmt = $this->conn->prepare($sql);
        
            if ($min_roll !== null && $max_roll !== null) {
                $stmt->bindParam(':min_roll', $min_roll, PDO::PARAM_INT);
                $stmt->bindParam(':max_roll', $max_roll, PDO::PARAM_INT);
            }
        
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Show data error: " . $e->getMessage());
            return [];
        }
    }

    public function UpdateData($name_update, $student_section_update, $roll_update, $grade_update, $image_update, $date_update, $student_id) {
        try {
            $sql = $this->conn->prepare("UPDATE student_data SET 
                student_name = :name, 
                student_section = :section, 
                student_roll = :roll, 
                student_grade = :grade, 
                image = :image, 
                date = :date 
                WHERE id = :student_id");
                
            $sql->bindParam(":name", $name_update, PDO::PARAM_STR);
            $sql->bindParam(":section", $student_section_update, PDO::PARAM_STR);
            $sql->bindParam(":roll", $roll_update, PDO::PARAM_INT);
            $sql->bindParam(":grade", $grade_update, PDO::PARAM_STR);
            $sql->bindParam(":image", $image_update, PDO::PARAM_STR);
            $sql->bindParam(":date", $date_update, PDO::PARAM_STR);
            $sql->bindParam(":student_id", $student_id, PDO::PARAM_INT);
            
            return $sql->execute();
            
        } catch (PDOException $e) {
            error_log("Update student error: " . $e->getMessage());
            return false;
        }
    }

    public function getStudentById($id) {
        try {
            $sql = "SELECT * FROM student_data WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Get student by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    public function Deletedata($delete_id) {
        try {
            $sql = $this->conn->prepare("DELETE FROM student_data WHERE id = ?");
            return $sql->execute([$delete_id]);
            
        } catch (PDOException $e) {
            error_log("Delete student error: " . $e->getMessage());
            return false;
        }
    }

    public function updateAttendance($student_id, $status, $date) {
        try {
            // Validate status
            if (!in_array($status, ['Present', 'Absent'])) {
                throw new Exception("Invalid attendance status");
            }
            
            // Check if record exists
            $checkSql = "SELECT 1 FROM attendance WHERE student_id = ? AND date = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->execute([$student_id, $date]);
            $exists = $checkStmt->fetch();
            
            if ($exists) {
                $sql = "UPDATE attendance SET status = ? WHERE student_id = ? AND date = ?";
                $params = [$status, $student_id, $date];
            } else {
                $sql = "INSERT INTO attendance (student_id, status, date) VALUES (?, ?, ?)";
                $params = [$student_id, $status, $date];
            }
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Attendance update error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Attendance validation error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAttendanceReport($date) {
        try {
            // Modified query to properly join with your table structure
            $sql = "SELECT 
                    s.id,
                    s.student_name,
                    s.student_roll,
                    s.student_grade,
                    s.student_section,
                    s.image,
                    COALESCE(a.status, 'Not Marked') as status 
                FROM student_data s
                LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
                ORDER BY s.student_roll";
                
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$date]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug output
            error_log("Attendance Query: ".$sql);
            error_log("Using Date: ".$date);
            error_log("Results: ".print_r($results, true));
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("Attendance report error: " . $e->getMessage());
            return [];
        }
    }



    // public function getAttendanceReport($date) {
    //     try {
    //         $sql = "SELECT s.*, a.status 
    //                 FROM student_data s
    //                 LEFT JOIN attendence a ON s.student_roll = a.student_id 
    //                 AND a.date = :date";
            
    //         $stmt = $this->conn->prepare($sql); // $this->db এর পরিবর্তে $this->conn
    //         $stmt->bindParam(':date', $date);
    //         $stmt->execute();
            
    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
    //         error_log("Attendance Query: ".$sql);
    //         error_log("Date Parameter: ".$date);
    //         error_log("Result Count: ".count($result));
            
    //         return $result;
    //     } catch (PDOException $e) {
    //         error_log("Database Error: ".$e->getMessage());
    //         return [];
    //     }
    // }
}