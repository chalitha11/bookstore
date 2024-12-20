<?php
@include 'header.php';
@include 'configDatabase.php';

    // Fetching All books from the database
    $queryAllBook = "SELECT * FROM book ORDER BY bookID DESC";
    $resultAllBook = $conn->query($queryAllBook);

    $books = [];
    if ($resultAllBook->num_rows > 0) {
        while ($rowNewArrival = $resultAllBook->fetch_assoc()) {
            $books[] = $rowNewArrival;
        }
    }

// Function to sanitize user input
function sanitizeInput($data)
{
    global $conn;  // Access the global connection variable
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Inserting or updating a book into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'Add') {
        // Inserting a new book
        $bookID = sanitizeInput($_POST["bookID"]);
        $name = sanitizeInput($_POST["bname"]);
        $author = sanitizeInput($_POST["bauthor"]);
        $price = sanitizeInput($_POST["bprice"]);
        $type = sanitizeInput($_POST["btype"]);

        // Image file upload handling
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'images/' . $type . '/';

        // Generate a unique filename with datetime prefix
        $datetime_prefix = date('YmdHis');
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_image_name = $datetime_prefix . '.' . $image_extension;
        $image_path = $image_folder . $new_image_name;

        // Move the uploaded file to the destination folder
        if (move_uploaded_file($image_tmp_name, $image_path)) {
            $sql = "INSERT INTO book (bookID, bname, bauthor, bprice, btype, bimage) VALUES ('$bookID', '$name', '$author', '$price', '$type', '$image_path')";
            if ($conn->query($sql) === TRUE) {
                header("Location: index.php");
            } else {
                echo '<script>';
                echo 'alert("Book insertion failed!");';
                echo '</script>';
            } 
        } else {
            echo '<script>';
            echo 'alert("File upload failed!");';
            echo '</script>';
        }

    } elseif (isset($_POST['action']) && $_POST['action'] == 'Save') {
        // Updating an existing book
        $bookID = sanitizeInput($_POST["bookID"]);
        $name = sanitizeInput($_POST["bname"]);
        $author = sanitizeInput($_POST["bauthor"]);
        $price = sanitizeInput($_POST["bprice"]);
        $type = sanitizeInput($_POST["btype"]);
        $imageURL = sanitizeInput($_POST["imageURL"]);
        
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'images/' . $type . '/';

        $datetime_prefix = date('YmdHis');
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_image_name = $datetime_prefix . '.' . $image_extension;
        $image_path = $image_folder . $new_image_name;
       
        if($image_name == ''){
            $image_path = $imageURL;

            $sql = "UPDATE book SET bookID= '$bookID', bname='$name', bauthor='$author', bprice='$price', btype='$type',bimage='$image_path' WHERE bookID=$bookID";
                if ($conn->query($sql) === TRUE) {
                    // Fetching Books from the database
                    $queryAllBook = "SELECT * FROM book ORDER BY bookID DESC";
                    $resultAllBook = $conn->query($queryAllBook);
    
                    $books = [];
                    if ($resultAllBook->num_rows > 0) {
                        while ($rowNewArrival = $resultAllBook->fetch_assoc()) {
                            $books[] = $rowNewArrival;
                        }
                    }
                } else {
                    echo '<script>';
                    echo 'alert("Book updated fail!");';
                    echo '</script>';
                }
        }
        else{
            if (move_uploaded_file($image_tmp_name, $image_path)){ 
                $sql = "UPDATE book SET bookID = '$bookID', bname='$name', bauthor='$author', bprice='$price', btype='$type',bimage='$image_path' WHERE bookID=$bookID";
                if ($conn->query($sql) === TRUE) {
                    // Fetching Books from the database
                    $queryAllBook = "SELECT * FROM book ORDER BY bookID DESC";
                    $resultAllBook = $conn->query($queryAllBook);
    
                    $books = [];
                    if ($resultAllBook->num_rows > 0) {
                        while ($rowNewArrival = $resultAllBook->fetch_assoc()) {
                            $books[] = $rowNewArrival;
                        }
                    }
                } else {
                    echo '<script>';
                    echo 'alert("Book updated fail!");';
                    echo '</script>';
                }
            }
        }
        
        
        
    }
}
// Check if the 'delete' parameter is in the URL
if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_query = mysqli_query($conn, "DELETE FROM book WHERE bookID = '$delete_id' ") or die('query failed');
    if($delete_query){
       header('location:AdminPanel.php');
       $message[] = 'Product has been deleted';
    }else{
       header('location:AdminPanel.php');
       $message[] = 'Product could not be deleted';
    };
 }
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="css/AdminPanel.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

