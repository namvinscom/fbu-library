<?php

namespace App\Services;

use App\Http\Requests\BookRequest;
use App\Models\BookModel;
use Illuminate\Http\Request;

class BookManagementService
{
    public function getAllBooks()
    {
        // Fetch books from the database
        $books = BookModel::select(
            'id',
            'book_cover',
            'book_code',
            'book_name',
            'book_type',
            'author',
            'quantity',
            'broken',
            'description'
        )->orderBy('created_at', 'desc')->paginate(10);

        // Calculate availableBooks after fetching
        $books->each(function ($book) {
            $book->availableBooks = max(0, $book->quantity - $book->broken); // Calculate available books
        });

        return $books;
    }

    public function saveBookImage(Request $request)
    {
        if ($request->hasFile('book_cover')) {
            $file = $request->file('book_cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('image', $filename, 'public');
            return $filename;
        }
        return null;
    }

    public function createBook(BookRequest $request)
    {
        $book = new BookModel($request->only(['book_code', 'book_name', 'book_type', 'author', 'quantity', 'description', 'book_cover', 'broken']));
        $book->book_cover = $this->saveBookImage($request);
        return $book->save();
    }

    public function updateBook(BookRequest $request)
    {

        $book = BookModel::where('book_code', $request->book_code)->firstOrFail();
        $book->fill($request->only(['book_name', 'book_type', 'author', 'quantity', 'description', 'book_cover', 'broken']));

        if ($request->hasFile('book_cover')) {
            $book->book_cover = $this->saveBookImage($request);
        }

        return $book->save();
    }

    public function deleteBook($id)
    {
        return BookModel::where('book_code', $id)->delete();
    }

    public function searchBooks($query)
    {
        return BookModel::where('book_name', 'LIKE', "%{$query}%")
            ->orWhere('book_code', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->orWhere('book_type', 'LIKE', "%{$query}%")
            ->paginate(10)->appends(['query' => $query]);
    }
}
