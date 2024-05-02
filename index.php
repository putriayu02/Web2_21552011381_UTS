<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script>
        $(document).ready(function(){
            // Objek Book
            class Book {
                constructor(title, author, year) {
                    this.title = title;
                    this.author = author;
                    this.year = year;
                    this.status = 'available'; // Set status awal buku menjadi tersedia
                }
            }

            // Objek Library
            class Library {
                constructor() {
                    this.books = []; // Array untuk menyimpan daftar buku di perpustakaan
                    this.borrowedBooks = []; // Array untuk menyimpan buku yang sedang dipinjam
                }

                // Metode untuk menambahkan buku ke dalam perpustakaan
                addBook(book) {
                    this.books.push(book);
                    console.log(`Book "${book.title}" added to library.`);
                    this.printAvailableBooks();
                    this.updateBorrowedBooksList();
                }

                // Metode untuk meminjam buku dari perpustakaan
                borrowBook(title) {
                    for (let i = 0; i < this.books.length; i++) {
                        if (this.books[i].title === title && this.books[i].status === 'available') {
                            this.books[i].status = 'borrowed';
                            this.borrowedBooks.push(this.books.splice(i, 1)[0]);
                            console.log(`Book "${title}" borrowed.`);
                            this.printAvailableBooks();
                            this.updateBorrowedBooksList();
                            return true; // Kembalikan true jika buku berhasil dipinjam
                        }
                    }
                    console.log(`Book "${title}" not found or unavailable.`);
                    return false; // Kembalikan false jika buku tidak ditemukan atau tidak tersedia
                }

                // Metode untuk mengembalikan buku yang dipinjam ke perpustakaan
                returnBook(title) {
                    for (let i = 0; i < this.borrowedBooks.length; i++) {
                        if (this.borrowedBooks[i].title === title) {
                            this.borrowedBooks[i].status = 'available';
                            this.books.push(this.borrowedBooks.splice(i, 1)[0]);
                            console.log(`Book "${title}" returned.`);
                            this.printAvailableBooks();
                            this.updateBorrowedBooksList();
                            return true; // Kembalikan true jika buku berhasil dikembalikan
                        }
                    }
                    console.log(`Book "${title}" not found or not borrowed.`);
                    return false; // Kembalikan false jika buku tidak ditemukan atau tidak sedang dipinjam
                }

                // Metode untuk mencetak daftar buku yang tersedia di perpustakaan
                printAvailableBooks() {
                    $('#availableBooks').empty();
                    const availableBooksTitle = this.books.filter(book => book.status === 'available').map(book => `<li class="list-group-item">${book.title} by ${book.author} (${book.year})</li>`).join('');
                    if (availableBooksTitle !== '') {
                        $('#availableBooks').append('<h3>Available Books:</h3>');
                        $('#availableBooks').append(availableBooksTitle);
                    }
                }

                // Metode untuk memperbarui listbox buku yang dipinjam
                updateBorrowedBooksList() {
                    $('#borrowedBooksList').empty();
                    for (let book of this.borrowedBooks) {
                        $('#borrowedBooksList').append(`<option>${book.title}</option>`);
                    }
                }
            }

            // Inisialisasi perpustakaan
            const library = new Library();

            // Menambahkan beberapa buku ke perpustakaan
            library.addBook(new Book("A Tale of Two Cities", "A.N wilson", 1859));
            library.addBook(new Book("The Lord Of the Rings", "R.R.Tolkien", 1937));
            library.addBook(new Book("The Hobbit", "R.R.Tolkien", 1937));
            library.addBook(new Book("Dream Of the Red Chamber", "Cao Xueqin", 1754));
            library.addBook(new Book("And Then There Where None", "Agatha Christie", 1999));
            library.addBook(new Book("She: A History of Adventure", "H.Rider Haggard", 1887));
            library.addBook(new Book("The Little Prince", "Antoine de Saint", 1943));
            library.addBook(new Book("The Davinci Code", "Dan Brown", 2003));
            library.addBook(new Book("The Catcher in the Rye", "J.D. Salinger", 1951));
            library.addBook(new Book("The Alchemist", "Paulo Coelho", 1988));

            // Menampilkan buku yang tersedia saat halaman dimuat
            library.printAvailableBooks();

            // Menambahkan event listener untuk tombol Borrow Book
            $('#borrowBookBtn').click(function(){
                const title = $('#borrowBookTitle').val();
                library.borrowBook(title);
                $('#borrowBookTitle').val('');
            });

            // Menambahkan event listener untuk tombol Return Book
            $('#returnBookBtn').click(function(){
                const title = $('#returnBookTitle').val();
                library.returnBook(title);
                $('#returnBookTitle').val('');
            });

            // Menambahkan event listener untuk tombol Add Book
            $('#addBookBtn').click(function(){
                const title = $('#addBookTitle').val();
                const author = $('#addBookAuthor').val();
                const year = $('#addBookYear').val();
                library.addBook(new Book(title, author, year));
                $('#addBookTitle').val('');
                $('#addBookAuthor').val('');
                $('#addBookYear').val('');
            });

            // Menambahkan event listener untuk tombol Return Selected Book
            $('#returnSelectedBookBtn').click(function(){
                const selectedBook = $('#borrowedBooksList').val();
                if(selectedBook) {
                    library.returnBook(selectedBook);
                }
            });
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Library Management System</h1>

        <!-- Form untuk meminjam buku -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="borrowBookTitle" class="form-control" placeholder="Enter book title to borrow">
            </div>
            <div class="col-md-6">
                <button id="borrowBookBtn" class="btn btn-primary">Borrow Book</button>
            </div>
        </div>

        <!-- Form untuk mengembalikan buku -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="returnBookTitle" class="form-control" placeholder="Enter book title to return">
            </div>
            <div class="col-md-6">
                <button id="returnBookBtn" class="btn btn-primary">Return Book</button>
            </div>
        </div>

        <!-- Form untuk menambahkan buku -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="addBookTitle" class="form-control" placeholder="Enter book title">
            </div>
            <div class="col-md-4">
                <input type="text" id="addBookAuthor" class="form-control" placeholder="Enter author name">
            </div>
            <div class="col-md-2">
                <input type="number" id="addBookYear" class="form-control" placeholder="Enter year">
            </div>
            <div class="col-md-2">
                <button id="addBookBtn" class="btn btn-success">Add Book</button>
            </div>
        </div>

        <!-- Tampilan daftar buku yang tersedia -->
        <div class="row">
            <div class="col-md-6">
                <ul id="availableBooks" class="list-group"></ul>
            </div>
            <div class="col-md-6">
                <h3>Borrowed Books:</h3>
                <select id="borrowedBooksList" class="form-control" multiple></select>
                <button id="returnSelectedBookBtn" class="btn btn-primary mt-2">Return Selected Book</button>
            </div>
        </div>
    </div>
</body>
</html>
