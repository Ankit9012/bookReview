<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when(
            $title,
            fn ($query, $title) => $query->title($title)
        );

        /** match is working same as the switch case but it need to return something, so default case is required */

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'poplular_last_6_month' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6_month' => $books->highestRatedLast6Months(),
            default => $books->latest()
        };


        $books = $books->get();

        return view('books.index', [
            'books' => $books,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {

        /**
         * Create arrow funciton
         */
        return view('books.show', [
            'book' => $book->load([
                'reviews' => fn (Builder $query) => $query->latest()
            ])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