</head>

<body>
    <h2>Insert Book</h2>
    <br><br><br>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="card bg-dark text-light">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="card-header text-center">
                        <h1>Add Books</h1>
                    </div>
                    <div class="body container">
                        <div class="row">
                            <div class="col-md-3"> </div>
                            <div class="col-md-6">
                                <input type="hidden" name="userType" id="" value="Admin">
                                <input type="hidden" name="userID" id="bookID" value="3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="title">ISBN No:</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="bookID" id="bookID" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="title">Book Name:</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="bname" id="bname" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="author">Author:</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="bauthor" id="bauthor" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="published_year">Price:</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" name="bprice" id="bprice" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="dropdown">Book Types:</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="dropdown">
                                            <select id="btype" name="btype" id="btype">
                                                <option value="Novels">Novels</option>
                                                <option value="ShortStory">Short story</option>
                                                <option value="Fantasy">Fantasy</option>
                                               
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="bookImage">Book Image:</label>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="text-center image-container">
                                    <img src="" id="imageview" class="img-thumbnail" alt="...">
                                    </div>
                                        <input type="hidden" id="imageURL" name="imageURL"/>
                                        <input type="file" name="image" id="image" accept="image/png, image/jpg, image/jpeg" onchange="previewImage(this)">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                    <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2" id='actionAdd'>
                            <input type="submit" name="action" value="Add" class="btn btn-success">
                        </div>
                        <div class="col-md-2" style="display:none" id='actionSave'>
                            <input type="submit" name="action" value="Save" class="btn btn-primary">
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <br><br>
    <div class="row">
        <div class="container">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Book Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Author</th>
                        <th scope="col">Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //display all the books
                    foreach ($books as $book) {
                        echo "<tr>";
                        echo "<td>" . $book['bookID'] . "</td>";
                        echo "<td>" . $book['bname'] . "</td>";
                        
                        // Check if btype is 'Novel' and display 'Novel' accordingly
                        echo "<td>";
                        if ($book['btype'] == 'Novels') {
                            echo "Novels";
                        } else if ($book['btype'] == 'ShortStory') {
                            echo "Short Story";
                        } else if ($book['btype'] == 'Thriller') {
                            echo "Thriller";
                        } else if ($book['btype'] == 'Fantasy') {
                            echo "Fantasy";
                        }
                        else if ($book['btype'] == 'Fiction') {
                            echo "Fiction";
                        }
                        echo "</td>";

                        echo "<td>" . $book['bauthor'] . "</td>";
                        echo "<td>" . $book['bprice'] . ".00/=</td>";
                        echo "<td>
                                <button class='btn btn-warning' onclick='editBook(" . json_encode($book) . ")'>Edit</button>
                                <a class='btn btn-danger' onclick='return confirm('Are you sure you want to delete this?');' href='AdminPanel.php?delete=".$book['bookID']. "'>
                                <i class='fas fa-trash'></i> Delete
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function editBook(book) {
            // Populate the form fields with the existing values
           
            document.getElementById('bookID').value = book.bookID;
            document.getElementById('bname').value = book.bname;
            document.getElementById('bauthor').value = book.bauthor;
            document.getElementById('bprice').value = book.bprice;
            document.getElementById('btype').value = book.btype;
             // Update the image source
            var imageElement = document.getElementById('imageview');
            imageElement.src = book.bimage;

            document.getElementById('imageURL').value = book.bimage;
            //Add and Save button changing
            var addBtn = document.getElementById('actionAdd');
            var saveBtn = document.getElementById('actionSave');

            addBtn.style.display = 'none';
            saveBtn.style.display = 'block';
        }

        function previewImage(input) {   
            var imageContainer = $(".image-container img")[0];
            var fileInput = input.files[0];
        
            if (fileInput) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    imageContainer.src = e.target.result;
                };
                reader.readAsDataURL(fileInput);
            }
        }
    </script>
</body>

</html>
