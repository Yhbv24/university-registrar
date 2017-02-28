<?php
    class Student
    {
        private $name;
        private $enrollment_date;
        private $id;

        function __construct($name, $enrollment_date, $id = null)
        {
            $this->name = $name;
            $this->enrollment_date = $enrollment_date;
            $this->id = $id;
        }

        function getId()
        {
            return $this->id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function getEnrollmentDate()
        {
            return $this->enrollment_date;
        }

        function setEnrollmentDate($new_enrollment_date)
        {
            $this->enrollment_date = $new_enrollment_date;
        }

        function getCourse()
        {
            $returned_courses = $GLOBALS['DB']->query("SELECT courses.* FROM students JOIN students_courses ON (students_courses.student_id = students.id) JOIN courses ON (students_courses.course_id = courses.id) WHERE students.id = {$this->getId()};");
            $courses = array();

            foreach($returned_courses as $course) {
                $name = $course['name'];
                $id = $course['id'];
                $new_course = new Course($name, $id);
                array_push($courses, $new_course);
            }

            return $courses;
        }

        // function getCourse()
        // {
        //     $query = $GLOBALS['DB']->query("SELECT course_id FROM students_courses WHERE student_id = {$this->getId()};");
        //     $course_ids = $query->fetchAll(PDO::FETCH_ASSOC);
        //     $courses = array();
        //
        //     foreach($course_ids as $id) {
        //         $course_id = $id['course_id'];
        //         $result = $GLOBALS['DB']->query("SELECT * FROM courses WHERE id = {$course_id};");
        //         $returned_course = $result->fetchAll(PDO::FETCH_ASSOC);
        //
        //         $name = $returned_course[0]['name'];
        //         $id = $returned_course[0]['id'];
        //         $new_course = new Course($name, $id);
        //         array_push($courses, $new_course);
        //     }
        //
        //     return $courses;
        // }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO students (name, enrollment_date) VALUES ('{$this->getName()}', '{$this->getEnrollmentDate()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_students = $GLOBALS['DB']->query("SELECT * FROM students;");
            $students = array();

            foreach($returned_students as $student) {
                $name = $student['name'];
                $enrollment_date = $student['enrollment_date'];
                $id = $student['id'];
                $new_student = new Student($name, $enrollment_date, $id);
                array_push($students, $new_student);
            }

            return $students;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM students;");
            $GLOBALS['DB']->exec("DELETE FROM students_courses;");
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM students WHERE id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM students_courses WHERE student_id = {$this->getId()};");
        }

        static function find($search_id)
        {
            $found_student = null;
            $students = Student::getAll();

            foreach($students as $student) {
                $student_id = $student->getId();
                if ($student_id == $search_id) {
                    $found_student = $student;
                }
            }

            return $found_student;
        }

        function addCourse($course)
        {
            $GLOBALS['DB']->exec("INSERT INTO students_courses (student_id, course_id) VALUES ({$this->getId()}, {$course->getId()});");
        }
    }

?>
