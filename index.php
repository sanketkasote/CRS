<html>
<body>
    <form action="index.php" method="post">
    Course: <select name="courseid">
    <?php
        // Populate the couses selection box.
        $coursefilename = "courses.txt";
        $coursefile = fopen($coursefilename, "r") or die("Unable to open file!");

        while(!feof($coursefile)) {
            list($coursename, $code, $max) = explode("::", rtrim(fgets($coursefile)));
            echo "<option value=\"" . $code . "\">" . $coursename . "</option>";
        }

        fclose($coursefile);
    ?>
    </select><br>
    Student Name: <input type="text" name="name"><br>
    Student Number: <input type="text" name="no"><br>
    <input type="submit" value="Enroll">
    </form>
    <?php
        // store the students
        $students = array();
        // store the courses
        $courses = array();
        // store the enrollment data
        $enrollments = array();

        // populate the student array
        $studentfilename = "students.txt";
        $studentfile = fopen($studentfilename, "r") or die("Unable to open file!");

        // fields are: student name, student id
        // fields seperated by double colons.
        while(!feof($studentfile)) {
            list($sname, $sid) = explode("::", rtrim(fgets($studentfile)));
            $students[$sid] = $sname;
        }

        fclose($studentfile);

        // populate the course array
        $coursefilename = "courses.txt";
        $coursefile = fopen($coursefilename, "r") or die("Unable to open file!");
        
        // fields are: course name, course code, and the maximum enrollment
        // fields seperated by double colons.
        while(!feof($coursefile)) {
            list($coursename, $code, $max) = explode("::", rtrim(fgets($coursefile)));
            $courses[$code] = ["name" => $coursename, "max" => $max];
        }

        fclose($coursefile);

        // populate the enrollment array.
        $enrollmentfilename = "enrollments.txt";
        $enrollmentfile = fopen($enrollmentfilename, "r") or die("Unable to open file!");

        // fields are: course code, student id
        // fields seperated by double colons.
        while(!feof($enrollmentfile)) {
            list($course, $student) = array_pad(explode("::", rtrim(fgets($enrollmentfile)), 2), 2, null);
            array_push($enrollments, [$course, $student]);
        }

        fclose($enrollmentfile);

        // check if data is properly submitted.
        if (isset($_POST["name"]) && isset($_POST["no"]) && isset($_POST["courseid"])) {
            $studentname = htmlspecialchars($_POST["name"]);
            $studentno = htmlspecialchars($_POST["no"]);
            $courseid = htmlspecialchars($_POST["courseid"]);

            // check if the student already exists
            if (array_key_exists($studentno, $students) && in_array($studentname, $students) && $students[$studentno] == $studentname) {
                // check if the student is not already enrolled in the course.
                if (!in_array([$courseid, $studentno], $enrollments)) {
                    // stores the amount of students in the course.
                    $count = 0;

                    // count the stuents in the course.
                    foreach($enrollments as &$v)
                        if ($v[0] == $courseid)
                            $count++;
                    
                    // check if the course is full
                    if ($count < $courses[$courseid]["max"]) {
                        // enroll if all checks are passed.
                        file_put_contents($enrollmentfilename, $courseid . "::" . $studentno . "\n", FILE_APPEND | LOCK_EX);
                        echo "<p>Enrollment Sucess!</p>";
                    } else {
                        echo "<p>Enrollment Failed! Course Full.</p>";
                    }
                } else {
                    echo "<p>Enrollment Failed! Already Enrolled.</p>";
                }
            } else {
                echo "<p>Student Not Exists</p>";
            }
        }
    ?>
</body>
</html>