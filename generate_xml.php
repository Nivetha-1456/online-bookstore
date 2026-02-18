<?php
require 'includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

$books = $conn->query("SELECT title, author, price, stock FROM books");

$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

$root = $xml->createElement('bookstore');
$xml->appendChild($root);

while ($row = $books->fetch_assoc()) {
    $book = $xml->createElement('book');
    
    $title = $xml->createElement('title', htmlspecialchars($row['title']));
    $book->appendChild($title);
    
    $author = $xml->createElement('author', htmlspecialchars($row['author']));
    $book->appendChild($author);
    
    $price = $xml->createElement('price', number_format($row['price'], 2));
    $book->appendChild($price);
    
    $stock = $xml->createElement('stock', $row['stock']);
    $book->appendChild($stock);
    
    $root->appendChild($book);
}

echo $xml->saveXML();
$conn->close();
?>