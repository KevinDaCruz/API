<?php

namespace App\Http\Controllers\API;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Books API', version: '1.0.0', description: 'API for books and auth')]
#[OA\Server(url: 'http://localhost:8000/api/v1')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Tag(name: 'Auth', description: 'Authentication endpoints')]
#[OA\Tag(name: 'Books', description: 'Book endpoints')]
#[OA\Schema(
    schema: 'Book',
    type: 'object',
    required: ['title', 'author', 'summary', 'isbn'],
    properties: [
        new OA\Property(property: 'title', type: 'string', example: '1984'),
        new OA\Property(property: 'author', type: 'string', example: 'GEORGE ORWELL'),
        new OA\Property(property: 'summary', type: 'string', example: 'Roman dystopique...'),
        new OA\Property(property: 'isbn', type: 'string', example: '9780451524935'),
        new OA\Property(property: '_links', type: 'object'),
    ]
)]
#[OA\Schema(
    schema: 'BookInput',
    type: 'object',
    required: ['title', 'author', 'summary', 'isbn'],
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Nouveau livre'),
        new OA\Property(property: 'author', type: 'string', example: 'Nouvel auteur'),
        new OA\Property(property: 'summary', type: 'string', example: 'Un resume assez long pour la validation.'),
        new OA\Property(property: 'isbn', type: 'string', example: '9781234567890'),
    ]
)]
#[OA\Schema(
    schema: 'AuthResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'token', type: 'string'),
        new OA\Property(property: 'user', type: 'object')
    ]
)]
#[OA\Schema(
    schema: 'ErrorResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'An error occurred'),
        new OA\Property(property: 'errors', type: 'object', nullable: true)
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            example: ['email' => ['The email field is required.'], 'title' => ['The title must be at least 3 characters.']]
        )
    ]
)]
class OpenApi {}
