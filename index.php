<?php
// Objek Book
class Book {
    public $title;
    public $author;
    public $year;
    public $status;

    public function __construct($title, $author, $year) {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->status = 'available'; // Set status awal buku menjadi tersedia
    }
}

// Objek Library
class Library {
    public $books = []; // Array untuk menyimpan daftar buku di perpustakaan
    public $borrowedBooks = []; // Array untuk menyimpan buku yang sedang dipinjam

    // Metode untuk menambahkan buku ke dalam perpustakaan
    public function addBook($book) {
        $this->books[] = $book;
    }

    // Metode untuk menambahkan buku yang dipinjam ke cookies
    private function updateBorrowedBooksCookies() {
        setcookie('borrowedBooks', serialize($this->borrowedBooks), time() + (86400 * 30), "/");
    }

    // Metode untuk meminjam buku dari perpustakaan
    public function borrowBook($title) {
        foreach ($this->books as $key => $book) {
            if ($book->title === $title && $book->status === 'available') {
                $book->status = 'borrowed';
                $this->borrowedBooks[] = $book;
                // Update cookies setiap kali ada perubahan dalam peminjaman
                $this->updateCookies();
                $this->updateBorrowedBooksCookies(); // Tambahkan ini untuk memperbarui cookies untuk buku yang dipinjam
                return true; // Kembalikan true jika buku berhasil dipinjam
            }
        }
        return false; // Kembalikan false jika buku tidak ditemukan atau tidak tersedia
    }

    // Metode untuk mengembalikan buku yang dipinjam ke perpustakaan
    public function returnBook($title) {
        foreach ($this->borrowedBooks as $key => $book) {
            if ($book->title === $title) {
                $book->status = 'available';
                $this->books[] = $book;
                unset($this->borrowedBooks[$key]);
                // Update cookies setiap kali ada perubahan dalam pengembalian
                $this->updateCookies();
                $this->updateBorrowedBooksCookies(); // Tambahkan ini untuk memperbarui cookies untuk buku yang dipinjam
                return true; // Kembalikan true jika buku berhasil dikembalikan
            }
        }
        return false; // Kembalikan false jika buku tidak ditemukan atau tidak sedang dipinjam
    }

    // Metode untuk mengupdate cookies
    private function updateCookies() {
        // Simpan daftar buku yang tersedia dalam cookies
        setcookie('availableBooks', serialize($this->books), time() + (86400 * 30), "/");
    }
}

// Inisialisasi perpustakaan
$library = new Library();

// Cek apakah ada cookies untuk daftar buku yang tersedia
if (isset($_COOKIE['availableBooks'])) {
    $library->books = unserialize($_COOKIE['availableBooks']);
}

// Cek apakah ada cookies untuk daftar buku yang dipinjam
if (isset($_COOKIE['borrowedBooks'])) {
    $library->borrowedBooks = unserialize($_COOKIE['borrowedBooks']);
}

// Tambahkan beberapa buku ke perpustakaan
$library->addBook(new Book("The Great Gatsby", "F. Scott Fitzgerald", 1925));
$library->addBook(new Book("To Kill a Mockingbird", "Harper Lee", 1960));
$library->addBook(new Book("1984", "George Orwell", 1949));

// Event handler untuk tombol "Borrow Book"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrowBookBtn'])) {
    $bookTitle = $_POST['borrowBookTitle'];
    $library->borrowBook($bookTitle);
}

// Event handler untuk tombol "Return Book"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['returnBookBtn'])) {
    $bookTitle = $_POST['returnBookTitle'];
    $library->returnBook($bookTitle);
}

// Event handler untuk tombol "Add Book"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addBookBtn'])) {
    $bookTitle = $_POST['addBookTitle'];
    $bookAuthor = $_POST['addBookAuthor'];
    $bookYear = $_POST['addBookYear'];
    $library->addBook(new Book($bookTitle, $bookAuthor, $bookYear));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Library Management System</h1>

        <!-- Form untuk meminjam buku -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="borrowBookTitle" class="form-control" placeholder="Enter book title to borrow">
            </div>
            <div class="col-md-6">
                <button type="submit" name="borrowBookBtn" class="btn btn-primary">Borrow Book</button>
                </form>
            </div>
        </div>

        <!-- Form untuk mengembalikan buku -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="returnBookTitle" class="form-control" placeholder="Enter book title to return">
            </div>
            <div class="col-md-6">
                <button type="submit" name="returnBookBtn" class="btn btn-primary">Return Book</button>
                </form>
            </div>
        </div>

        <!-- Form untuk menambahkan buku -->
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="addBookTitle" class="form-control" placeholder="Enter book title">
            </div>
            <div class="col-md-4">
                <input type="text" name="addBookAuthor" class="form-control" placeholder="Enter author name">
            </div>
            <div class="col-md-2">
                <input type="number" name="addBookYear" class="form-control" placeholder="Enter year">
            </div>
            <div class="col-md-2">
                <button type="submit" name="addBookBtn" class="btn btn-success">Add Book</button>
                </form>
            </div>
        </div>

        <!-- Tampilan daftar buku yang tersedia -->

        <div class="row">
            <div class="col-md-6">
                <ul id="availableBooks" class="list-group">
                    <?php
                    // PHP code to display available books
                    foreach ($library->books as $book) {
                        if ($book->status === 'available') {
                            echo '<li class="list-group-item">' . $book->title . ' by ' . $book->author . ' (' . $book->year . ')</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Borrowed Books:</h3>
                <select id="borrowedBooksList" class="form-control" multiple>
                    <?php
                    // PHP code to display borrowed books
                    foreach ($library->borrowedBooks as $book) {
                        echo '<option>' . $book->title . '</option>';
                    }
                    ?>
                </select>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <button type="submit" name="returnSelectedBookBtn" class="btn btn-primary mt-2">Return Selected Book</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
