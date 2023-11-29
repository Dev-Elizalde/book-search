<?php

// Function to make a POST request to the books/search endpoint
function searchBooks($title)
{
    $url = 'https://ejditq67mwuzeuwrlp5fs3egwu0yhkjz.lambda-url.us-east-2.on.aws/api/books/search';
    $data = ['title' => $title];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true, // Ignore errors to fetch response code manually
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Get the HTTP response code
    $http_response_header = $http_response_header ?? [];
    $response_code = isset($http_response_header[0]) ? explode(' ', $http_response_header[0])[1] : null;

    if ($response_code === '200') {
        return json_decode($result, true);
    } else {
        echo "\n";
        echo "Failed to fetch data from the API. Status Code: $response_code\n";
        return null;
    }
}

// Function to get author details by author ID
function getAuthorDetails($authorId)
{
    $url = 'https://ejditq67mwuzeuwrlp5fs3egwu0yhkjz.lambda-url.us-east-2.on.aws/api/authors/' . $authorId;
    $result = file_get_contents($url);

    // Get the HTTP response code
    $http_response_header = $http_response_header ?? [];
    $response_code = isset($http_response_header[0]) ? explode(' ', $http_response_header[0])[1] : null;

    if ($response_code === '200') {
        $authorData = json_decode($result, true);
        return $authorData['firstName'] . ' ' . ($authorData['middleInitial'] ?? '') . ' ' . ($authorData['lastName'] ?? '');
    } else {
        echo "\n";
        echo "Failed to fetch author details.: $response_code\n";
        echo "\n";
        return null;
    }
}

// Main loop to prompt user input and search books
while (true) {
    $title = readline("Enter book title to search (or type 'exit' to quit): ");

    if ($title === 'exit') {
        echo "Exiting the application. Goodbye!";
        break;
    }

    $bookData = searchBooks($title);

    if ($bookData === null) {
        echo "Failed to find the book.\n";
        echo "\n";
        continue;
    }

    if (empty($bookData['authors'])) {
        echo "No authors found for this book.\n";
        continue;
    }

    echo "\n";
    echo "Book Title: " . $bookData['title'] . "\n";
    echo "Description: " . $bookData['description'] . "\n";
    echo "Authors:\n";
    foreach ($bookData['authors'] as $authorId) {
        $authorName = getAuthorDetails($authorId);
        echo "- " . $authorName . "\n";
    }
    echo "\n";
}
?>