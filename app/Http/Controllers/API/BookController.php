<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      

        $data = Book::latest()->with(['user' => function ($query) {
            $query->select('id','name');
        }])->get();
       
        return response()->json([BookResource::collection($data), 'Books fetched.'],201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'desc' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'vendor' => $request->vendor,
            'desc' => $request->desc
         ]);
        
        return response()->json(['Book created successfully.', new BookResource($book)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        if (is_null($book)) {
            return response()->json('Data not found', 404); 
        }
        return response()->json([new BookResource($book)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'desc' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $book->title = $request->title;
        $book->author = $request->author;
        $book->vendor = $request->vendor;
        $book->desc = $request->desc;
        $book->save();
        
        return response()->json(['Book updated successfully.', new BookResource($book)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json('Book deleted successfully');
    }

    public function userassign(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'book_id' => 'required',
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }
        $book = Book::find($request->book_id);

        if($book) {
            $book->user_id = $request->user_id;
            $book->save();
        }
        
        return response()->json(['Book updated successfully.', new BookResource($book)]);
    }
}