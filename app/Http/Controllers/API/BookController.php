<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: '/books',
        summary: 'List books (paginated)',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'Accept', in: 'header', required: true, schema: new OA\Schema(type: 'string', enum: ['application/json'], example: 'application/json')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Book')),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object')
                    ],
                    example: [
                        'data' => [[
                            'title' => '1984',
                            'author' => 'GEORGE ORWELL',
                            'summary' => 'Roman dystopique...',
                            'isbn' => '9780451524935',
                            '_links' => ['self' => ['href' => '/api/v1/books/1']]
                        ]],
                        'links' => ['first' => '/api/v1/books?page=1', 'last' => '/api/v1/books?page=5'],
                        'meta' => ['current_page' => 1, 'per_page' => 2, 'total' => 10]
                    ]
                )
            )
        ]
    )]
    public function index()
    {
        return BookResource::collection(Book::paginate(2));
    }

    #[OA\Post(
        path: '/books',
        summary: 'Create a book',
        tags: ['Books'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'Accept', in: 'header', required: true, schema: new OA\Schema(type: 'string', enum: ['application/json'], example: 'application/json')),
            new OA\Parameter(name: 'Authorization', in: 'header', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/Book', example: ['title' => 'Nouveau livre', 'author' => 'Nouvel auteur', 'summary' => 'Un resume assez long pour la validation.', 'isbn' => '9781234567890'])),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Unauthorized'])),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'))
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'author' => ['required', 'string', 'min:3', 'max:100'],
            'summary' => ['required', 'string', 'min:10', 'max:500'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
        ]);

        $book = Book::create($validated);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/books/{id}',
        summary: 'Get one book',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'Accept', in: 'header', required: true, schema: new OA\Schema(type: 'string', enum: ['application/json'], example: 'application/json')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Book', example: ['title' => '1984', 'author' => 'GEORGE ORWELL', 'summary' => 'Roman dystopique...', 'isbn' => '9780451524935'])),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Book not found']))
        ]
    )]
    public function show(Book $book)
    {
        $cached = Cache::remember('book:' . $book->id, 60 * 60, function () use ($book): array {
            return (new BookResource($book))->resolve();
        });

        return response()->json($cached);
    }

    #[OA\Put(
        path: '/books/{id}',
        summary: 'Update a book',
        tags: ['Books'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'Accept', in: 'header', required: true, schema: new OA\Schema(type: 'string', enum: ['application/json'], example: 'application/json')),
            new OA\Parameter(name: 'Authorization', in: 'header', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Book')),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Unauthorized'])),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Book not found'])),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'))
        ]
    )]
    #[OA\Patch(
        path: '/books/{id}',
        summary: 'Update a book (partial)',
        tags: ['Books'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'Accept', in: 'header', required: true, schema: new OA\Schema(type: 'string', enum: ['application/json'], example: 'application/json')),
            new OA\Parameter(name: 'Authorization', in: 'header', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Book')),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Unauthorized'])),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Book not found'])),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError', example: ['message' => 'The given data was invalid.', 'errors' => ['title' => ['The title must be at least 3 characters.']]]))
        ]
    )]
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'author' => ['required', 'string', 'min:3', 'max:100'],
            'summary' => ['required', 'string', 'min:10', 'max:500'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn,' . $book->id],
        ]);

        $book->update($validated);
        Cache::forget('book:' . $book->id);

        return new BookResource($book);
    }

    #[OA\Delete(
        path: '/books/{id}',
        summary: 'Delete a book',
        tags: ['Books'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'Accept', in: 'header', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'Authorization', in: 'header', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'No content'),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Unauthorized'])),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse', example: ['message' => 'Book not found']))
        ]
    )]
    public function destroy(Book $book)
    {
        $book->delete();
        Cache::forget('book:' . $book->id);

        return response()->noContent();
    }
}
